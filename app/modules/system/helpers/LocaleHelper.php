<?php namespace System\Helpers;

/**
 * @deprecated see System\Helpers\Preset
 */
class LocaleHelper
{
    /**
     * @deprecated use `\System\Helpers\Preset::locales()`
     */
    public static function listLocales()
    {
        return \System\Helpers\Preset::locales();
    }

    /**
     * @deprecated use `\System\Helpers\Preset::flags()`
     */
    public static function listLocalesWithFlags()
    {
        return \System\Helpers\Preset::flags();
    }

    /**
     * @deprecated use `Arr::trans()`
     */
    public static function translateArray(array $arr): array
    {
        return \Arr::trans($arr);
    }
}
