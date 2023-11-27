<?php namespace Cms\Twig;

use App;
use Cms;
use Block;
use Event;
use Response;
use Cms\Classes\PageManager;
use Cms\Classes\Controller;
use Cms\Classes\ThisVariable;
use Twig\Environment as TwigEnvironment;
use Twig\TwigFilter as TwigSimpleFilter;
use Twig\TwigFunction as TwigSimpleFunction;
use Twig\Extension\AbstractExtension as TwigExtension;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Extension implements the basic CMS Twig functions and filters.
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class Extension extends TwigExtension
{
    /**
     * @var \Cms\Classes\Controller controller reference
     */
    protected $controller;

    /**
     * __construct the extension instance.
     */
    public function __construct(Controller $controller = null)
    {
        $this->controller = $controller;
    }

    /**
     * addExtensionToTwig adds this extension to the Twig environment and also
     * creates a hook for others.
     */
    public static function addExtensionToTwig(TwigEnvironment $twig, Controller $controller = null)
    {
        $twig->addExtension(new static($controller));

        /**
         * @event cms.extendTwig
         * Provides an opportunity to extend the Twig environment used by the CMS
         *
         * Example usage:
         *
         *     Event::listen('system.extendTwig', function ((Twig\Environment) $twig) {
         *         $twig->addExtension(new \Twig\Extension\StringLoaderExtension);
         *     });
         *
         */
        Event::fire('cms.extendTwig', [$twig]);
    }

    /**
     * getFunctions returns a list of functions to add to the existing list.
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigSimpleFunction('page', [$this, 'pageFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('partial', [$this, 'partialFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('hasPartial', [$this, 'hasPartialFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('content', [$this, 'contentFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('hasContent', [$this, 'hasContentFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('component', [$this, 'componentFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('placeholder', [$this, 'placeholderFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('hasPlaceholder', [$this, 'hasPlaceholderFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('ajaxHandler', [$this, 'ajaxHandlerFunction'], []),
            new TwigSimpleFunction('response', [$this, 'responseFunction'], []),
            new TwigSimpleFunction('redirect', [$this, 'redirectFunction'], []),
            new TwigSimpleFunction('abort', [$this, 'abortFunction'], []),
        ];
    }

    /**
     * getFilters returns a list of filters this extension provides.
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigSimpleFilter('page', [$this, 'pageFilter'], ['is_safe' => ['html']]),
            new TwigSimpleFilter('theme', [$this, 'themeFilter'], ['is_safe' => ['html']]),
            new TwigSimpleFilter('content', [$this, 'contentFilter'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * getTokenParsers returns a list of token parsers this extension provides.
     * @return array
     */
    public function getTokenParsers()
    {
        return [
            new PageTokenParser,
            new PartialTokenParser,
            new AjaxPartialTokenParser,
            new ContentTokenParser,
            new PutTokenParser,
            new PlaceholderTokenParser,
            new DefaultTokenParser,
            new FrameworkTokenParser,
            new ComponentTokenParser,
            new FlashTokenParser,
            new ScriptsTokenParser,
            new StylesTokenParser,
            new MetaTokenParser,
        ];
    }

    /**
     * getNodeVisitors returns a list of node visitors this extension provides.
     * @return array
     */
    public function getNodeVisitors()
    {
        return [
            new GetAttrAdjuster
        ];
    }

    /**
     * pageFunction renders a page.
     * This function should be used in the layout code to output the requested page.
     * @param array|null $context
     * @return string
     */
    public function pageFunction($context = null)
    {
        if ($this->controller->getLayout()->isPriority() && is_array($context)) {
            $this->controller->vars += $context;
        }

        return $this->controller->renderPage();
    }

    /**
     * partialFunction renders a partial based on the partial name. The parameters
     * are an optional list of view variables. An exception can be thrown if
     * nothing is found.
     * @return string
     */
    public function partialFunction($name, $parameters = [], $throwException = false)
    {
        return $this->controller->renderPartial($name, $parameters, $throwException);
    }

    /**
     * hasPartialFunction checks the partials existence without rendering it.
     * @return bool
     */
    public function hasPartialFunction($name)
    {
        return (bool) $this->controller->loadPartialObject($name);
    }

    /**
     * contentFunction renders a partial based on the file name. The parameters
     * are an optional list of view variables, otherwise pass false to render nothing
     * and check the existence. An exception can be thrown if nothing is found.
     * @return string
     */
    public function contentFunction($name, $parameters = [], $throwException = false)
    {
        return $this->controller->renderContent($name, $parameters, $throwException);
    }

    /**
     * hasContentFunction checks the content existence without rendering it.
     * @return bool
     */
    public function hasContentFunction($name)
    {
        return (bool) $this->controller->loadContentObject($name);
    }

    /**
     * componentFunction renders a component's default content.
     * @param string $name Specifies the component name.
     * @param array $parameters A optional list of parameters to pass to the component.
     * @return string
     */
    public function componentFunction($name, $parameters = [])
    {
        return $this->controller->renderComponent($name, $parameters);
    }

    /**
     * assetsFunction renders registered assets of a given type
     * @return string
     */
    public function assetsFunction($type = null)
    {
        $result = $this->controller->makeAssets($type);

        Event::fire('cms.assets.render', [$type, &$result]);

        return $result;
    }

    /**
     * placeholderFunction renders a placeholder content, without removing the block,
     * must be called before the placeholder tag itself
     * @return string
     */
    public function placeholderFunction($name, $default = null)
    {
        if (($result = Block::get($name)) === null) {
            return null;
        }

        $result = str_replace('<!-- X_OCTOBER_DEFAULT_BLOCK_CONTENT -->', trim($default), $result);

        return $result;
    }

    /**
     * hasPlaceholderFunction checks that a placeholder exists without rendering it
     */
    public function hasPlaceholderFunction($name)
    {
        return Block::has($name);
    }

    /**
     * ajaxHandlerFunction runs an ajax handler
     * @param string $name
     */
    public function ajaxHandlerFunction($name = '')
    {
        return $this->controller->runAjaxHandlerAsResponse($name);
    }

    /**
     * responseFunction returns a new response from the application.
     * @param \Illuminate\Contracts\View\View|string|array|null $content
     * @param int|null $status
     * @param array $headers
     */
    public function responseFunction($content = '', $status = null, array $headers = [])
    {
        if ($content instanceof \Illuminate\Contracts\Support\Responsable) {
            $response = $content->toResponse(App::make('request'));
        }
        elseif ($content instanceof \Symfony\Component\HttpFoundation\Response) {
            $response = $content;
        }
        else {
            $response = Response::make($content, $status ?: 200, $headers);
        }

        if ($status !== null) {
            $response->setStatusCode($status);
        }

        // Allow headers and interception from Response Maker
        $response = $this->controller->makeResponse($response);

        throw new HttpResponseException($response);
    }

    /**
     * redirectFunction will redirect the response to a theme page or URL
     * @param string $to
     * @param int $code
     */
    public function redirectFunction($to, $parameters = [], $code = 302)
    {
        throw new HttpResponseException(Cms::redirect($to, $parameters, $code));
    }

    /**
     * abortFunction will abort the successful page cycle
     * @param int $code
     * @param string|false $message
     */
    public function abortFunction($code, $message = '')
    {
        if ($message === false) {
            $this->controller->setStatusCode($code);
            return;
        }

        if ($code == 404) {
            throw new NotFoundHttpException($message);
        }

        throw new HttpException($code, $message);
    }

    /**
     * pageFilter looks up the URL for a supplied page name and returns it relative to the website root,
     * including route parameters. Parameters can be persisted from the current page parameters.
     * @param mixed $name
     * @param array $parameters
     * @param bool $routePersistence
     * @return string
     */
    public function pageFilter($name, $parameters = [], $routePersistence = true)
    {
        if ($name instanceof ThisVariable) {
            $name = '';
        }

        return $this->controller->pageUrl($name, $parameters, $routePersistence);
    }

    /**
     * themeFilter converts supplied URL to a theme URL relative to the website root. If the URL provided is an
     * array then the files will be combined.
     * @param mixed $url Specifies the theme-relative URL
     * @return string
     */
    public function themeFilter($url)
    {
        return $this->controller->themeUrl($url);
    }

    /**
     * contentFilter processes content for links and snippets
     * @param string $content
     * @return string
     */
    public function contentFilter($content)
    {
        return PageManager::processMarkup($content);
    }

    /**
     * startBlock opens a layout block.
     * @param string $name Specifies the block name
     */
    public function startBlock($name)
    {
        Block::startBlock($name);
    }

    /**
     * setBlock sets a block value as a variable.
     */
    public function setBlock(string $name, $value)
    {
        Block::set($name, $value);
    }

    /**
     * displayBlock returns a layout block contents and removes the block.
     * @param string $name Specifies the block name
     * @param string $default The default placeholder contents.
     * @return string|null
     */
    public function displayBlock($name, $default = null)
    {
        if (($result = Block::placeholder($name)) === null) {
            return $default;
        }

        /**
         * @event cms.block.render
         * Provides an opportunity to modify the rendered block content
         *
         * Example usage:
         *
         *     Event::listen('cms.block.render', function ((string) $name, (string) $result) {
         *         if ($name === 'myBlockName') {
         *             return 'my custom content';
         *         }
         *     });
         *
         */
        if ($event = Event::fire('cms.block.render', [$name, $result], true)) {
            $result = $event;
        }

        $result = str_replace('<!-- X_OCTOBER_DEFAULT_BLOCK_CONTENT -->', trim($default), $result);

        return $result;
    }

    /**
     * endBlock closes a layout block.
     */
    public function endBlock($append = true)
    {
        Block::endBlock($append);
    }
}
