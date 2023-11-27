<?php namespace Cms\Widgets;

use Redirect;
use Cms\Models\PageLookupItem;
use Backend\Classes\WidgetBase;
use ValidationException;

/**
 * PageLookup widget
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class PageLookup extends WidgetBase
{
    use \Backend\Traits\SearchableWidget;

    //
    // Configurable Properties
    //

    /**
     * @var string title text to display for the title of the popup list form
     */
    public $title = 'Select Page Link';

    //
    // Object Properties
    //

    /**
     * @var string defaultAlias to identify this widget.
     */
    protected $defaultAlias = 'pagemanager';

    /**
     * @var \Backend\Classes\WidgetBase selectWidget reference to the widget used for selecting a page.
     */
    protected $selectFormWidget;

    /**
     * bindToController
     */
    public function bindToController()
    {
        parent::bindToController();

        if ($redirect = $this->handleResolverRedirect()) {
            $this->controller->setResponse($redirect);
        }
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'title',
        ]);

        if (post('pagelookup_flag')) {
            $this->getSelectFormWidget();
        }
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addJsBundle('js/pagelookup.js', 'global');
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('lookup_form');
    }

    /**
     * prepareVars for display
     */
    public function prepareVars()
    {
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['title'] = $this->title;
        $this->vars['selectWidget'] = $this->getSelectFormWidget();
        $this->vars['includeTitle'] = $this->shouldIncludeTitle();
        $this->vars['singleMode'] = $this->isSingleMode();
    }

    /**
     * getLoadValue
     */
    public function getLoadValue()
    {
        return post('value');
    }

    /**
     * onLoadPopup AJAX handler
     */
    public function onLoadPopup()
    {
        // Disable asset broadcasting
        $this->controller->flushAssets();

        return $this->render();
    }

    /**
     * onSearchReference AJAX handler
     */
    public function onSearchReference()
    {
        // Disable asset broadcasting
        $this->controller->flushAssets();

        $this->setSearchTerm(post('term'));

        return ['results' => $this->getMatches()];
    }

    /**
     * onRefreshReference AJAX handler
     */
    public function onRefreshReference()
    {
        $data = post('PageLookupItem');
        if (array_get($data, 'type') === 'url') {
            $data['url'] = base64_decode(array_pull($data, 'reference', ''));
        }

        $form = $this->getSelectFormWidget();
        $form->setFormValues($data);

        return ['#'.$this->getId('selectWidget') => $form->render()];
    }

    /**
     * onInsertReference
     */
    public function onInsertReference()
    {
        // Disable asset broadcasting
        $this->controller->flushAssets();

        $widget = $this->getSelectFormWidget();

        $data = $widget->getSaveData();
        $type = array_pull($data, 'type', '');
        $reference = array_pull($data, 'reference', '');

        // Validate
        if (!$type) {
            throw new ValidationException(['type' => 'Missing type!']);
        }

        if (!$widget->getField('cms_page')->hidden && !isset($data['cms_page'])) {
            throw new ValidationException(['cms_page' => __('Please select a CMS page')]);
        }

        if (!$widget->getField('reference')->hidden && !$widget->getField('reference')->disabled && !$reference) {
            throw new ValidationException(['reference' => __('Please select a page reference')]);
        }

        if ($type === 'url' && (!isset($data['url']) || !PageLookupItem::isValidUrl($data['url']))) {
            throw new ValidationException(['reference' => __('Please enter a valid URL')]);
        }

        // Determine reference title
        $model = $widget->model;
        $model->type = $type;
        $model->reference = $reference;

        // Split params and data
        $params = $data;
        if ($title = $model->getReferenceLabel()) {
            $params['title'] = $title;

            if ($this->shouldIncludeTitle()) {
                $data['title'] = $title;
            }
        }

        return [
            'type' => $type,
            'reference' => $reference,
            'params' => $params,
            'link' => PageLookupItem::encodeSchema($type, $reference, $data)
        ];
    }

    /**
     * getSelectFormWidget
     */
    protected function getSelectFormWidget()
    {
        if ($this->selectFormWidget) {
            return $this->selectFormWidget;
        }

        $model = new PageLookupItem;
        $model->type = 'url';
        $model->singleMode = $this->isSingleMode();

        if ($value = $this->getLoadValue()) {
            $model = PageLookupItem::fromSchema($value) ?: $model;
        }

        $config = $this->makeConfig();
        $config->model = $model;
        $config->alias = $this->alias . 'Select';
        $config->arrayName = 'PageLookupItem';

        $form = $this->makeWidget(\Backend\Widgets\Form::class, $config);
        $form->bindEvent('form.extendFields', function() use ($form) {
            $form->getField('search')->searchHandler($this->getEventHandler('onSearchReference'));
        });
        $form->bindToController();

        return $this->selectFormWidget = $form;
    }

    /**
     * getMatches
     */
    protected function getMatches()
    {
        $searchTerm = mb_strtolower($this->getSearchTerm());
        if (!strlen($searchTerm)) {
            return [];
        }

        $words = explode(' ', $searchTerm);

        $iterator = function ($type, $references) use (&$iterator, $words) {
            $typeMatches = [];

            foreach ($references as $key => $referenceInfo) {
                $title = is_array($referenceInfo) ? $referenceInfo['title'] : $referenceInfo;
                if ($this->textMatchesSearch($words, $title)) {
                    $typeMatches[] = [
                        'id' => "$type::$key",
                        'text' => $title
                    ];
                }

                if (isset($referenceInfo['items']) && count($referenceInfo['items'])) {
                    $typeMatches = array_merge($typeMatches, $iterator($type, $referenceInfo['items']));
                }
            }

            return $typeMatches;
        };

        $types = [];
        $item = new PageLookupItem;
        $item->singleMode = $this->isSingleMode();
        foreach ($item->getTypeOptions() as $type => $typeTitle) {
            $typeInfo = $item->getTypeInfo($type);
            if (empty($typeInfo['references'])) {
                continue;
            }

            $typeMatches = $iterator($type, $typeInfo['references']);
            if (!empty($typeMatches)) {
                $types[] = [
                    'text' => __($typeTitle),
                    'children' => $typeMatches
                ];
            }
        }

        if (PageLookupItem::isValidUrl($searchTerm)) {
            $types[] = [
                'text' => __('URL'),
                'children' => [[
                    'id' => "url::".base64_encode($searchTerm),
                    'text' => $searchTerm
                ]]
            ];
        }

        return $types;
    }

    /**
     * handleResolverRedirect
     */
    protected function handleResolverRedirect()
    {
        $address = get('_lookup_link');
        if (!$address || !is_string($address)) {
            return;
        }

        $item = PageLookupItem::resolveFromSchema($address);
        if (!$item) {
            return;
        }

        return Redirect::to($item->url);
    }

    /**
     * shouldIncludeTitle
     */
    protected function shouldIncludeTitle(): bool
    {
        return (bool) post('pagelookup_title');
    }

    /**
     * isSingleMode
     */
    protected function isSingleMode(): bool
    {
        return (bool) post('pagelookup_single');
    }
}
