<?php namespace Tailor\Controllers;

use Arr;
use Lang;
use Flash;
use Backend;
use Redirect;
use BackendMenu;
use Tailor\Models\EntryRecord;
use Tailor\Classes\RecordIndexer;
use Tailor\Classes\Blueprint;
use Tailor\Classes\BlueprintIndexer;
use Backend\Classes\WildcardController;
use Tailor\Classes\Blueprint\SingleBlueprint;
use Tailor\Classes\Blueprint\StructureBlueprint;
use ApplicationException;
use ForbiddenException;
use NotFoundException;

/**
 * Entries controller
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class Entries extends WildcardController
{
    /**
     * @var array implement extensions
     */
    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class,
        \Tailor\Behaviors\PreviewController::class,
        \Tailor\Behaviors\VersionController::class,
        \Tailor\Behaviors\DraftController::class
    ];

    /**
     * @var array listConfig is `ListController` configuration.
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var string formConfig is `FormController` configuration.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var Blueprint activeSource
     */
    protected $activeSource;

    /**
     * @var string actionMethod is the action method to call
     */
    protected $actionMethod;

    /**
     * beforeDisplay
     */
    public function beforeDisplay()
    {
        // Pop off first parameter as source handle
        $sourceHandle = array_shift($this->params);
        $this->makeBlueprintSource($sourceHandle);

        $validMethods = ['create'];
        $slug = $this->params[0] ?? null;

        if (in_array($slug, $validMethods)) {
            // Pop second parameter as action method
            $actionMethod = array_shift($this->params);

            $this->actionMethod = $this->actionView = $actionMethod;
        }
        elseif ($this->isSectionSingular() || $slug) {
            $this->actionMethod = $this->actionView = 'update';
        }

        if (!$this->activeSource) {
            throw new NotFoundException;
        }

        $this->checkSourcePermission();

        $this->setNavigationContext();

        $this->setPreviewPageContext('section', $this->activeSource);
    }

    /**
     * actionUrl returns a URL for this controller and supplied action.
     */
    public function actionUrl($action = null, $path = null)
    {
        $url = 'tailor/entries/'.$this->activeSource->handleSlug.'/'.$action;
        if ($path) {
            $url .= '/'.$path;
        }

        return Backend::url($url);
    }

    /**
     * index action
     */
    public function index()
    {
        if ($this->hasFatalError()) {
            return;
        }

        if ($this->actionMethod) {
            $this->addJs('/modules/tailor/assets/js/tailor-app.base.js');
            $this->addJs('/modules/tailor/assets/js/tailor-entry.js');

            $this->registerVueComponent(\Backend\VueComponents\Document::class);
            $this->registerVueComponent(\Backend\VueComponents\DropdownMenuButton::class);
            $this->registerVueComponent(\Tailor\VueComponents\PublishingControls::class);
            $this->registerVueComponent(\Tailor\VueComponents\PublishButton::class);
            $this->registerVueComponent(\Tailor\VueComponents\DraftNotes::class);

            return $this->{$this->actionMethod}(...$this->params);
        }

        $this->asExtension('ListController')->index();

        $this->prepareVars();
    }

    /**
     * index_onBulkAction
     */
    public function index_onBulkAction()
    {
        if (
            ($bulkAction = post('action')) &&
            ($checkedIds = post('checked')) &&
            is_array($checkedIds) &&
            count($checkedIds)
        ) {
            foreach ($checkedIds as $modelId) {
                if (!$model = $this->formFindModelObject($modelId)) {
                    continue;
                }

                switch ($bulkAction) {
                    case 'disable':
                        $this->checkSourcePermission('publish');
                        $model->is_enabled = false;
                        $model->save();
                        Flash::success(__('Entries have been disabled'));
                        break;

                    case 'enable':
                        $this->checkSourcePermission('publish');
                        $model->is_enabled = true;
                        $model->save();
                        Flash::success(__('Entries have been enabled'));
                        break;

                    case 'delete':
                        $this->checkSourcePermission('delete');
                        $model->delete();
                        Flash::success(__('Entries have been deleted'));
                        break;

                    case 'restore':
                        $this->checkSourcePermission('delete');
                        $model->restore();
                        Flash::success(__('Entries have been restored'));
                        break;

                    case 'forceDelete':
                        $this->checkSourcePermission('delete');
                        $model->forceDelete();
                        Flash::success(__('Entries have been deleted'));
                        break;

                    case 'duplicate':
                        $this->checkSourcePermission('create');
                        $model->duplicateRecord()->save(['propagate' => true]);
                        Flash::success(__('Entries have been duplicated'));
                        break;
                }
            }
        }

        return $this->listRefresh();
    }

    /**
     * create action
     */
    public function create()
    {
        $this->checkSourcePermission('create');

        if ($this->isSectionDraftable()) {
            return $this->asExtension('DraftController')->create();
        }

        $this->bodyClass = 'compact-container';

        $this->pageTitle = 'Create Entry';

        $this->asExtension('FormController')->create();

        $this->prepareVars();

        $this->vars['initialState']['isCreateAction'] = true;
    }

    /**
     * update action
     */
    public function update($recordId = null)
    {
        $this->bodyClass = 'compact-container';

        $this->pageTitle = 'Update Entry';

        if ($this->isVersionMode()) {
            $response = $this->asExtension('VersionController')->update($recordId);
        }
        elseif ($this->isDraftMode()) {
            $response = $this->asExtension('DraftController')->update($recordId);
        }
        else {
            $response = $this->asExtension('FormController')->update($recordId);
        }

        if ($response) {
            return $response;
        }

        $model = $this->formGetModel();

        // Record not found or some error happened
        if (!$model) {
            // throw new ApplicationException('Record not found');
            return;
        }

        // Helpful redirect to active draft for non-publishers
        if (!$this->hasSourcePermission('publish') && ($redirect = $this->makeDraftRedirectToClosest($model))) {
            return $redirect;
        }

        if ($model->isVersionStatus()) {
            $this->actionView = 'version';
        }

        $this->prepareVars();
    }

    /**
     * prepareVars
     */
    protected function prepareVars()
    {
        $model = $this->getPrimaryModel();

        $this->vars['primaryModel'] = $model;
        $this->vars['entityName'] = $this->activeSource->name ?? '';
        $this->vars['activeSource'] = $this->activeSource;
        $this->vars['initialState'] = $this->makeInitialState($model);
    }

    /**
     * makeInitialState
     */
    public function makeInitialState($model)
    {
        if (!$model) {
            return [];
        }

        $initialState = [
            'primaryRecordUrl' => $this->actionUrl($model->getKey()),
            'entryTypeOptions' => $model->getContentGroupOptions(),
            'draftable' => $this->isSectionDraftable(),
            'contentGroup' => $model->content_group,
            'isDraft' => $this->isSectionDraftable() && ($model->isDraftStatus() || $this->isDraftMode()),
            'isFirstDraft' => $this->isSectionDraftable() && $model->isFirstDraftStatus(),
            'isSingular' => $this->isSectionSingular(),
            'isDeleted' => $model->trashed(),
            'drafts' => $model->getDraftRecords(),
            'statusCode' => $model->status_code,
            'hasPreviewPage' => $this->hasPreviewPage(),
            'statusCodeOptions' => Arr::trans($model->getStatusCodeOptions()),
            'showTreeControls' => $this->isSectionStructured() && $this->activeSource->hasTree(),
            'fullSlug' => $model->fullslug,
            'canDelete' => $this->hasSourcePermission('delete'),
            'canPublish' => $this->hasSourcePermission('publish'),
            'canRestore' => $model->trashed(),
        ];

        $showEntryTypeSelector = isset($initialState['entryTypeOptions']) && $initialState['entryTypeOptions'];
        if ($initialState['isDraft'] && !$initialState['isFirstDraft']) {
            $showEntryTypeSelector = false;
        }

        if ($initialState['isDeleted']) {
            $showEntryTypeSelector = false;
        }

        $initialState['showEntryTypeSelector'] = $showEntryTypeSelector;

        if ($this->isDraftMode()) {
            $formModel = $this->formGetModel();
            $initialState['draftNotes'] = $formModel->getDraftNotes();
            $initialState['draftName'] = $formModel->getDraftName();
            $initialState['currentDraftId'] = $formModel->getDraftId();
        }

        return $initialState;
    }

    /**
     * prepareAjaxResponseVars
     */
    protected function prepareAjaxResponseVars()
    {
        $model = $this->formGetModel();

        return [
            'result' => [
                'drafts' => $model->getDraftRecords(),
                'versions' => $model->getVersionRecords(),
                'statusCode' => $model->status_code,
                'fullSlug' => $model->fullslug
            ]
        ];
    }

    /**
     * getPrimaryModel
     */
    protected function getPrimaryModel()
    {
        if ($this->isVersionMode()) {
            return $this->versionGetPrimaryModel();
        }

        if ($this->isDraftMode()) {
            return $this->draftGetPrimaryModel();
        }

        return $this->formGetModel();
    }

    /**
     * onCommitDraft is saving a draft without publishing it
     */
    public function onCommitDraft($recordId = null)
    {
        $redirect = $this->asExtension('DraftController')->onCommitDraft($recordId);
        if (post('close')) {
            return $redirect;
        }

        return $this->prepareAjaxResponseVars();
    }

    /**
     * onSave
     */
    public function onSave($recordId = null)
    {
        if ($this->actionMethod === 'update') {
            $redirect = $this->asExtension('FormController')->update_onSave($recordId);
            if (post('close')) {
                return $redirect;
            }

            return $this->prepareAjaxResponseVars();
        }
        else {
            return $this->asExtension('FormController')->create_onSave();
        }
    }

    /**
     * onDelete
     */
    public function onDelete($recordId = null)
    {
        $this->checkSourcePermission('delete');

        if ($this->actionMethod === 'update') {
            return $this->asExtension('FormController')->update_onDelete($recordId);
        }
    }

    /**
     * onRestore
     */
    public function onRestore($recordId = null)
    {
        $this->checkSourcePermission('publish');

        if ($model = $this->formFindModelObject($recordId)) {
            $model->restore();

            Flash::success(Lang::get('backend::lang.form.restore_success', [
                'name' => Lang::get($this->asExtension('FormController')->getConfig('name', 'backend::lang.model.name'))
            ]));

            return Redirect::refresh();
        }
    }

    /**
     * onForceDelete
     */
    public function onForceDelete($recordId = null)
    {
        $this->checkSourcePermission('delete');

        if ($model = $this->formFindModelObject($recordId)) {
            $model->forceDelete();

            Flash::success(Lang::get('backend::lang.form.delete_success'));

            if ($redirect = $this->makeRedirect('delete', $model)) {
                return $redirect;
            }
        }
    }

    /**
     * onChangeEntryType
     */
    public function onChangeEntryType()
    {
        $this->formGetWidget()->setFormValues();

        return ['#entryPrimaryTabs' => $this->makePartial('primary_tabs')];
    }

    /**
     * listGetConfig
     */
    public function listGetConfig($definition)
    {
        $config = $this->asExtension('ListController')->listGetConfig($definition);

        if ($this->isSectionStructured()) {
            $config->structure = [
                'maxDepth' => $this->activeSource->getMaxDepth(),
                'showTree' => $this->activeSource->hasTree(),
            ] + ((array) $this->activeSource->structure);
        }

        // Each source needs its own session store
        $config->widgetAlias = camel_case(
            $definition . '-' . $this->activeSource->handleSlug
        );

        return $config;
    }

    /**
     * listExtendModel
     */
    public function listExtendModel($model)
    {
        $model->extendWithBlueprint($this->activeSource->uuid);

        return $model;
    }

    /**
     * listExtendQuery
     */
    public function listExtendQuery($query)
    {
        if ($this->isSectionDraftable()) {
            $query->withSavedDrafts();
        }
    }

    /**
     * listExtendRecords
     */
    public function listExtendRecords($records)
    {
        $this->eagerLoadRelationsForList($this->listGetWidget(), $records);
    }

    /**
     * listOverrideRecordUrl
     */
    public function listOverrideRecordUrl($record, $definition = null)
    {
        return "tailor/entries/{$this->activeSource->handleSlug}/{$record->id}";
    }

    /**
     * listAfterReorder
     */
    public function listAfterReorder($record, $definition = null)
    {
        // Reload the new record position
        $record = $record->newQueryWithoutScopes()->find($record->getKey());

        RecordIndexer::instance()->process($record);
    }

    /**
     * listExtendRefreshResults updates bulk actions when the list changes
     */
    public function listExtendRefreshResults($filter, $result)
    {
        return ['#listBulkActions' => $this->makePartial('list_bulk_actions')];
    }

    /**
     * eagerLoadRelations
     */
    protected function eagerLoadRelationsForList($list, $models)
    {
        if (!$models->count()) {
            return;
        }

        $definitions = $models->first()->getRelationDefinitions();

        foreach ($definitions as $type => $relations) {
            foreach ($relations as $name => $options) {
                if (!$list->isColumnVisible($name)) {
                    continue;
                }

                $models->loadMissing($name);
            }
        }
    }

    /**
     * formBeforeSave
     */
    public function formBeforeSave($model)
    {
        if ($this->isSectionVersionable()) {
            $this->asExtension('VersionController')->versionBeforeSave($model);
        }
    }

    /**
     * formAfterSave
     */
    public function formAfterSave($model)
    {
        RecordIndexer::instance()->process($model);
    }

    /**
     * formFindModelObject
     */
    public function formFindModelObject($recordId)
    {
        if (!$recordId && $this->isSectionSingular()) {
            $recordId = $this->findSingularModelObjectWithFallback()->getKey();
        }

        $model = EntryRecord::inSection($this->activeSource->handle);

        // Remove multisite restriction
        if ($this->formHasMultisite($model)) {
            $model = $model->withSites();
        }

        if ($this->isVersionMode()) {
            $model = $model->withVersions();
        }

        if ($this->isDraftMode()) {
            $model = $model->withDrafts();
        }
        else {
            $model = $model->withSavedDrafts();
        }

        // Include deleted records
        $model = $model->withTrashed()->find($recordId);

        if (!$model) {
            throw new ApplicationException(Lang::get('backend::lang.form.not_found', [
                'class' => EntryRecord::class, 'id' => $recordId
            ]));
        }

        // Mimic parent method
        $this->formExtendModel($model);

        return $model;
    }

    /**
     * formGetRedirectUrl
     */
    public function formGetRedirectUrl($context = null, $model = null): string
    {
        if (post('close') || $this->isSectionSingular() || $context === 'delete') {
            $url = 'tailor/entries/'.$this->activeSource->handleSlug;
        }
        else {
            $url = 'tailor/entries/'.$this->activeSource->handleSlug.'/'.$model->getKey();
        }

        return $url;
    }

    /**
     * formCreateModelObject
     */
    public function formCreateModelObject()
    {
        $model = new EntryRecord;

        $model->extendWithBlueprint($this->activeSource->uuid);

        $model->setDefaultContentGroup(get('entry_type'));

        // Without publish permission, enabled should be default false
        if (!$this->hasSourcePermission('publish')) {
            $model->is_enabled = false;
        }

        return $model;
    }

    /**
     * formExtendModel
     */
    public function formExtendModel($model)
    {
        // Entry type switching
        if ($entryType = post('EntryRecord[content_group]')) {
            $model->setBlueprintGroup($entryType);
        }
    }

    /**
     * formExtendFields
     */
    public function formExtendFields($widget)
    {
        if (!$this->hasSourcePermission('publish')) {
            $widget->getField('is_enabled')->hidden();
            $widget->getField('published_at')->hidden();
            $widget->getField('expired_at')->hidden();
        }
    }

    /**
     * isSectionActive
     */
    protected function isSectionActive(Blueprint $section): bool
    {
        return $this->activeSource->uuid === $section->uuid;
    }

    /**
     * isSectionDraftable
     */
    protected function isSectionDraftable(): bool
    {
        return $this->activeSource && $this->activeSource->useDrafts();
    }

    /**
     * isSectionVersionable
     */
    protected function isSectionVersionable(): bool
    {
        return $this->activeSource && $this->activeSource->useVersions();
    }

    /**
     * isSectionSingular
     */
    protected function isSectionSingular(): bool
    {
        return $this->activeSource && $this->activeSource instanceof SingleBlueprint;
    }

    /**
     * isSectionMultisite
     */
    protected function isSectionMultisite(): bool
    {
        return $this->activeSource && $this->activeSource->useMultisite();
    }

    /**
     * isSectionStructure
     */
    protected function isSectionStructured(): bool
    {
        return $this->activeSource && $this->activeSource instanceof StructureBlueprint;
    }

    /**
     * makeBlueprintSource
     */
    protected function makeBlueprintSource($activeSource = null): void
    {
        $this->activeSource = $activeSource
            ? BlueprintIndexer::instance()->findSectionByHandle($activeSource)
            : null;
    }

    /**
     * checkSourcePermission
     */
    protected function checkSourcePermission($names = null, $throwException = true)
    {
        if ($names) {
            $permissionNames = array_map(function($name) {
                return $this->activeSource->getPermissionCodeName($name);
            }, (array) $names);
        }
        else {
            $permissionNames = [$this->activeSource->getPermissionCodeName()];
        }

        $hasPermission = $this->user->hasAnyAccess($permissionNames);

        if (!$hasPermission && $throwException) {
            throw new ForbiddenException;
        }

        return $hasPermission;
    }

    /**
     * hasSourcePermission
     */
    protected function hasSourcePermission(...$names)
    {
        return $this->checkSourcePermission($names, false);
    }

    /**
     * setNavigationContext
     */
    protected function setNavigationContext()
    {
        $item = BlueprintIndexer::instance()->findSecondaryNavigation($this->activeSource->uuid);
        if ($item) {
            $item->setBackendControllerContext();
        }
        else {
            BackendMenu::setContext('October.Tailor', 'tailor');
        }
    }

    /**
     * findSingularModelObjectWithFallback
     */
    protected function findSingularModelObjectWithFallback()
    {
        $uuid = $this->activeSource->uuid;

        if (!$this->isSectionMultisite()) {
            return EntryRecord::findSingleForSectionUuid($uuid);
        }

        // Check site context first
        $record = EntryRecord::inSectionUuid($uuid)->first();
        if ($record) {
            return $record;
        }

        // Try by removing the multisite restriction
        $record = EntryRecord::inSectionUuid($uuid)->withSites()->first();
        if ($record) {
            return $record;
        }

        // Time to create a new record
        return EntryRecord::findSingleForSectionUuid($uuid);
    }
}
