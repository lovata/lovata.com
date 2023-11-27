<?php namespace Cms\Classes;

use Throwable;

/**
 * PartialWatcher instructs the controller to capture output and AJAX handlers
 * from a complete page cycle. Either set to a partial name to ensure the partial
 * contents are available or true to just activate the cycle.
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class PartialWatcher
{
    /**
     * @var bool|string partialName mode.
     */
    protected $partialName;

    /**
     * @var array partialList for AJAX updates.
     */
    protected $partialList;

    /**
     * @var array partialOutput
     */
    protected $partialOutput;

    /**
     * @var array handlerOutput
     */
    protected $handlerOutput;

    /**
     * @var Throwable handlerException
     */
    protected $handlerException;

    /**
     * startCapture begins capturing partial processes.
     */
    public function startCapture($partialName = true, $partialList = [])
    {
        $this->partialName = $partialName;
        $this->partialList = $partialList;
    }

    /**
     * isWatching checks if this partial is relevant to the request
     */
    public function isWatching($name)
    {
        return $name === $this->partialName || in_array($name, $this->partialList);
    }

    /**
     * setPartialContents stores partials that the watcher cares about
     */
    public function setPartialContents($name, $contents)
    {
        if ($this->isWatching($name)) {
            $this->partialOutput[$name] = $contents;
        }
    }

    /**
     * getPartialContents
     */
    public function getPartialContents($name)
    {
        return $this->partialOutput[$name] ?? null;
    }

    /**
     * isWatchingHandler
     */
    public function isWatchingHandler($name)
    {
        if (!$this->isWatching($name)) {
            return false;
        }

        return $this->handlerOutput === null;
    }

    /**
     * setHandlerResponse
     */
    public function setHandlerResponse($contents)
    {
        $this->handlerOutput = $contents;
    }

    /**
     * getHandlerResponse
     */
    public function getHandlerResponse()
    {
        return $this->handlerOutput;
    }

    /**
     * setHandlerException
     */
    public function setHandlerException(Throwable $exception)
    {
        $this->handlerException = $exception;
    }

    /**
     * getHandlerException
     */
    public function getHandlerException(): ?Throwable
    {
        return $this->handlerException;
    }
}
