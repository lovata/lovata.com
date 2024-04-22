<?php namespace System\Models\SiteDefinition;

use Url;
use Config;
use System\Helpers\Preset as PresetHelper;

/**
 * HasModelAttributes are methods assigned for accessing via Twig or PHP
 *
 * @property array $available_countries
 * @property array|null $active_color
 * @property string $status_code
 * @property string $base_url
 * @property string $hard_locale
 * @property string $url
 * @property string $flag_icon
 */
trait HasModelAttributes
{
    /**
     * getActiveColorAttribute
     */
    public function getActiveColorAttribute()
    {
        if ($this->is_styled) {
            return [$this->color_background, $this->color_foreground];
        }

        return null;
    }

    /**
     * getStatusCodeAttribute
     */
    public function getStatusCodeAttribute()
    {
        if ($this->is_enabled) {
            return 'enabled';
        }

        if ($this->is_enabled_edit) {
            return 'editable';
        }

        return 'disabled';
    }

    /**
     * getBaseUrlAttribute
     */
    public function getBaseUrlAttribute()
    {
        $appUrl = $this->is_custom_url ? $this->app_url : Url::to('/');
        $prefix = $this->is_prefixed ? $this->route_prefix : '';

        return rtrim($appUrl . $prefix, '/');
    }

    /**
     * getHardLocaleAttribute will always return a locale code no matter what
     */
    public function getHardLocaleAttribute()
    {
        if ($this->locale) {
            return $this->locale;
        }

        return Config::get('app.original_locale', Config::get('app.locale', 'en'));
    }

    /**
     * getUrlAttribute
     */
    public function getUrlAttribute()
    {
        if ($this->urlOverride !== null) {
            return $this->urlOverride;
        }

        return $this->base_url;
    }

    /**
     * getFlagIconAttribute
     */
    public function getFlagIconAttribute()
    {
        if (!$this->locale || $this->locale === 'custom') {
            return '';
        }

        return PresetHelper::flags()[$this->locale][1] ?? '';
    }
}
