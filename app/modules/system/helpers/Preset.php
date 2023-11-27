<?php namespace System\Helpers;

use System\Classes\PresetManager;

/**
 * Presets
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class Preset
{
    /**
     * icons returns list of available system icons
     */
    public static function icons(): array
    {
        return PresetManager::instance()->getPreset('icons');
    }

    /**
     * locales returns list of available locales
     */
    public static function locales(): array
    {
        return PresetManager::instance()->getPreset('locales');
    }

    /**
     * flags returns list of available locales with flag icons
     */
    public static function flags(): array
    {
        return PresetManager::instance()->getPreset('flags');
    }

    /**
     * timezones returns list of available timezones
     */
    public static function timezones(): array
    {
        return PresetManager::instance()->getPreset('timezones');
    }

    /**
     * @deprecated
     */
    public static function localeIcons(): array
    {
        return self::flags();
    }
}
