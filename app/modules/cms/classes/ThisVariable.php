<?php namespace Cms\Classes;

use October\Rain\Extension\Extendable;
use October\Contracts\Twig\CallsAnyMethod;
use ArrayAccess;

/**
 * ThisVariable is a read-only container for accessing `{{ this }}` in Twig
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class ThisVariable extends Extendable implements CallsAnyMethod, ArrayAccess
{
    /**
     * @var array config values for this instance
     */
    public $config = [];

    /**
     * __construct
     */
    public function __construct($config = [])
    {
        $this->config = $config;

        parent::__construct();
    }

    /**
     * get an attribute from the element instance, with closure support
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->config)) {
            return value($this->config[$key]);
        }

        return value($default);
    }

    /**
     * __call handles dynamic calls to the element instance to set config.
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if ($controller = $this->get('controller')) {
            if (($partialObj = $controller->getPartialObject()) && $partialObj->methodExists($method)) {
                return $partialObj->$method(...$parameters);
            }

            if (($pageObj = $controller->getPageObject()) && $pageObj->methodExists($method)) {
                return $pageObj->$method(...$parameters);
            }

            if (($layoutObj = $controller->getLayoutObject()) && $layoutObj->methodExists($method)) {
                return $layoutObj->$method(...$parameters);
            }
        }

        return $this;
    }

    /**
     * offsetExists determines if the given offset exists.
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->config[$offset]);
    }

    /**
     * offsetGet gets the value for a given offset.
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * offsetSet does nothing, read-only.
     * @param  string  $offset
     * @param  mixed  $value
     */
    public function offsetSet($offset, $value): void
    {
    }

    /**
     * offsetUnset does nothing, read-only.
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
    }

    /**
     * __toString as an exception handler.
     */
    public function __toString()
    {
        return '';
    }
}
