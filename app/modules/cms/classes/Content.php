<?php namespace Cms\Classes;

use File;
use Markdown;
use Cms\Classes\PageManager;

/**
 * Content file class.
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class Content extends CmsCompoundObject
{
    /**
     * @var string dirName associated with the model, eg: pages.
     */
    protected $dirName = 'content';

    /**
     * @var array allowedExtensions
     */
    protected $allowedExtensions = ['htm', 'html', 'txt', 'md'];

    /**
     * @var array purgeable attribute names which are not considered "settings".
     */
    protected $purgeable = ['parsedMarkup'];

    /**
     * initCacheItem initializes the object properties from the cached data. The extra
     * data set here becomes available as attributes set on the model after fetch.
     * @param array $item
     */
    public static function initCacheItem(&$item)
    {
        $item['parsedMarkup'] = (new static($item))->parseMarkup();
    }

    /**
     * getParsedMarkupAttribute returns a default value for parsedMarkup attribute.
     * @return string
     */
    public function getParsedMarkupAttribute()
    {
        if (array_key_exists('parsedMarkup', $this->attributes)) {
            return $this->attributes['parsedMarkup'];
        }

        return $this->attributes['parsedMarkup'] = $this->parseMarkup();
    }

    /**
     * parseMarkup according to the file type.
     * @return string
     */
    public function parseMarkup()
    {
        $extension = strtolower(File::extension($this->fileName));
        $result = $this->markup;

        switch ($extension) {
            case 'html':
                $result = PageManager::processMarkup($result);
                break;
            case 'md':
                $result = Markdown::parse((string) $result);
                $result = PageManager::processMarkup($result);
                break;
            case 'txt':
                $result = htmlspecialchars($result);
                break;
        }

        return $result;
    }
}
