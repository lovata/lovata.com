<?php namespace System\Facades;

use October\Rain\Support\Facade;

/**
 * Ui facade
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 *
 * @method static \System\Classes\UiManager\Button button(string $label)
 * @method static \System\Classes\UiManager\AjaxButton ajaxButton(string $label, string $ajaxHandler)
 * @method static \System\Classes\UiManager\PopupButton popupButton(string $label, string $ajaxHandler)
 * @method static \System\Classes\UiManager\FormToolbar formToolbar(callable $body)
 * @method static \System\Classes\UiManager\Callout callout(callable $body = null)
 * @method static \System\Classes\UiManager\SearchInput searchInput()
 */
class Ui extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @see \System\Classes\UiManager
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'system.ui';
    }
}
