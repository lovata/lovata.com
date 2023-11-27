<?php namespace Tailor\ContentFields;

use Tailor\Classes\ContentFieldBase;

/**
 * MixinField is a special field used for including other fields.
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class MixinField extends ContentFieldBase
{
    /**
     * @var string uuid
     */
    public $source;

    /**
     * defineConfig
     */
    public function defineConfig(array $config)
    {
        if (isset($config['source'])) {
            $this->source = $config['source'];
        }
    }
}
