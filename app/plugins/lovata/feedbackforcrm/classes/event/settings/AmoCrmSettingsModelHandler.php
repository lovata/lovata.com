<?php namespace Lovata\FeedbackForCrm\Classes\Event\Settings;

use Lovata\FeedbackForCrm\Classes\Helper\AmoCrm\ApiAmoCRM;
use Lovata\FeedbackForCrm\Models\AmoCrmSettings;

/**
 * Class AmoCrmSettingsModelHandler
 * @package Lovata\FeedbackForCrm\Classes\Event\Settings
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class AmoCrmSettingsModelHandler
{
    const FORM_MAGIC_FORMS = 'magic_forms';

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        AmoCrmSettings::extend(function ($obAmoCrmSettings) {
            /** @var AmoCrmSettings $obAmoCrmSettings */
            $obAmoCrmSettings->bindEvent('model.afterSave', function () use ($obAmoCrmSettings) {
                $this->afterSave($obAmoCrmSettings);
            });
        });
        if (\System\Classes\PluginManager::instance()->hasPlugin('Martin.Forms')
            && !\System\Classes\PluginManager::instance()->isDisabled('Martin.Forms')) {
            $obEvent->listen(AmoCrmSettings::EVENT_FORM_LIST, function () {
                return [self::FORM_MAGIC_FORMS => trans('martin.forms::lang.plugin.name')];
            });
        }
    }

    /**
     * After save settings.
     * @param AmoCrmSettings $obAmoCrmSettings
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function afterSave($obAmoCrmSettings)
    {
        $this->generateTokenForAmoCRM($obAmoCrmSettings);
    }

    /**
     * Generate token for AmoCRM.
     * @param AmoCrmSettings $obAmoCrmSettings
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function generateTokenForAmoCRM($obAmoCrmSettings)
    {
        // Get token.
        $arData    = $obAmoCrmSettings->value;
        $arOldData = $obAmoCrmSettings->getOriginal('value');

        if (!is_array($arData)) {
            $arData = [];
        }

        if (!is_array($arOldData)) {
            try {
                $arOldData = json_decode($arOldData, true);
            } catch (\Exception $obException) {
                return;
            }
        }

        $sCode    = array_get($arData, 'code');
        $sOldCode = array_get($arOldData, 'code');
        $bForm    = (int) array_get($arData, 'form');

        if ($bForm && !empty($sCode) && $sCode != $sOldCode) {
            $obApiAmoCRM = new ApiAmoCRM();
            $obApiAmoCRM->token();
        }
    }
}
