<?php namespace System\Classes;

use Closure;
use October\Rain\Element\ElementBase;

/**
 * UiElement
 *
 * @method UiElement body(callable|array|string $body) body contents for the element, optional.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class UiElement extends ElementBase
{
    use \System\Traits\ElementRenderer;

    /**
     * __construct
     */
    public function __construct($config = [])
    {
        if (
            is_string($config) ||
            $config instanceof Closure ||
            $config instanceof UiElement
        ) {
            $this->body($config);
            $config = [];
        }

        parent::__construct($config);
    }
}
