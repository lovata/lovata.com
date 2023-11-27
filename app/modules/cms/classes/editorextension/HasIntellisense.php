<?php namespace Cms\Classes\EditorExtension;

/**
 * HasIntellisense provides data for the front-end CMS IntelliSense feature.
 */
trait HasIntellisense
{
    /**
     * intellisenseLoadOctoberTags
     */
    protected function intellisenseLoadOctoberTags()
    {
        return $this->loadAndLocalizeJsonFile(__DIR__.'/editorintellisense/octobertags.json');
    }

    /**
     * intellisenseLoadTwigFilters
     */
    protected function intellisenseLoadTwigFilters()
    {
        return $this->loadAndLocalizeJsonFile(__DIR__.'/editorintellisense/twigfilters.json');
    }
}
