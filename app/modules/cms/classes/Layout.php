<?php namespace Cms\Classes;

/**
 * Layout template class
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class Layout extends CmsCompoundObject
{
    use \Cms\Traits\ParsableAttributes;

    /**
     * Fallback layout name.
     */
    const FALLBACK_FILE_NAME = 'fallback';

    /**
     * @var string The container name associated with the model, eg: pages.
     */
    protected $dirName = 'layouts';

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'description',
        'is_priority',
        'markup',
        'settings',
        'code'
    ];

    /**
     * @var array parsable attributes support using parsed variables.
     */
    protected $parsable = [];

    /**
     * beforeValidate applies custom validation rules
     */
    public function beforeValidate()
    {
        // Wipe priority attribute from page settings
        if (!$this->getAttribute('is_priority')) {
            unset($this->attributes['is_priority']);
        }
    }

    /**
     * Initializes the fallback layout.
     * @param \Cms\Classes\Theme $theme Specifies a theme the file belongs to.
     * @return \Cms\Classes\Layout
     */
    public static function initFallback($theme)
    {
        $obj = self::inTheme($theme);
        $obj->markup = '{% page %}';
        $obj->fileName = self::FALLBACK_FILE_NAME;
        return $obj;
    }

    /**
     * isFallBack returns true if the layout is a fallback layout
     * @return bool
     */
    public function isFallBack()
    {
        return $this->fileName === self::FALLBACK_FILE_NAME;
    }

    /**
     * isPriority returns true if the layout should take priority in the load order
     * @return bool
     */
    public function isPriority(): bool
    {
        return (bool) $this->is_priority;
    }

    /**
     * Returns name of a PHP class to us a parent for the PHP class created for the object's PHP section.
     * @return mixed Returns the class name or null.
     */
    public function getCodeClassParent()
    {
        return LayoutCode::class;
    }
}
