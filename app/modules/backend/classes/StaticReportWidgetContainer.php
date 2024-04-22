<?php namespace Backend\Classes;

use Backend\Classes\ReportWidgetBase;
use Backend\Classes\Controller;
use SystemException;

/**
 * StaticReportWidgetContainer renders static report widgets.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class StaticReportWidgetContainer
{
    /**
     * @var \Backend\Classes\Controller controller for the backend.
     */
    protected $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Renders a static widget.
     * @param string $widgetClass Specifies the widget class name.
     * Widget classes must extend Backend\Classes\ReportWidgetBase.
     * @param array $widgetConfig Widget properties
     * @return string Returns the rendered widget string
     */
    public function renderWidget(string $widgetClass, array $widgetConfig): string
    {
        $widget = $this->makeWidget($widgetClass);
        $widget->setProperties($widgetConfig);

        return $widget->render();
    }

    /**
     * makeWidget
     */
    private function makeWidget(string $widgetClass)
    {
        if (!is_subclass_of($widgetClass, ReportWidgetBase::class)) {
            throw new SystemException("The provided class is not a report widget: " . $widgetClass);
        }

        return new $widgetClass($this->controller);
    }
}
