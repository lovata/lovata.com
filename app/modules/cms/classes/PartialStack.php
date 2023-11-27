<?php namespace Cms\Classes;

/**
 * PartialStack manager for stacking nested partials and keeping track
 * of their components. Partial "objects" store the components
 * used by that partial for deferred retrieval.
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class PartialStack
{
    /**
     * @var array activePartial object being rendered.
     */
    protected $activePartial;

    /**
     * @var array partialStack of previously rendered partial objects.
     */
    protected $partialStack = [];

    /**
     * stackPartial is the partial entry point, appends a new partial to the stack.
     */
    public function stackPartial()
    {
        if ($this->activePartial !== null) {
            array_unshift($this->partialStack, $this->activePartial);
        }

        $this->activePartial = [
            'components' => [],
            'obj' => null
        ];
    }

    /**
     * unstackPartial is the partial exit point, removes the active partial from the stack.
     */
    public function unstackPartial()
    {
        $this->activePartial = array_shift($this->partialStack);
    }

    /**
     * getPartialObj
     */
    public function getPartialObj()
    {
        return $this->activePartial['obj'] ?? null;
    }

    /**
     * addPartialObj
     */
    public function addPartialObj($partialObj)
    {
        $this->activePartial['obj'] = $partialObj;
    }

    /**
     * findHandlerFromStack
     */
    public function findPartialByHandler($handler)
    {
        if (!$this->activePartial) {
            return null;
        }

        $partialObj = $this->activePartial['obj'] ?? null;
        if (method_exists($partialObj, $handler)) {
            return $partialObj;
        }

        foreach ($this->partialStack as $stack) {
            $partialObj = $stack['obj'] ?? null;
            if (method_exists($partialObj, $handler)) {
                return $partialObj;
            }
        }

        return null;
    }

    /**
     * addComponent to the active partial stack.
     */
    public function addComponent($alias, $componentObj)
    {
        array_push($this->activePartial['components'], [
            'name' => $alias,
            'obj' => $componentObj
        ]);
    }

    /**
     * getComponent returns a component by its alias from the partial stack.
     */
    public function getComponent($name)
    {
        if (!$this->activePartial) {
            return null;
        }

        $component = $this->findComponentFromStack($name, $this->activePartial);
        if ($component !== null) {
            return $component;
        }

        foreach ($this->partialStack as $stack) {
            $component = $this->findComponentFromStack($name, $stack);
            if ($component !== null) {
                return $component;
            }
        }

        return null;
    }

    /**
     * findComponentFromStack locates a component by its alias from the supplied stack.
     */
    protected function findComponentFromStack($name, $stack)
    {
        foreach ($stack['components'] as $componentInfo) {
            if ($componentInfo['name'] == $name) {
                return $componentInfo['obj'];
            }
        }

        return null;
    }
}
