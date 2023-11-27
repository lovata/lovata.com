<?php namespace Backend\Traits;

use Str;

/**
 * SearchableWidget adds search features to back-end widgets
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */

trait SearchableWidget
{
    /**
     * @var bool searchTerm
     */
    protected $searchTerm = false;

    /**
     * getSearchTerm
     */
    protected function getSearchTerm()
    {
        return $this->searchTerm !== false ? $this->searchTerm : $this->getSession('search');
    }

    /**
     * setSearchTerm
     */
    protected function setSearchTerm($term)
    {
        $this->searchTerm = trim($term);
        $this->putSession('search', $this->searchTerm);
    }

    /**
     * textMatchesSearch
     */
    protected function textMatchesSearch(&$words, $text)
    {
        foreach ($words as $word) {
            $word = trim($word);
            if (!strlen($word)) {
                continue;
            }

            if (Str::contains(Str::lower($text), $word)) {
                return true;
            }
        }

        return false;
    }
}
