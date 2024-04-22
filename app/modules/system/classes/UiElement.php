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
    public function __construct($body = null)
    {
        if (
            is_string($body) ||
            $body instanceof Closure ||
            $body instanceof UiElement
        ) {
            $this->body($body);
        }

        parent::__construct([]);
    }
}
