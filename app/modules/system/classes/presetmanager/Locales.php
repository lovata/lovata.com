<?php namespace System\Classes\PresetManager;

use Lang;

/**
 * Locales is a resource file with minimal dependencies
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class Locales
{
    /**
     * locales returns list of available locales
     */
    public static function locales(): array
    {
        $locales = [
            'ar'    => Lang::get('system::lang.locale.ar'),
            'be'    => Lang::get('system::lang.locale.be'),
            'bg'    => Lang::get('system::lang.locale.bg'),
            'ca'    => Lang::get('system::lang.locale.ca'),
            'cs'    => Lang::get('system::lang.locale.cs'),
            'da'    => Lang::get('system::lang.locale.da'),
            'de'    => Lang::get('system::lang.locale.de'),
            'el'    => Lang::get('system::lang.locale.el'),
            'en'    => Lang::get('system::lang.locale.en'),
            'en-au' => Lang::get('system::lang.locale.en-au'),
            'en-ca' => Lang::get('system::lang.locale.en-ca'),
            'en-gb' => Lang::get('system::lang.locale.en-gb'),
            'es'    => Lang::get('system::lang.locale.es'),
            'es-ar' => Lang::get('system::lang.locale.es-ar'),
            'et'    => Lang::get('system::lang.locale.et'),
            'fa'    => Lang::get('system::lang.locale.fa'),
            'fi'    => Lang::get('system::lang.locale.fi'),
            'fr'    => Lang::get('system::lang.locale.fr'),
            'fr-ca' => Lang::get('system::lang.locale.fr-ca'),
            'hr'    => Lang::get('system::lang.locale.hr'),
            'hu'    => Lang::get('system::lang.locale.hu'),
            'id'    => Lang::get('system::lang.locale.id'),
            'it'    => Lang::get('system::lang.locale.it'),
            'ja'    => Lang::get('system::lang.locale.ja'),
            'ko'    => Lang::get('system::lang.locale.ko'),
            'lt'    => Lang::get('system::lang.locale.lt'),
            'lv'    => Lang::get('system::lang.locale.lv'),
            'nb-no' => Lang::get('system::lang.locale.nb-no'),
            'nn-no' => Lang::get('system::lang.locale.nn-no'),
            'nl'    => Lang::get('system::lang.locale.nl'),
            'pl'    => Lang::get('system::lang.locale.pl'),
            'pt-br' => Lang::get('system::lang.locale.pt-br'),
            'pt-pt' => Lang::get('system::lang.locale.pt-pt'),
            'ro'    => Lang::get('system::lang.locale.ro'),
            'ru'    => Lang::get('system::lang.locale.ru'),
            'sk'    => Lang::get('system::lang.locale.sk'),
            'sl'    => Lang::get('system::lang.locale.sl'),
            'sv'    => Lang::get('system::lang.locale.sv'),
            'th'    => Lang::get('system::lang.locale.th'),
            'tr'    => Lang::get('system::lang.locale.tr'),
            'uk'    => Lang::get('system::lang.locale.uk'),
            'vn'    => Lang::get('system::lang.locale.vn'),
            'zh-cn' => Lang::get('system::lang.locale.zh-cn'),
            'zh-tw' => Lang::get('system::lang.locale.zh-tw'),
        ];

        // Sort locales alphabetically
        asort($locales);

        return $locales;
    }

    /**
     * flags returns list of available locales with flag icons
     */
    public static function flags(): array
    {
        $flags = [
            'ar'    => 'flag-sa',
            'be'    => 'flag-by',
            'bg'    => 'flag-bg',
            'ca'    => 'flag-es-ct',
            'cs'    => 'flag-cz',
            'da'    => 'flag-dk',
            'de'    => 'flag-de',
            'el'    => 'flag-gr',
            'en'    => 'flag-us',
            'en-au' => 'flag-au',
            'en-ca' => 'flag-ca',
            'en-gb' => 'flag-gb',
            'es'    => 'flag-es',
            'es-ar' => 'flag-ar',
            'et'    => 'flag-ee',
            'fa'    => 'flag-ir',
            'fi'    => 'flag-fi',
            'fr'    => 'flag-fr',
            'fr-ca' => 'flag-ca',
            'hr'    => 'flag-hr',
            'hu'    => 'flag-hu',
            'id'    => 'flag-id',
            'it'    => 'flag-it',
            'ja'    => 'flag-jp',
            'ko'    => 'flag-kr',
            'lt'    => 'flag-lt',
            'lv'    => 'flag-lv',
            'nb-no' => 'flag-no',
            'nn-no' => 'flag-no',
            'nl'    => 'flag-nl',
            'pl'    => 'flag-pl',
            'pt-br' => 'flag-br',
            'pt-pt' => 'flag-pt',
            'ro'    => 'flag-ro',
            'ru'    => 'flag-ru',
            'sk'    => 'flag-sk',
            'sl'    => 'flag-si',
            'sv'    => 'flag-se',
            'th'    => 'flag-th',
            'tr'    => 'flag-tr',
            'uk'    => 'flag-ua',
            'vn'    => 'flag-vn',
            'zh-cn' => 'flag-cn',
            'zh-tw' => 'flag-hk',
        ];

        $locales = [];

        foreach (self::locales() as $code => $label) {
            if (isset($flags[$code])) {
                $locales[$code] = [$label, $flags[$code]];
            }
        }

        return $locales;
    }

    /**
     * @deprecated
     */
    public static function localeIcons(): array
    {
        return self::flags();
    }
}
