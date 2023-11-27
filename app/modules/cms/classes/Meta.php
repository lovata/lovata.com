<?php namespace Cms\Classes;

use Yaml;

/**
 * Meta is used for interacting with YAML files
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges, Luke Towers
 */
class Meta extends CmsObject
{
    /**
     * @var string dirName associated with the model, eg: pages.
     */
    protected $dirName = 'meta';

    /**
     * @var array contentDataCache store used by parseContent method.
     */
    protected $contentDataCache;

    /**
     * @var array allowedExtensions as file extensions.
     */
    protected $allowedExtensions = ['yaml'];

    /**
     * @var string defaultExtension as file extension.
     */
    protected $defaultExtension = 'yaml';

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct(...func_get_args());

        // Bind data processing to model events
        $this->bindEvent('model.beforeSave', function () {
            $this->content = $this->renderContent();
        });

        $this->bindEvent('model.afterFetch', function () {
            $this->setRawAttributes(array_merge($this->attributes, $this->parseContent()), true);
        });
    }

    /**
     * parseContent processes the content attribute to an array of menu data.
     * @return array|null
     */
    protected function parseContent()
    {
        if ($this->contentDataCache !== null) {
            return $this->contentDataCache;
        }

        $parsedData = Yaml::parse($this->content);

        if (!is_array($parsedData)) {
            return null;
        }

        return $this->contentDataCache = $parsedData;
    }

    /**
     * renderContent (meta data) as a content string in YAML format.
     * @return string
     */
    protected function renderContent()
    {
        return Yaml::render($this->settings);
    }

    /**
     * toCompiled the content for this CMS object, used by the theme logger.
     * @return string
     */
    public function toCompiled()
    {
        return $this->renderContent();
    }
}
