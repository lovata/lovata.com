<?php namespace Tailor\Behaviors;

use Flash;
use Backend\Classes\FormField;
use Backend\Classes\ControllerBehavior;
use Exception;

/**
 * DraftController is a modifier to FormController to enable draft records
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class DraftController extends ControllerBehavior
{
    use \Backend\Traits\FormModelSaver;

    /**
     * @var Model model used by the form
     */
    protected $primaryModel;

    /**
     * @var Model model used by the form
     */
    protected $model;

    /**
     * @var string draftId
     */
    protected $draftId;

    /**
     * @var string deleteMethod
     */
    protected $deleteMethod = 'delete';

    /**
     * initDraft
     */
    public function initDraft($model, $context = null)
    {
        $this->primaryModel = $model;

        if ($model->isClassInstanceOf(\October\Contracts\Database\SoftDeleteInterface::class)) {
            $this->deleteMethod = 'forceDelete';
        }

        if ($this->isDraftMode()) {
            $draftModel = $this->controller->formFindModelObject($this->getDraftId());

            if ($draftModel->isUnsavedDraftStatus()) {
                $context = FormField::CONTEXT_CREATE;
            }

            $this->controller->initForm($draftModel, $context);

            $this->model = $draftModel;

            $this->draftId = $draftModel->getKey();
        }
        else {
            $this->controller->initForm($model, $context);

            $this->model = $model;
        }
    }

    /**
     * Controller "create" action used for creating new model records.
     *
     * @param string $context Form context
     * @return void
     */
    public function create()
    {
        $model = $this->controller->formCreateModelObject();

        $model->saveAsFirstDraft(['name' => __('First Draft')]);

        return $this->makeDraftRedirect(FormField::CONTEXT_UPDATE, $model);
    }

    /**
     * Controller "update" action used for updating existing model records.
     *
     * @param int $recordId Record identifier
     * @param string $context Form context
     * @return void
     */
    public function update($recordId = null, $context = null)
    {
        try {
            $model = $this->controller->formFindModelObject($recordId);
            $context = $context ?: FormField::CONTEXT_UPDATE;

            // Multisite
            if ($this->controller->formHasMultisite($model)) {
                if ($redirect = $this->makeMultisiteDraftRedirect('create', $model)) {
                    return $redirect;
                }

                $this->addHandlerToSiteSwitcher();
            }

            $this->initDraft($model, $context);
        }
        catch (Exception $ex) {
            $this->controller->handleError($ex);
        }
    }

    /**
     * addHandlerToSiteSwitcher
     */
    protected function addHandlerToSiteSwitcher()
    {
        $siteSwitcher = $this->getWidget('siteSwitcher');
        if (!$siteSwitcher) {
            return;
        }

        $siteSwitcher->setSwitchHandler('onSwitchSite');
    }

    /**
     * onCommitDraft is saving a draft without publishing it
     */
    public function onCommitDraft($recordId = null)
    {
        $model = $this->controller->formFindModelObject($recordId);

        $this->initDraft($model);

        $draftModel = $this->draftGetDraftModel();

        $draftModel->setDraftCommit((array) post('Draft'));

        $formWidget = $this->controller->formGetWidget();

        $this->controller->formBeforeSave($model);

        $this->performSaveOnModel(
            $draftModel,
            $formWidget->getSaveData(),
            ['sessionKey' => $formWidget->getSessionKey(), 'propagate' => true]
        );

        $this->controller->formAfterSave($model);

        Flash::success(__('Draft Saved'));

        return $this->makeDraftRedirect(FormField::CONTEXT_UPDATE, $model);
    }

    /**
     * onPublishDraft is saving a draft and publishing it
     */
    public function onPublishDraft($recordId = null, $context = null)
    {
        $model = $this->controller->formFindModelObject($recordId);

        $isFirstDraft = $model->isFirstDraftStatus();

        $this->initDraft($model);

        $model->setDraftPublish();

        $formWidget = $this->controller->formGetWidget();

        $this->controller->formBeforeSave($model);

        $this->performSaveOnModel(
            $model,
            $formWidget->getSaveData(),
            ['sessionKey' => $formWidget->getSessionKey(), 'propagate' => true]
        );

        $this->controller->formAfterSave($model);

        if (!$isFirstDraft) {
            $draftModel = $this->draftGetDraftModel();
            $draftModel->{$this->deleteMethod}();
        }

        Flash::success(__('Draft Applied'));

        return $this->controller->makeRedirect(FormField::CONTEXT_UPDATE, $model);
    }

    /**
     * onCreateDraft is creating a new draft from a published record
     */
    public function onCreateDraft($recordId = null, $context = null)
    {
        $model = $this->controller->formFindModelObject($recordId);

        $this->initDraft($model);

        $draftName = __('Draft').' '.($model->countDrafts() + 1);

        $newModel = $model->createNewDraft([
            'name' => $draftName,
            'notes' => $draftName.': '.__('put the draft notes here')
        ]);

        $this->draftId = $newModel->getKey();

        Flash::success(__('Draft Created'));

        return $this->makeDraftRedirect(FormField::CONTEXT_UPDATE, $model);
    }

    /**
     * onDiscardDraft deletes the draft
     */
    public function onDiscardDraft($recordId = null, $context = null)
    {
        $model = $this->controller->formFindModelObject($recordId);

        $this->initDraft($model);

        if ($draftModel = $this->draftGetDraftModel()) {
            $draftModel->{$this->deleteMethod}();
            Flash::success(__('Draft Discarded'));
        }
        else {
            Flash::error('Unable to find draft model');
        }

        if ($redirect = $this->controller->makeRedirect('update', $model)) {
            return $redirect;
        }
    }

    /**
     * draftGetPrimaryModel
     */
    public function draftGetPrimaryModel()
    {
        return $this->primaryModel;
    }

    /**
     * draftGetDraftModel
     */
    public function draftGetDraftModel()
    {
        return $this->model;
    }

    /**
     * getDraftId
     */
    public function getDraftId(): string
    {
        if ($this->draftId) {
            return $this->draftId;
        }

        return (string) get('draft');
    }

    /**
     * isDraftMode
     */
    public function isDraftMode(): bool
    {
        return (bool) $this->getDraftId();
    }

    /**
     * makeMultisiteDraftRedirect
     */
    public function makeMultisiteDraftRedirect($context = null, $model = null)
    {
        if ($model->isUnsavedDraftStatus()) {
            return;
        }

        return $this->controller->makeMultisiteRedirect($context, $model);
    }

    /**
     * makeDraftRedirect
     */
    public function makeDraftRedirect($context = null, $model = null, $queryParams = [])
    {
        // Escaping draft mode
        if (post('close')) {
            return $this->controller->makeRedirect($context, $model);
        }

        // Redirect back to the same page
        if ($this->isDraftMode()) {
            $queryParams += ['draft' => $this->getDraftId()];
        }
        elseif ($model->isDraftStatus()) {
            $queryParams += ['draft' => $model->getKey()];
        }

        return $this->controller->makeRedirect($context, $model, $queryParams);
    }

    /**
     * makeDraftRedirectToClosest
     */
    public function makeDraftRedirectToClosest($model)
    {
        if ($model->isDraftStatus()) {
            return;
        }

        // Find nearest draft model
        $draftModel = $model->getDraftRecords()->first();
        if (!$draftModel) {
            return;
        }

        return $this->makeDraftRedirect(
            FormField::CONTEXT_UPDATE,
            $model,
            ['draft' => $draftModel->getKey()]
        );
    }
}
