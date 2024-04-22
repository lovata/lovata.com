<?php namespace System\Classes;

use App;
use BadMethodCallException;

/**
 * UiManager class manages UI elements
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class UiManager
{
    /**
     * @var array elements collection of UI elements
     */
    protected $elements;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->registerElement('button', \System\Classes\UiManager\Button::class);
        $this->registerElement('ajaxButton', \System\Classes\UiManager\AjaxButton::class);
        $this->registerElement('popupButton', \System\Classes\UiManager\PopupButton::class);
        $this->registerElement('searchInput', \System\Classes\UiManager\SearchInput::class);
        $this->registerElement('callout', \System\Classes\UiManager\Callout::class);
    }

    /**
     * instance creates a new instance of this singleton
     */
    public static function instance(): static
    {
        return App::make('system.ui');
    }

    /**
     * registerElement
     */
    public function registerElement(string $alias, string $class)
    {
        $this->elements[$alias] = $class;
    }

    /**
     * getElement
     */
    public function getElement(string $alias, array $args): ?UiElement
    {
        if ($this->hasElement($alias)) {
            return new $this->elements[$alias](...$args);
        }

        return null;
    }

    /**
     * hasElement
     */
    public function hasElement(string $alias): bool
    {
        return isset($this->elements[$alias]);
    }

    /**
     * __call dynamically handles calls into the query instance.
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $args)
    {
        if ($this->hasElement($method)) {
            return $this->getElement($method, $args);
        }

        throw new BadMethodCallException(sprintf(
            'There is no UI element registered as method %s::%s()',
            get_class($this),
            $method
        ));
    }
}
