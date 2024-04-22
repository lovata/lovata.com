<?php namespace Backend\Controllers;

use Redirect;
use BackendAuth;
use BackendMenu;
use Backend\Classes\Controller;

/**
 * Index controller for the dashboard
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 *
 */
class Index extends Controller
{
    use \Backend\Traits\InspectableContainer;
    use \Backend\Controllers\Index\HasVueDashboard;

    /**
     * @var array requiredPermissions to view this page.
     * @see checkPermissionRedirect()
     */
    public $requiredPermissions = [];

    /**
     * @var bool turboVisitControl
     */
    public $turboVisitControl = 'reload';

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContextOwner('October.Backend');

        $this->addCss('/modules/backend/assets/css/dashboard/dashboard.css');
    }

    /**
     * index controller action
     */
    public function index()
    {
        if ($redirect = $this->checkPermissionRedirect()) {
            return $redirect;
        }

        if ($this->usingVueDashboard()) {
            $this->actionVueDashIndex();
        }
        else {
            $this->initReportContainer();
        }

        $this->pageTitle = 'backend::lang.dashboard.menu_label';

        BackendMenu::setContextMainMenu('dashboard');
    }

    /**
     * index_onInitReportContainer
     */
    public function index_onInitReportContainer()
    {
        $this->initReportContainer();

        return ['#dashReportContainer' => $this->widget->reportContainer->render()];
    }

    /**
     * initReportContainer prepares the report widget used by the dashboard
     * @param Model $model
     * @return void
     */
    protected function initReportContainer()
    {
        $widgetConfig = $this->makeConfig('config_dashboard.yaml');
        $widgetConfig->showConfigure = BackendAuth::userHasAccess('dashboard.manage');
        $widgetConfig->showAddRemove = BackendAuth::userHasAccess('dashboard.manage');
        $widgetConfig->showReorder = $widgetConfig->showConfigure || $widgetConfig->showAddRemove;
        $widgetConfig->showMakeDefault = BackendAuth::userHasAccess('dashboard.manage');

        $reportWidget = $this->makeWidget(\Backend\Widgets\ReportContainer::class, $widgetConfig);
        $reportWidget->bindToController();
    }

    /**
     * checkPermissionRedirect custom permissions check that will redirect to the next
     * available menu item, if permission to this page is denied.
     */
    protected function checkPermissionRedirect()
    {
        if ($this->user->hasAnyAccess(['dashboard', 'dashboard.*'])) {
            return;
        }

        if ($first = array_first(BackendMenu::listMainMenuItems())) {
            return Redirect::intended($first->url);
        }
    }
}
