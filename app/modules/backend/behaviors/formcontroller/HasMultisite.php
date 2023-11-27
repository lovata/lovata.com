<?php namespace Backend\Behaviors\FormController;

use Site;

/**
 * HasMultisite contains logic for managing multisite records
 */
trait HasMultisite
{
    /**
     * formHasMultisite
     */
    public function formHasMultisite($model)
    {
        return $model &&
            $model->isClassInstanceOf(\October\Contracts\Database\MultisiteInterface::class) &&
            $model->isMultisiteEnabled();
    }

    /**
     * makeMultisiteRedirect
     */
    public function makeMultisiteRedirect($context = null, $model = null)
    {
        if (!$model || !$this->controller->formHasMultisite($model)) {
            return;
        }

        $activeSiteId = Site::getSiteIdFromContext();
        if ((int) $model->site_id === (int) $activeSiteId) {
            return;
        }

        $otherModel = $model->findOrCreateForSite($activeSiteId);

        return $this->makeRedirect($context, $otherModel, ['_site_id' => $activeSiteId]);
    }

    /**
     * onSwitchSite
     */
    public function onSwitchSite($recordId = null)
    {
        $result = [];

        $siteId = post('site_id');
        $model = $this->controller->formFindModelObject($recordId);
        if (!$siteId || !$model) {
            return $result;
        }

        $otherModel = $model->findForSite($siteId);

        // Model missing or trashed
        $showConfirm = !$otherModel || (
            $otherModel->isClassInstanceOf(\October\Contracts\Database\SoftDeleteInterface::class) &&
            $otherModel->trashed()
        );

        if ($showConfirm) {
            $result['confirm'] = __('A record does not exist for the selected site. Create one?');
        }

        return $result;
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
}
