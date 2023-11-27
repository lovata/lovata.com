<?php namespace Tailor\Classes;

use Response;

/**
 * BlueprintErrorData wraps information about a blueprint error to be used on the client side.
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class BlueprintErrorData
{
    /**
     * @var string message The error message
     */
    protected $message;

    /**
     * @var int line The error line
     */
    protected $line;

    /**
     * @var int pos Position of the error in the line
     */
    protected $pos;

    /**
     * __construct
     */
    public function __construct($message, $line = null, $pos = null)
    {
        $this->message = $message;
        $this->line = $line;
        $this->pos = $pos;
    }

    /**
     * fromException
     */
    public static function fromException($ex)
    {
        return new self($ex->getMessage(), $ex->getLine());
    }

    /**
     * toResponse
     */
    public function toResponse()
    {
        return Response::json($this->asArray(), 406);
    }

    /**
     * asArray
     */
    protected function asArray()
    {
        return [
            'blueprintError' => [
                'message' => $this->message,
                'line' => $this->line,
                'pos' => $this->pos
            ]
        ];
    }
}
