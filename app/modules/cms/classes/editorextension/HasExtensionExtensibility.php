<?php namespace Cms\Classes\EditorExtension;

use Cms\Classes\EditorExtension;
use Backend\Helpers\Inspector as InspectorHelper;

/**
 * HasExtensionExtensibility implements CMS extensibility features
 */
trait HasExtensionExtensibility
{
    /**
     * loadAndExtendCmsSettingsFields
     */
    protected function loadAndExtendCmsSettingsFields($fieldsClass, $entityName)
    {
        $settings = $this->loadSettingsFields($fieldsClass);

        $dataHolder = (object) ['settings' => [], 'templateType' => $entityName];

        /**
         * @event cms.template.extendTemplateSettingsFields
         * Fires after CMS Editor (page|partial|layout) Settings configuration is loaded and provides an opportunity to extend the standard template Settings popup. `$dataHolder` = {settings: [], templateType: string}
         *
         * Example usage:
         *
         *     Event::listen('cms.template.extendTemplateSettingsFields', function ((\Cms\Classes\EditorExtension) $extension, (object) $dataHolder) {
         *         // Make some modifications to the $dataHolder->settings array
         *     });
         */
        $this->fireSystemEvent('cms.template.extendTemplateSettingsFields', [$dataHolder]);

        array_walk_recursive($dataHolder->settings, function(&$value, $key) {
            if (is_string($value)) {
                $value = trans($value);
            }
        });

        foreach ($dataHolder->settings as $propertyDefinition) {
            $propertyDefinition['property'] = 'settings.'.$propertyDefinition['property'];
            $settings[] = $propertyDefinition;
        }

        return $settings;
    }

    /**
     * getToolbarCustomSettingsButtons
     */
    protected function getToolbarCustomSettingsButtons()
    {
        return [
            EditorExtension::DOCUMENT_TYPE_PAGE => $this->getTemplateToolbarCustomSettingsButtons('page'),
            EditorExtension::DOCUMENT_TYPE_PARTIAL => $this->getTemplateToolbarCustomSettingsButtons('partial'),
            EditorExtension::DOCUMENT_TYPE_LAYOUT => $this->getTemplateToolbarCustomSettingsButtons('layout')
        ];
    }

    /**
     * getTemplateToolbarCustomSettingsButtons
     */
    protected function getTemplateToolbarCustomSettingsButtons($entityName)
    {
        $buttons = [];
        if ($entityName === 'partial') {
            $buttons = $this->loadCustomSettingsButtons(\Cms\Classes\Partial\Fields::class);
        }

        $dataHolder = (object) ['buttons' => [], 'templateType' => $entityName];

        /**
         * @event cms.template.getTemplateToolbarSettingsButtons
         * Provides an opportunity to extend a CMS template toolbar with custom settings buttons. `$dataHolder` = {buttons: [], templateType: string}
         *
         * Example usage:
         *
         *     Event::listen('cms.template.getTemplateToolbarSettingsButtons', function ((\Cms\Classes\EditorExtension) $extension, (object) $dataHolder) {
         *         // Make some modifications to the $dataHolder->buttons array
         *     });
         */
        $this->fireSystemEvent('cms.template.getTemplateToolbarSettingsButtons', [$dataHolder]);

        array_walk_recursive($dataHolder->buttons, function(&$value, $key) {
            if (is_string($value)) {
                $value = trans($value);
            }
        });

        foreach ($dataHolder->buttons as &$buttonDefinition) {
            if (!array_key_exists('useViewBag', $buttonDefinition)) {
                $buttonDefinition['useViewBag'] = true;
            }

            if ($buttonDefinition['useViewBag']) {
                continue;
            }

            if (!is_array($buttonDefinition['properties'])) {
                continue;
            }

            foreach ($buttonDefinition['properties'] as &$property) {
                $property['property'] = 'settings.'.$property['property'];
            }
        }

        foreach ($dataHolder->buttons as &$buttonDefinition) {
            $buttons[] = $buttonDefinition;
        }

        return $buttons;
    }

    /**
     * loadCustomSettingsButtons
     * @see \Editor\Classes\ExtensionBase::loadSettingsFields()
     */
    protected function loadCustomSettingsButtons(string $fieldsClass): array
    {
        $buttons = (new $fieldsClass)->defineSettingsButtons();

        foreach ($buttons as &$button) {
            $button['properties'] = InspectorHelper::getPropertyConfig($button['properties'] ?? []);
        }

        return $buttons;
    }
}
