<?php namespace Backend\Behaviors\FormController;

use Backend;
use ApplicationException;

/**
 * HasFormDesigns
 */
trait HasFormDesigns
{
    /**
     * getDesignDisplayMode returns the display mode taken from the form configuration,
     * defaults to `basic` display mode.
     */
    protected function getDesignDisplayMode()
    {
        return $this->getConfig(
            "{$this->context}[design][displayMode]",
            $this->getConfig('design[displayMode]')
        ) ?: 'basic';
    }

    /**
     * getDesignFormSize returns the page size taken from the form configuration,
     * can also specify a custom configuration name, e.g. `sidebarSize`.
     */
    protected function getDesignFormSize($name = 'size')
    {
        $value = $this->getConfig(
            "{$this->context}[design][{$name}]",
            $this->getConfig("design[{$name}]")
        ) ?: 'auto';

        return Backend::sizeToPixels($value) ?: null;
    }

    /**
     * getDesignBodyClass
     */
    protected function getDesignBodyClass()
    {
        if ($this->getDesignDisplayMode() === 'sidebar') {
            return 'compact-container';
        }

        return null;
    }

    /**
     * isHorizontalForm
     */
    protected function isHorizontalForm(): bool
    {
        if ($this->getConfig("{$this->context}[design][horizontalMode]", $this->getConfig('design[horizontalMode]'))) {
            return true;
        }

        return $this->getDesignDisplayMode() === 'survey';
    }

    /**
     * isSurveyDesign
     */
    protected function isSurveyDesign(): bool
    {
        if ($this->getConfig("{$this->context}[design][surveyMode]", $this->getConfig('design[surveyMode]'))) {
            return true;
        }

        return $this->getDesignDisplayMode() === 'survey';
    }

    /**
     * isPopupDesign
     */
    protected function isPopupDesign(): bool
    {
        return $this->getDesignDisplayMode() === 'popup';
    }

    /**
     * beforeDisplayPopup
     */
    protected function beforeDisplayPopup()
    {
        if (!post('form_popup_flag')) {
            return;
        }

        // Emulate the form action
        if ($id = post('form_record_id')) {
            $this->update($id);
        }
        else {
            $this->create();
        }
    }

    /**
     * hidePopupDesign
     */
    protected function hidePopupDesign()
    {
        $this->extensionHideMethod('index_onPopupLoadForm');
        $this->extensionHideMethod('index_onPopupSave');
        $this->extensionHideMethod('index_onPopupCancel');
    }

    /**
     * index_onPopupLoadForm
     */
    public function index_onLoadPopupForm()
    {
        if (!$this->isPopupDesign()) {
            throw new ApplicationException(__("This form is not using a popup design."));
        }

        if ($id = post('form_record_id')) {
            $this->update($id);
            $this->vars['popupTitle'] = $this->getLang('update[title]', 'backend::lang.form.update_title');
            $this->vars['recordId'] = $id;
        }
        else {
            $this->create();
            $this->vars['popupTitle'] = $this->getLang('create[title]', 'backend::lang.form.create_title');
        }

        $this->vars['popupSize'] = $this->controller->pageSize;
        return $this->formRenderDesign();
    }

    /**
     * index_onSave
     */
    public function index_onPopupSave()
    {
        if ($id = post('form_record_id')) {
            $this->update_onSave($id);
        }
        else {
            $this->create_onSave();
        }

        return $this->controller->listRefresh();
    }

    /**
     * index_onPopupCancel
     */
    public function index_onPopupCancel()
    {
        if ($id = post('form_record_id')) {
            $this->update_onCancel($id);
        }
        else {
            $this->create_onCancel();
        }
    }

    /**
     * index_onPopupDelete
     */
    public function index_onPopupDelete()
    {
        if ($id = post('form_record_id')) {
            $this->update_onDelete($id);
        }

        return $this->controller->listRefresh();
    }
}
