<?php namespace Backend\FormWidgets;

use Lang;
use ApplicationException;
use Backend\Classes\FormWidgetBase;
use Illuminate\Database\Eloquent\Collection;

/**
 * RecordFinder renders a record finder field
 *
 *    user:
 *        label: User
 *        type: recordfinder
 *        list: ~/plugins/rainlab/user/models/user/columns.yaml
 *        recordsPerPage: 10
 *        title: Find Record
 *        keyFrom: id
 *        nameFrom: name
 *        descriptionFrom: email
 *        conditions: email = "bob@example.com"
 *        scope: whereActive
 *        searchMode: all
 *        searchScope: searchUsers
 *        useRelation: false
 *        modelClass: RainLab\User\Models\User
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class RecordFinder extends FormWidgetBase
{
    use \Backend\Traits\FormModelWidget;

    //
    // Configurable Properties
    //

    /**
     * @var string keyFrom is the field name to use for key
     */
    public $keyFrom;

    /**
     * @var string nameFrom is the relation column to display for the name
     */
    public $nameFrom = 'name';

    /**
     * @var string descriptionFrom is the relation column to display for the description
     */
    public $descriptionFrom;

    /**
     * @var string title text to display for the title of the popup list form
     */
    public $title = 'backend::lang.recordfinder.find_record';

    /**
     * @var int recordsPerPage is the maximum rows to display for each page
     */
    public $recordsPerPage = 10;

    /**
     * @var string scope uses a custom scope method for the list query.
     */
    public $scope;

    /**
     * @var string conditions filters the relation using a raw where query statement.
     */
    public $conditions;

    /**
     * @var string searchMode if searching the records, specifies a policy to use.
     * - all: result must contain all words
     * - any: result can contain any word
     * - exact: result must contain the exact phrase
     */
    public $searchMode;

    /**
     * @var string searchScope uses a custom scope method for performing searches.
     */
    public $searchScope;

    /**
     * @var boolean useRelation flag for using the name of the field as a relation
     * name to interact with directly on the parent model. Default: true. Disable
     * to return just the selected model's ID
     */
    public $useRelation = true;

    /**
     * @var string modelClass of the model to use for listing records when
     * useRelation = false
     */
    public $modelClass;

    //
    // Object Properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'recordfinder';

    /**
     * @var Model relationModel
     */
    public $relationModel;

    /**
     * @var string|int relationKeyValue
     */
    protected $relationKeyValue = -1;

    /**
     * @var \Backend\Classes\WidgetBase listWidget reference to the widget used for
     * viewing (list or form).
     */
    protected $listWidget;

    /**
     * @var \Backend\Classes\WidgetBase searchWidget reference to the widget used for
     * searching.
     */
    protected $searchWidget;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'title',
            'keyFrom',
            'nameFrom',
            'descriptionFrom',
            'scope',
            'conditions',
            'searchMode',
            'searchScope',
            'recordsPerPage',
            'useRelation',
            'modelClass',
        ]);

        if (!$this->useRelation && !class_exists($this->modelClass)) {
            throw new ApplicationException(Lang::get('backend::lang.recordfinder.invalid_model_class', ['modelClass' => $this->modelClass]));
        }

        if (post('recordfinder_flag')) {
            $this->listWidget = $this->makeListWidget();
            $this->listWidget->bindToController();

            $this->searchWidget = $this->makeSearchWidget();
            $this->searchWidget->bindToController();

            $this->listWidget->setSearchTerm($this->searchWidget->getActiveTerm());

            // Link the Search Widget to the List Widget
            $this->searchWidget->bindEvent('search.submit', function () {
                $this->listWidget->setSearchTerm($this->searchWidget->getActiveTerm());
                return $this->listWidget->onRefresh();
            });
        }
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('css/recordfinder.css');
        $this->addJs('js/recordfinder.js');
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('container');
    }

    /**
     * prepareVars for display
     */
    public function prepareVars()
    {
        $this->relationModel = $this->getLoadValue();

        if ($this->formField->disabled) {
            $this->previewMode = true;
        }

        $this->vars['displayMode'] = 'single';
        $this->vars['value'] = $this->getKeyValue();
        $this->vars['field'] = $this->formField;
        $this->vars['nameValue'] = $this->getNameValue();
        $this->vars['descriptionValue'] = $this->getDescriptionValue();
        $this->vars['listWidget'] = $this->listWidget;
        $this->vars['searchWidget'] = $this->searchWidget;
        $this->vars['title'] = $this->title;
    }

    /**
     * onRefresh AJAX handler
     */
    public function onRefresh()
    {
        $value = post($this->getFieldName());

        $this->setKeyValue($value);

        $this->prepareVars();

        return ['#'.$this->getId('container') => $this->makePartial('recordfinder')];
    }

    /**
     * onClearRecord AJAX handler
     */
    public function onClearRecord()
    {
        $this->setKeyValue(null);

        $this->prepareVars();

        return ['#'.$this->getId('container') => $this->makePartial('recordfinder')];
    }

    /**
     * onFindRecord AJAX handler
     */
    public function onFindRecord()
    {
        $this->prepareVars();

        // Purge the search term stored in session
        if ($this->searchWidget) {
            $this->listWidget->setSearchTerm(null);
            $this->searchWidget->setActiveTerm(null);
        }

        return $this->makePartial('recordfinder_form');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return strlen($value) ? $value : null;
    }

    /**
     * @inheritDoc
     */
    public function getLoadValue()
    {
        $value = null;

        if ($this->useRelation) {
            [$model, $attribute] = $this->resolveModelAttribute($this->valueFrom);
            if ($model !== null) {
                $value = $model->{$attribute};
            }

            // Multi support
            if ($value instanceof Collection) {
                $value = $value->first();
            }
        }
        else {
            $value = parent::getLoadValue();
            if ($value) {
                $value = $this->modelClass::where($this->getKeyFromAttributeName(), $value)->first();
            }
        }

        return $value;
    }

    /**
     * setKeyValue
     */
    public function setKeyValue($value)
    {
        $this->relationKeyValue = $value;

        if ($this->useRelation) {
            [$model, $attribute] = $this->resolveModelAttribute($this->valueFrom);
            $model->{$attribute} = $value;
        }
        else {
            $this->formField->value = $value;
        }
    }

    /**
     * getKeyValue
     */
    public function getKeyValue()
    {
        if ($this->relationKeyValue !== -1) {
            return $this->relationKeyValue;
        }

        if (!$this->relationModel) {
            return null;
        }

        return $this->useRelation
            ? $this->relationModel->{$this->getKeyFromAttributeName()}
            : $this->formField->value;
    }

    /**
     * getKeyFromAttributeName
     */
    protected function getKeyFromAttributeName()
    {
        if ($this->keyFrom) {
            return $this->keyFrom;
        }

        if (!$this->useRelation) {
            return 'id';
        }

        $relationType = $this->getRelationType();
        $relationObject = $this->getRelationObject();

        // Relations can specify a custom local or foreign "other" key,
        // which can be detected and implemented here automatically.
        if (in_array($relationType, ['belongsTo'])) {
            $primaryKeyName = $relationObject->getOwnerKeyName();
        }
        elseif (in_array($relationType, ['hasMany', 'hasOne', 'belongsToMany', 'morphedByMany', 'morphToMany'])) {
            $primaryKeyName = $relationObject->getRelatedKeyName();
        }
        else {
            $primaryKeyName = $this->getRelationModel()->getKeyName();
        }

        return $primaryKeyName;
    }

    /**
     * getNameValue
     */
    public function getNameValue()
    {
        if (!$this->relationModel || !$this->nameFrom) {
            return null;
        }

        return $this->relationModel->{$this->nameFrom};
    }

    /**
     * getDescriptionValue
     */
    public function getDescriptionValue()
    {
        if (!$this->relationModel || !$this->descriptionFrom) {
            return null;
        }

        return $this->relationModel->{$this->descriptionFrom};
    }

    /**
     * makeListWidget
     */
    protected function makeListWidget()
    {
        $config = $this->makeConfig($this->getConfig('list'));

        if ($this->useRelation) {
            $config->model = $this->getRelationModel();
        }
        else {
            $config->model = new $this->modelClass;
        }

        $config->alias = $this->alias . 'List';
        $config->showSetup = false;
        $config->showCheckboxes = false;
        $config->recordsPerPage = $this->recordsPerPage;
        $config->recordOnClick = sprintf("$('#%s').recordFinder('updateRecord', this, ':" . $this->getKeyFromAttributeName() . "')", $this->getId());
        $widget = $this->makeWidget(\Backend\Widgets\Lists::class, $config);

        $widget->setSearchOptions([
            'mode' => $this->searchMode,
            'scope' => $this->searchScope,
        ]);

        if ($sqlConditions = $this->conditions) {
            $widget->bindEvent('list.extendQueryBefore', function ($query) use ($sqlConditions) {
                $query->whereRaw($sqlConditions);
            });
        }
        elseif ($scopeMethod = $this->scope) {
            $widget->bindEvent('list.extendQueryBefore', function ($query) use ($scopeMethod) {
                if (
                    is_string($scopeMethod) &&
                    count($staticMethod = explode('::', $scopeMethod)) === 2 &&
                    is_callable($staticMethod)
                ) {
                    $staticMethod($query, $this->model);
                }
                elseif (is_string($scopeMethod)) {
                    $query->$scopeMethod($this->model);
                }
                else {
                    $scopeMethod($query, $this->model);
                }
            });
        }
        else {
            if ($this->useRelation) {
                $widget->bindEvent('list.extendQueryBefore', function ($query) {
                    $this->getRelationObject()->addDefinedConstraintsToQuery($query);
                });
            }
        }

        return $widget;
    }

    /**
     * makeSearchWidget
     */
    protected function makeSearchWidget()
    {
        $config = $this->makeConfig();
        $config->alias = $this->alias . 'Search';
        $config->growable = false;
        $config->prompt = 'backend::lang.list.search_prompt';
        $widget = $this->makeWidget(\Backend\Widgets\Search::class, $config);
        $widget->cssClasses[] = 'recordfinder-search';
        return $widget;
    }
}
