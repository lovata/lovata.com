<?php namespace Backend\Facades;

use October\Rain\Support\Facade;

/**
 * BackendMenu
 * @see \Backend\Classes\NavigationManager
 */
class BackendMenu extends Facade
{
    /**
     * getFacadeAccessor returns the registered name of the component
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'backend.menu';
    }
}
