<?php namespace Backend\Traits;

use Log;
use System\Classes\ErrorHandler;
use October\Rain\Exception\ApplicationException;
use Illuminate\Validation\ValidationException;

/**
 * ErrorMaker Trait adds exception based methods to a class, goes well with `System\Traits\ViewMaker`
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
trait ErrorMaker
{
    /**
     * @var string|null fatalError stores the object used for a fatal error.
     */
    protected $fatalError;

    /**
     * hasFatalError returns true if a fatal error has been set.
     */
    public function hasFatalError()
    {
        return !is_null($this->fatalError);
    }

    /**
     * getFatalError returns error message
     */
    public function getFatalError()
    {
        return $this->fatalError;
    }

    /**
     * handleError sets standard page variables in the case of a controller error.
     */
    public function handleError($exception)
    {
        if (
            !$exception instanceof ApplicationException &&
            !$exception instanceof ValidationException
        ) {
            Log::error($exception);
        }

        $errorMessage = ErrorHandler::getDetailedMessage($exception);
        $this->fatalError = $errorMessage;
        $this->vars['fatalError'] = $errorMessage;
    }
}
