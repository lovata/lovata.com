<?php namespace Backend\Classes;

use App;
use Lang;
use SystemException;
use Backend\Classes\Controller;

/**
 * DashboardManager manages custom dashboard widgets.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class DashboardManager
{
    /**
     * @var string[]
     */
    protected $dashboardWidgets = [];

    /**
     * instance creates a new instance of this singleton
     */
    public static function instance(): static
    {
        return App::make('backend.dashboards');
    }

    /**
     * Registers a dashboard widget component.
     * @param string $className A class name of a dashboard widget.
     * The class must extend Backend\Classes\VueComponentBase
     * @param string $displayName A name to display in the user interface.
     * @param string $groupName A group name to use in the Create Widget menu.
     */
    public function registerWidget(string $className, string $displayName, string $groupName): void
    {
        $className = strtolower($className);
        $this->dashboardWidgets[$className] = [
            'displayName' => $displayName,
            'groupName' => $groupName
        ];
    }

    /**
     * Returns class names and registration parameters of registered dashboard widgets.
     * @return array
     */
    public function listWidgetClasses(): array
    {
        return array_keys($this->dashboardWidgets);
    }

    /**
     * listVueWidgetGroups returns Vue component, group and widget names.
     * @return array
     */
    public function listVueWidgetGroups(): array
    {
        $groups = [];
        foreach ($this->dashboardWidgets as $className => $params) {
            $group = Lang::get($params['groupName']);
            $groups[$group] ??= [];

            $componentName = strtolower(str_replace('\\', '-', $className));
            $groups[$group][] = [
                'type' => $componentName,
                'name' => Lang::get($params['displayName'])
            ];
        }

        return $groups;
    }

    /**
     * getWidget returns a dashboard widget instance by its class name.
     * @throws SystemException if the provided class name is not a subclass Backend\Classes\DashboardWidgetBase.
     * @param string $className A dashboard widget class name.
     * @param Controller $controller Parent controller instance.
     * @return ?DashboardWidgetBase Returns the dashboard widget instance or null.
     */
    public function getWidget(string $className, Controller $controller): ?DashboardWidgetBase
    {
        $className = strtolower($className);
        if (!array_key_exists($className, $this->dashboardWidgets)) {
            return null;
        }

        if (!is_subclass_of($className, DashboardWidgetBase::class)) {
            throw new SystemException("The provided class is not a dashboard widget: {$className}");
        }

        return new $className($controller);
    }
}
