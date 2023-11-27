<?php namespace System\Classes;

use View;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use ArrayAccess;

/**
 * PagerElement is an internal class returned by the pager() Twig function. It also acts as
 * an abstraction from Laravel's pagination features.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class PagerElement implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    /**
     * @var mixed $paginator
     */
    protected $paginator;

    /**
     * @var array config values for this instance
     */
    public $config = [];

    /**
     * @var array view variables for this instance
     */
    public $vars = [];

    /**
     * __construct
     */
    public function __construct($paginator, $config = [])
    {
        $this->paginator = $paginator;
        $this->config = $this->vars = $config;
        $this->preparePager();
    }

    /**
     * setArrayMode cleans up the variables for output as an array
     */
    public function setArrayMode()
    {
        $paginated = $this->paginator->toArray();

        $this->vars = [
            'links' => [
                'first' => $paginated['first_page_url'] ?? null,
                'last' => $paginated['last_page_url'] ?? null,
                'prev' => $paginated['prev_page_url'] ?? null,
                'next' => $paginated['next_page_url'] ?? null,
            ],
            'meta' => array_except($paginated, [
                'data',
                'first_page_url',
                'last_page_url',
                'prev_page_url',
                'next_page_url',
            ])
        ];
    }

    /**
     * preparePager
     */
    public function preparePager()
    {
        if (!$this->getConfig('template') || $this->getConfig('template') === 'array') {
            $this->setArrayMode();
        }

        if ($this->getConfig('withQuery') === true) {
            $this->paginator->withQueryString();
        }

        if ($appends = $this->getConfig('appends')) {
            $this->paginator->appends($appends);
        }

        if ($fragment = $this->getConfig('fragment')) {
            $this->paginator->fragment($fragment);
        }
    }

    /**
     * prepareVars for render
     */
    public function prepareVars()
    {
        $this->vars['paginator'] = $this->paginator;
        $this->vars['elements'] = $this->getPaginatorElements();
    }

    /**
     * render as a string
     */
    public function render()
    {
        $this->prepareVars();

        if (
            ($partial = $this->getConfig('partial')) &&
            ($controller = $this->getCmsController())
        ) {
            return $controller->renderPartial($partial, $this->vars, true);
        }

        return View::make($this->getPaginatorView(), $this->vars);
    }

    /**
     * getPaginatorView
     */
    protected function getPaginatorView()
    {
        $viewMap = [
            'default' => 'system::pagination.default',
            'simple' => 'system::pagination.simple',
            'ajax' => 'system::pagination.ajax',
            'loadmore' => 'system::pagination.loadmore'
        ];

        $defaultView = $this->paginator instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? 'default'
            : 'simple';

        $template = $this->getConfig('template', $defaultView);

        return $viewMap[$template] ?? $template;
    }

    /**
     * getPaginatorElements is needed because it is locked out in Laravel
     * @return array
     */
    protected function getPaginatorElements()
    {
        if (!$this->paginator instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return [];
        }

        $window = UrlWindow::make($this->paginator);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }

    /**
     * getCmsController
     */
    protected function getCmsController()
    {
        if (\App::runningInFrontend() && \System::hasModule('Cms')) {
            return \Cms\Classes\Controller::getController() ?: new \Cms\Classes\Controller;
        }
    }

    /**
     * getConfig
     */
    public function getConfig($name, $default = null)
    {
        return $this->config[$name] ?? $default;
    }

    /**
     * __toString
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * get an attribute from the element instance.
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->vars)) {
            return $this->vars[$key];
        }

        return $default;
    }

    /**
     * toArray converts the element instance to an array.
     * @return array
     */
    public function toArray()
    {
        return $this->vars;
    }

    /**
     * jsonSerialize converts the object into something JSON serializable.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * toJson converts the element instance to JSON.
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * offsetExists determines if the given offset exists.
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->vars[$offset]);
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
     * offsetSet is disabled (read-only)
     * @param  string  $offset
     * @param  mixed  $value
     */
    public function offsetSet($offset, $value): void
    {
    }

    /**
     * offsetUnset is disabled (read-only)
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
    }

    /**
     * __get dynamically retrieves the value of an attribute.
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * __set is disabled (read-only)
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
    }

    /**
     * __isset dynamically checks if an attribute is set.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * __unset is disabled (read-only)
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
    }
}
