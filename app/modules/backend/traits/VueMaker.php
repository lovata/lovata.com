<?php namespace Backend\Traits;

use Str;
use SystemException;
use Backend\Classes\VueComponentBase;

/**
 * VueMaker Trait adds Vue based methods to a class
 *
 * To add a component call the `registerVueComponent` method:
 *
 *     $this->registerVueComponent('Plugin/VueComponents/MyComponent');
 *
 * This will automatically load the component's JavaScript definition,
 * component template, and CSS file.
 *
 * @see Backend\Classes\VueComponentBase
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
trait VueMaker
{
    /**
     * @var array vueComponents contains Vue component classes
     */
    protected $vueComponents = [];

    /**
     * registerDefaultVueComponents
     */
    public function registerDefaultVueComponents()
    {
        $this->registerVueComponent(\Backend\VueComponents\Modal::class);
    }

    /**
     * registerVueComponent to be loaded when the action view renders.
     * @param string $className
     */
    public function registerVueComponent($className)
    {
        $component = $this->makeVueComponent($className);
        $this->vueComponents[] = $component;

        $requiredComponents = $component->getDependencies();
        if (!is_array($requiredComponents)) {
            throw new SystemException(sprintf('getDependencies() must return an array: %s', $className));
        }

        foreach ($requiredComponents as $className) {
            if (!$this->isVueComponentRegistered($className)) {
                $this->registerVueComponent($className);
            }
        }
    }

    /**
     * outputVueComponentTemplates
     */
    public function outputVueComponentTemplates()
    {
        $result = [];

        foreach ($this->vueComponents as $component) {
            $templateId = Str::getClassId($component);
            $result[] = sprintf('<script type="text/template" id="%s">', $templateId);
            $result[] = $component->render();
            $result[] = '</script>';

            foreach ($component->getSubcomponents() as $subcomponent) {
                $templateId = Str::getClassId($component).'_'.$subcomponent;
                $templateId = str_replace('.', '_', $templateId);
                $result[] = sprintf('<script type="text/template" id="%s">', $templateId);
                $result[] = $component->renderSubcomponent($subcomponent);
                $result[] = '</script>';
            }
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * makeVueComponent
     */
    protected function makeVueComponent($className)
    {
        if (!class_exists($className)) {
            throw new SystemException(sprintf('Vue component class not found: %s', $className));
        }

        $component = new $className($this);
        if (!$component instanceof VueComponentBase) {
            throw new SystemException(
                sprintf('Vue component class must be a descendant of Backend\Classes\VueComponentBase: %s', $className)
            );
        }

        return $component;
    }

    /**
     * isVueComponentRegistered
     */
    protected function isVueComponentRegistered($className)
    {
        foreach ($this->vueComponents as $component) {
            if ($className == get_class($component)) {
                return true;
            }
        }

        return false;
    }
}
