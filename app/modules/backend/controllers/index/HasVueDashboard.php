<?php namespace Backend\Controllers\Index;

use App;
use Lang;
use Config;
use Backend;
use Request;
use Response;
use Backend\Classes\DashboardManager;
use Backend\Classes\ReportDataSourceManager;
use Backend\Models\Dashboard;
use ApplicationException;
use SystemException;

/**
 * HasVueDashboard in the controller
 */
trait HasVueDashboard
{
    /**
     * usingVueDashboard
     */
    protected function usingVueDashboard(): bool
    {
        // Beta Warning: Enable this configuration to access the dashboard early,
        // however, be aware that the interface for this implementation may change.
        return Config::get('backend.vue_dashboard', false);
    }

    /**
     * actionVueDashIndex
     */
    public function actionVueDashIndex()
    {
        $dashboards = Dashboard::listUserDashboards($this->user);
        $this->addJsBundle('/modules/backend/assets/js/vendor/daterangepicker/daterangepicker.js');
        $this->addCssBundle('/modules/backend/assets/js/vendor/daterangepicker/daterangepicker.css');

        $this->addJsBundle('/modules/backend/assets/js/dashboard.helpers.js');
        $this->addJsBundle('/modules/backend/assets/js/dashboard.store.js');
        $this->addJsBundle('/modules/backend/assets/js/dashboard.page.js');
        $this->registerVueComponent(\Backend\VueComponents\Inspector::class);
        $this->registerVueComponent(\Backend\VueComponents\Dashboard::class);

        $widgetManager = DashboardManager::instance();
        $widgetClasses = $widgetManager->listWidgetClasses();
        foreach ($widgetClasses as $widgetClassName) {
            $this->registerVueComponent($widgetClassName);
        }

        $this->vars['initialState'] = $this->loadInitialState($dashboards);
    }

    /**
     * export dashboard action
     */
    public function export($dashboard)
    {
        if (!Dashboard::userHasEditDashboardPermissions($this->user)) {
            throw new SystemException(Lang::get('backend::lang.dashboard.no_edit_permissions'));
        }

        return Response::make(
            Dashboard::getConfigurationAsJson($dashboard),
            200,
            [
                'Content-Type' => 'application/json',
                'Content-Disposition' => sprintf('%s; filename="%s"', 'attachment', $dashboard . '.json')
            ]
        );
    }

    /**
     * onUpload
     */
    public function onUploadDashboard()
    {
        $file = Request::file('file');
        if (!$file || !$file->isValid()) {
            throw new ApplicationException("Error importing the dashboard");
        }

        $content = file_get_contents($file->getRealPath());
        $slug = Dashboard::import($content, $this->user->id);
        unlink($file->getRealPath());

        return [
            'dashboards' => $this->getUserDashboardList(),
            'slug' => $slug
        ];
    }

    /**
     * loadInitialState for the dashboards
     */
    protected function loadInitialState($dashboards)
    {
        $dashboards = array_map(
            function($dashboard) {
                return $dashboard->getStateProps();
            },
            $dashboards
        );

        $widgetManager = DashboardManager::instance();

        $defaultWidgetConfigs = ReportDataSourceManager::instance()->getDefaultWidgetConfigs();
        return [
            "locale" => App::getLocale(),
            "colors" => [
                '#ef4444' => Lang::get('backend::lang.dashboard.colors.red'),
                '#f97316' => Lang::get('backend::lang.dashboard.colors.orange'),
                '#f59e0b' => Lang::get('backend::lang.dashboard.colors.amber'),
                '#84cc16' => Lang::get('backend::lang.dashboard.colors.lime'),
                '#22c55e' => Lang::get('backend::lang.dashboard.colors.green'),
                '#14b8a6' => Lang::get('backend::lang.dashboard.colors.teal'),
                '#06b6d4' => Lang::get('backend::lang.dashboard.colors.cyan'),
                '#0ea5e9' => Lang::get('backend::lang.dashboard.colors.sky'),
                '#3b82f6' => Lang::get('backend::lang.dashboard.colors.blue'),
                '#6366f1' => Lang::get('backend::lang.dashboard.colors.indigo'),
                '#8b5cf6' => Lang::get('backend::lang.dashboard.colors.violet'),
                '#ec4899' => Lang::get('backend::lang.dashboard.colors.pink'),
                '#f43f5e' => Lang::get('backend::lang.dashboard.colors.rose'),
                '#64748b' => Lang::get('backend::lang.dashboard.colors.slate')
            ],
            "dashboards" => $dashboards,
            "canCreateAndEdit" => Dashboard::userHasEditDashboardPermissions($this->user),
            "inspectorConfigs" => $this->loadGlobalInspectorConfigs(),
            "defaultWidgetConfigs" => $defaultWidgetConfigs,
            "exportUrl" => Backend::url('backend/index/export'),
            "customWidgetGroups" => $widgetManager->listVueWidgetGroups()
        ];
    }

