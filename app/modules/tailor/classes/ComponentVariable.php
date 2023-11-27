<?php namespace Tailor\Classes;

use Config;
use Cms\Classes\ComponentBase;
use Illuminate\Support\Collection;
use Illuminate\Pagination\AbstractPaginator;
use October\Contracts\Twig\CallsAnyMethod;
use IteratorAggregate;
use JsonSerializable;
use ArrayAccess;
use Traversable;
use Exception;

/**
 * ComponentVariable is the read-only default variable for tailor components.
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class ComponentVariable implements IteratorAggregate, ArrayAccess, CallsAnyMethod, JsonSerializable
{
    use \Illuminate\Support\Traits\ForwardsCalls;

    /**
     * @var \Cms\Classes\ComponentBase component
     */
    protected $component;

    /**
     * @var \Model record
     */
    protected $record;

    /**
     * @var bool isRecordLoaded
     */
    protected $isRecordLoaded = false;

    /**
     * __construct
     */
    public function __construct(ComponentBase $component)
    {
        $this->component = $component;
    }

    /**
     * getComponent
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * getRecord
     */
    public function getRecord()
    {
        if (!$this->isRecordLoaded) {
            $this->loadRecord();
        }

        return $this->record;
    }

    /**
     * getRecordQuery
     */
    public function getRecordQuery()
    {
        try {
            return $this->component->getPrimaryRecordQuery();
        }
        catch (Exception $ex) {
            if (!Config::get('cms.strict_components', false)) {
                return null;
            }

            throw $ex;
        }
    }

    /**
     * loadRecord
     */
    protected function loadRecord()
    {
        try {
            $this->record = $this->component->getPrimaryRecord();
        }
        catch (Exception $ex) {
            if (!Config::get('cms.strict_components', false)) {
                return null;
            }

            throw $ex;
        }

        $this->isRecordLoaded = true;
    }

    /**
     * __get attributes on the component
     */
    public function __get($key)
    {
        if (!$this->isRecordLoaded) {
            $this->loadRecord();
        }

        if ($this->record && isset($this->record->$key)) {
            $value = $this->record->$key;
            if ($value instanceof Collection) {
                return $value->count() ? $value : [];
            }

            return $value;
        }

        return $this->component->$key;
    }

    /**
     * __isset forces explicit method calls
     */
    public function __isset($key)
    {
        return true;
    }

    /**
     * __call
     */
    public function __call($method, $parameters)
    {
        if ($query = $this->getRecordQuery()) {
            return $this->forwardCallTo($query->newQuery(), $method, $parameters);
        }

        return null;
    }

    /**
     * getIterator for the records, this allows empty checks.
     */
    public function getIterator(): Traversable
    {
        if (!$this->isRecordLoaded) {
            $this->loadRecord();
        }

        if ($this->record instanceof Collection || $this->record instanceof AbstractPaginator) {
            return $this->record->getIterator();
        }

        if ($this->record && $this->record->exists) {
            return new Collection([$this->record]);
        }

        return new Collection;
    }

    /**
     * offsetExists implementation
     */
    public function offsetExists($offset): bool
    {
        return isset($this->$offset);
    }

    /**
     * offsetGet implementation
     */
    public function offsetGet($offset): mixed
    {
        return $this->$offset;
    }

    /**
     * offsetSet is disabled (read-only)
     */
    public function offsetSet($offset, $value): void
    {
        return;
    }

    /**
     * offsetUnset is disabled (read-only)
     */
    public function offsetUnset($offset): void
    {
        return;
    }

    /**
     * toArray convert the variable to an array.
     * @return array
     */
    public function toArray()
    {
        return ($record = $this->getRecord()) ? $record->toArray() : [];
    }

    /**
     * jsonSerialize converts the object into something JSON serializable.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
