<?php namespace Tailor\Classes;

use File;
use Exception;

/**
 * BlueprintException
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class BlueprintException extends Exception
{
    /**
     * __construct the exception class for blueprints
     * @param string $message Error message.
     * @param int $code Error code.
     * @param Exception $previous Previous exception.
     */
    public function __construct(Blueprint $blueprint, $message = "", $lineNo = 0, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->line = $lineNo;

        $this->file = $blueprint->getFilePath();
    }
}