    /**
     * onGetWidgetData
     */
    public function onGetWidgetData()
    {
        $handler = new DashboardHandler();

        $dateStart = post('date_start');
        $dateEnd = post('date_end');
        $compare = post('compare');

        $data = $handler->getWidgetData(
            $this,
            post('widget_config'),
            $dateStart,
            $dateEnd,
            post('aggregation_interval'),
            !!post('reset_cache'),
            post('dimension'),
            post('metrics'),
            post('extra_data', []),
            $compare
        );

        return $data;
    }

    /**
     * onGetWidgetCustomData
     */
    public function onGetWidgetCustomData()
    {
        $handler = new DashboardHandler();

        $dateStart = post('date_start');
        $dateEnd = post('date_end');
        $compare = post('compare');

        $data = $handler->onGetWidgetCustomData(
            $this,
            post('widget_config'),
            $dateStart,
            $dateEnd,
            post('aggregation_interval'),
            !!post('reset_cache'),
            post('extra_data', []),
            $compare
        );

        return $data;
    }

    /**
     * onSaveDashboard handler
     */
    public function onSaveDashboard()
    {
        if (!Dashboard::userHasEditDashboardPermissions($this->user)) {
            throw new ApplicationException(Lang::get('backend::lang.dashboard.no_edit_permissions'));
        }

        Dashboard::updateDashboard(
            trim(post('slug')),
            trim(post('definition'))
        );
    }

    /**
     * onCreateDashboard handler
     */
    public function onCreateDashboard()
    {
        if (!Dashboard::userHasEditDashboardPermissions($this->user)) {
            throw new ApplicationException(Lang::get('backend::lang.dashboard.no_edit_permissions'));
        }

        Dashboard::createDashboard(
            trim(post('name')),
            trim(post('slug')),
            trim(post('icon')),
            $this->user->id,
            !!post('global_access'),
        );

        return [
            'dashboards' => $this->getUserDashboardList()
        ];
    }

    /**
     * onUpdateDashboardConfig handler
     */
    public function onUpdateDashboardConfig()
    {
        if (!Dashboard::userHasEditDashboardPermissions($this->user)) {
            throw new ApplicationException(Lang::get('backend::lang.dashboard.no_edit_permissions'));
        }

        Dashboard::updateDashboardConfig(
            trim(post('original_slug')),
            trim(post('name')),
            trim(post('slug')),
            trim(post('icon')),
            !!post('global_access'),
        );

        return [
            'dashboards' => $this->getUserDashboardList()
        ];
    }

    /**
     * onDeleteDashboard handler
     */
    public function onDeleteDashboard()
    {
        if (!Dashboard::userHasEditDashboardPermissions($this->user)) {
            throw new ApplicationException(Lang::get('backend::lang.dashboard.no_edit_permissions'));
        }

        Dashboard::deleteDashboard(trim(post('slug')));

        return [
            'dashboards' => $this->getUserDashboardList()
        ];
    }

    /**
     * onRunDataSourceHandler handler
     */
    public function onRunDataSourceHandler()
    {
        $handler = new DashboardHandler();

        $data = $handler->runDataSourceHandler(
            $this,
            post('handler'),
            post('widget_config')
        );

        return $data;
    }

    /**
     * onRunCustomWidgetHandler handler
     */
    public function onRunCustomWidgetHandler()
    {
        $handler = new DashboardHandler();

        $data = $handler->runCustomWidgetHandler(
            $this,
            post('handler'),
            post('widget_config'),
            post('extra_data', []),
        );

        return $data;
    }

    /**
     * getUserDashboardList
     */
    protected function getUserDashboardList()
    {
        $dashboards = Dashboard::listUserDashboards($this->user);
        $dashboards = array_map(
            function($dashboard) {
                return $dashboard->getStateProps();
            },
            $dashboards
        );

        return $dashboards;
    }

    /**
     * loadGlobalInspectorConfigs
     */
    protected function loadGlobalInspectorConfigs()
    {
        $path = __DIR__.'/../index/inspector-configs.json';
        $contents = json_decode(file_get_contents($path), true);

        array_walk_recursive($contents, function(&$value) {
            if (is_string($value)) {
                $value = trans($value);
            }
        });

        return $contents;
    }
}
