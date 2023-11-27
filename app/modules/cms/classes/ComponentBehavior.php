<?php namespace Cms\Classes;

use October\Rain\Extension\ExtensionBase;

/**
 * ComponentBehavior base class
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class ComponentBehavior extends ExtensionBase
{
    /**
     * @var ComponentBase component class
     */
    protected $component;

    /**
     * @var \Cms\Classes\Controller controller to CMS controller.
     */
    protected $controller;

    /**
     * __construct the behavior
     */
    public function __construct($component)
    {
        $this->component = $component;

        $this->controller = $controller = $component->getController();

        if (!$controller) {
            return;
        }

        // Constructor logic that is protected by authentication
        $controller->bindEvent('page.initComponents', function() {
            $this->beforeDisplay();
        });
    }

    /**
     * beforeDisplay fires before the page is displayed and AJAX is executed.
     */
    public function beforeDisplay()
    {
    }
}
