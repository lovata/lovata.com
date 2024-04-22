<?php namespace Backend\Facades;

use October\Rain\Support\Facade;

/**
 * BackendAuth
 * @see \Backend\Classes\AuthManager
 */
class BackendAuth extends Facade
{
    /**
     * getFacadeAccessor returns the registered name of the component
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'backend.auth';
    }
}
