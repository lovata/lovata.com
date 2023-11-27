<?php namespace Lovata\MightySeo\Classes\Event;

use Backend\Classes\Controller;

use Lovata\MightySeo\Models\SeoParam;
use Lovata\MightySeo\Models\SeoTemplate;
use Lovata\MightySeo\Classes\Item\SeoParamItem;
use Lovata\MightySeo\Classes\Helper\SeoConfigHelper;

/**
 * Class ExtendModel
 * @package Lovata\MightySeo\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendModel
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        //Get item class list
        $arItemClassList = SeoConfigHelper::getItemClassList();
        if (!empty($arItemClassList)) {
            foreach ($arItemClassList as $sClassName) {
                $this->extendItemClass($sClassName);
            }
        }

        $arClassList = SeoConfigHelper::getModelClassList();
        if (empty($arClassList)) {
            return;
        }

        foreach ($arClassList as $sModelName) {
            $this->addRelationConfigToModel($sModelName);
        }

        $this->extendBackendControllers();
        $this->extendBackendFields($obEvent, $arClassList);

    }

    /**
     * Add relation config to model with SeoTemplate model
     * @param string $sModelName
     */
    protected function addRelationConfigToModel($sModelName)
    {
        $sModelName::extend(function ($obModel) {
            /** @var \Model $obModel */
            $obModel->morphMany['seo_template'] = [
                SeoTemplate::class, 'name' => 'external',
            ];

            /** @var \Model $obModel */
            $obModel->morphOne['seo'] = [
                SeoParam::class, 'name' => 'external',
            ];

            $obModel->addDynamicMethod('getSeoParamIdAttribute', function () use ($obModel) {
                //Get seo param model
                $obSeoParam = $obModel->seo;
                if (empty($obSeoParam)) {
                    return null;
                }

                return $obSeoParam->id;
            });

            $obModel->addDynamicMethod('getSeoParamAttribute', function () use ($obModel) {
                $obSeoParamItem = SeoParamItem::make($obModel->seo_param_id);

                return $obSeoParamItem;
            });

            $obModel->addDynamicMethod('setSeoValueAttribute', function ($arValue) use ($obModel) {
                //Get seo param model
                $obSeoParam = $obModel->seo;
                if (empty($obSeoParam)) {
                    $obSeoParam = new SeoParam();
                    $obSeoParam->external_id = $obModel->id;
                    $obSeoParam->external_type = get_class($obModel);
                    $obSeoParam->fill($arValue);
                    $obSeoParam->save();
                } else {
                    $obSeoParam->update($arValue);
                }
            });

            $obModel->addDynamicMethod('getSeoValueAttribute', function () use ($obModel) {
                $arValueList = SeoConfigHelper::getRelationFieldValueList($obModel->seo);

                return $arValueList;
            });

            if (method_exists($obModel, 'addCachedField')) {
                $obModel->addCachedField('seo_param_id');
            }
        });
    }

    /**
     * Add SEO fields to backend forms
     * @param \Illuminate\Events\Dispatcher $obEvent
     * @param                               $arModelList
     */
    protected function extendBackendFields($obEvent, $arModelList)
    {
        $obEvent->listen('backend.form.extendFields', function ($obWidget) use ($arModelList) {

            /** @var \Backend\Widgets\Form $obWidget */

            if (empty($arModelList) || $obWidget->isNested || $obWidget->context != 'update' || preg_match('%PivotForm$%', $obWidget->alias)) {
                return;
            }

            //Get model class name
            $sModelClassName = get_class($obWidget->model);
            if (!in_array($sModelClassName, $arModelList)) {
                return;
            }

            $arSeoFieldList = SeoConfigHelper::getRelationFieldList();

            $obWidget->addTabFields($arSeoFieldList);
            $obWidget->addTabFields([
                'seo_template' => [
                    'tab'  => 'lovata.mightyseo::lang.tab.seo_template',
                    'type' => 'partial',
                    'path' => '$/lovata/mightyseo/views/seo_template.htm',
                ],
            ]);
        });
    }

    /**
     * Add relation config to backend controllers
     */
    protected function extendBackendControllers()
    {
        Controller::extend(function ($obController) {

            /** @var Controller $obController */
            if (empty($obController->implement)) {
                $obController->implement = [];
            }

            //Extend controller
            if (!in_array('Backend.Behaviors.RelationController', $obController->implement) && !in_array('Backend\Behaviors\RelationController', $obController->implement)) {
                $obController->implement[] = 'Backend.Behaviors.RelationController';
            }

            if (!isset($obController->relationConfig)) {
                $obController->addDynamicProperty('relationConfig');
            }

            // Splice in configuration safely
            $sConfigPath = '$/lovata/mightyseo/config/config_relation.yaml';

            $obController->relationConfig = $obController->mergeConfig(
                $obController->relationConfig,
                $sConfigPath
            );
        });
    }

    /**
     * Extend item class name
     * @param string $sClassName
     */
    protected function extendItemClass($sClassName)
    {
        $sClassName::extend(function ($obItem) {
            /** @var \Lovata\Toolbox\Classes\Item\ElementItem $obItem */
            $obItem->arRelationList['seo_param'] = [
                'class' => SeoParamItem::class,
                'field' => 'seo_param_id',
            ];
        });
    }
}
