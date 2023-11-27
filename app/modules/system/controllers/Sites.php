<?php namespace System\Controllers;

use Config;
use System;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use System\Models\SiteDefinition;

/**
 * Sites controller
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class Sites extends Controller
{
    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class,
    ];

    /**
     * @var array `FormController` configuration.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var array `ListController` configuration.
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array requiredPermissions to view this page.
     */
    public $requiredPermissions = ['settings.manage_sites'];

    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('October.System', 'sites');
    }

    /**
     * index
     */
    public function index($tab = null)
    {
        SiteDefinition::syncPrimarySite();

        $this->asExtension('ListController')->index();
    }

    /**
     * formExtendFields adds available permission fields to the User form.
     * Mark default groups as checked for new Users.
     */
    public function formExtendFields($form)
    {
        $model = $form->model;

        if (!$model->is_primary) {
            $form->getField('_primary_site_hint')->hidden();
        }

        if ($model->isCustomLocale($model->locale)) {
            $form->getField('locale')->value('custom');
            $form->getField('_custom_locale')->value($model->locale);
        }

        $form->getField('app_url')->comment(sprintf(__('Current default value: :value', ['value' => '<strong>%s</strong>']), Config::get('app.url')))->commentHtml();
        $form->getField('theme')->comment(sprintf(__('Current default value: :value', ['value' => '<strong>%s</strong>']), Config::get('cms.active_theme', 'demo')))->commentHtml();
        $form->getField('timezone')->comment(sprintf(__('Current default value: :value', ['value' => '<strong>%s</strong>']), Config::get('cms.timezone', 'UTC')))->commentHtml();
        $form->getField('locale')->comment(sprintf(__('Current default value: :value', ['value' => '<strong>%s</strong>']), Config::get('app.original_locale', Config::get('app.locale', 'en'))))->commentHtml();

        // Remove themes without module
        if (!System::hasModule('Cms')) {
            $form->removeField('theme');
        }
    }

    /**
     * listExtendColumns modifies columns based on dynamic logic.
     */
    public function listExtendColumns($list)
    {
        // Remove themes without module
        if (!System::hasModule('Cms')) {
            $list->removeColumn('theme');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function listInjectRowClass($record, $definition = null)
    {
        $classes = [];

        if (!$record->is_enabled) {
            $classes[] = 'disabled';
        }

        if ($record->is_primary) {
            $classes[] = 'important';
        }

        if (count($classes) > 0) {
            return join(' ', $classes);
        }
    }

    /**
     * formBeforeSave
     */
    public function formBeforeSave($model)
    {
        if (post('SiteDefinition[locale]') === 'custom') {
            $this->formSetSaveValue('locale', post('SiteDefinition[_custom_locale]'));
        }

        if (post('SiteDefinition[is_enabled]')) {
            $this->formSetSaveValue('is_enabled_edit', true);
        }
    }
}
