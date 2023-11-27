<?php
declare(strict_types=1);

namespace Lovata\ViteJsintegration\Classes\Twig;

use Cms\Classes\Controller;
use Innocenzi\Vite\Configuration;
use Twig\Extension\AbstractExtension as TwigExtension;
use Twig\TwigFunction as TwigSimpleFunction;

final class Extension extends TwigExtension
{
    /**
     * @var \Cms\Classes\Controller controller reference
     */
    protected Controller $controller;

    /**
     * __construct the extension instance.
     */
    public function __construct(Controller $controller = null)
    {
        $this->controller = $controller;
    }

    /**
     * getFunctions returns a list of functions to add to the existing list.
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigSimpleFunction('vite', [$this, 'viteFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('vite_entry', [$this, 'viteEntryFunction'], []),
            new TwigSimpleFunction('vite_asset', [$this, 'viteAssetFunction'], []),
            new TwigSimpleFunction('vite_tag', [$this, 'viteTagFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('vite_tags', [$this, 'viteTagsFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('vite_client', [$this, 'viteClientFunction'], ['is_safe' => ['html']]),
        ];
    }

    public function viteFunction(?string $config = null): Configuration
    {
        return vite($config);
    }

    public function viteEntryFunction(string $path, ?string $configurationName = null): string
    {
        return vite_entry($path, $configurationName);
    }

    public function viteAssetFunction(string $path, ?string $configurationName = null): string
    {
        return vite_asset($path, $configurationName);
    }

    public function viteTagFunction(string $path, ?string $configurationName = null): string
    {
        return vite_tag($path, $configurationName);
    }

    public function viteTagsFunction(?string $configurationName = null): string
    {
        return vite_tags($configurationName);
    }

    public function viteClientFunction(?string $configurationName = null): string
    {
        return vite_client($configurationName);
    }
}
