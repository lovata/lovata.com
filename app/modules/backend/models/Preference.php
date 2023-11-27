<?php namespace Backend\Models;

use App;
use Lang;
use Config;
use Session;
use BackendAuth;
use System\Helpers\Preset as PresetHelper;
use System\Helpers\DateTime as DateTimeHelper;
use Backend\Models\UserPreferenceModel;

/**
 * Preference model for the backend user
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Preference extends UserPreferenceModel
{
    use \October\Rain\Database\Traits\Validation;

    const DEFAULT_THEME = 'twilight';

    /**
     * @var string settingsCode is a unique code
     */
    public $settingsCode = 'backend::backend.preferences';

    /**
     * @var mixed settingsFields form field definitions
     */
    public $settingsFields = 'fields.yaml';

    /**
     * @var array rules for validation
     */
    public $rules = [];

    /**
     * initSettingsData for this model. This only executes when the
     * model is first created or reset to default.
     * @return void
     */
    public function initSettingsData()
    {
        $config = App::make('config');
        $this->locale = $config->get('backend.locale', $config->get('app.locale', 'en'));
        $this->fallback_locale = $this->getFallbackLocale($this->locale);
        $this->timezone = $config->get('backend.timezone', $config->get('app.timezone'));

        $this->editor_theme = $config->get('editor.theme', static::DEFAULT_THEME);
        $this->editor_word_wrap = $config->get('editor.word_wrap', 'off');
        $this->editor_font_size = $config->get('editor.font_size', 12);
        $this->editor_tab_size = $config->get('editor.tab_size', 4);
        $this->editor_code_folding = $config->get('editor.code_folding', 'manual');
        $this->editor_autocompletion = $config->get('editor.editor_autocompletion', 'manual');
        $this->editor_use_emmet = $config->get('editor.use_emmet', true);
        $this->editor_show_gutter = $config->get('editor.show_gutter', true);
        $this->editor_highlight_active_line = $config->get('editor.highlight_active_line', true);
        $this->editor_auto_closing = $config->get('editor.auto_closing', true);
        $this->editor_use_hard_tabs = $config->get('editor.use_hard_tabs', false);
        $this->editor_display_indent_guides = $config->get('editor.display_indent_guides', false);
        $this->editor_show_invisibles = $config->get('editor.show_invisibles', false);
        $this->editor_show_print_margin = $config->get('editor.show_print_margin', false);
    }

    /**
     * setAppLocale based on the user preference.
     */
    public static function setAppLocale()
    {
        $prefLocale = null;
        if (Session::has('locale')) {
            $prefLocale = Session::get('locale');
        }
        elseif (BackendAuth::getUser() && ($locale = static::get('locale'))) {
            Session::put('locale', $locale);
            $prefLocale = $locale;
        }

        if ($prefLocale) {
            if (Config::get('app.original_locale') === null) {
                Config::set('app.original_locale', Config::get('app.locale'));
            }

            App::setLocale($prefLocale);
        }
    }

    /**
     * setAppFallbackLocale is the same as setAppLocale except for the fallback definition.
     */
    public static function setAppFallbackLocale()
    {
        $prefLocale = null;
        if (Session::has('fallback_locale')) {
            $prefLocale = Session::get('fallback_locale');
        }
        elseif (BackendAuth::getUser() && ($locale = static::get('fallback_locale'))) {
            Session::put('fallback_locale', $locale);
            $prefLocale = $locale;
        }

        if ($prefLocale) {
            Lang::setFallback($prefLocale);
        }
    }

    //
    // Events
    //

    /**
     * beforeValidate
     */
    public function beforeValidate()
    {
        $this->fallback_locale = $this->getFallbackLocale($this->locale);
    }

    /**
     * afterSave
     */
    public function afterSave()
    {
        Session::put('locale', $this->locale);
        Session::put('fallback_locale', $this->fallback_locale);
    }

    //
    // Utils
    //

    /**
     * Called when this model is reset to default by the user.
     * @return void
     */
    public function resetDefault()
    {
        parent::resetDefault();
        Session::forget('locale');
        Session::forget('fallback_locale');
    }

    /**
     * @deprecated whole method not used
     */
    public static function applyConfigValues()
    {
        $settings = self::instance();
        if (Config::get('app.original_locale') === null) {
            Config::set('app.original_locale', Config::get('app.locale'));
        }

        Config::set('app.locale', $settings->locale);
        Config::set('app.fallback_locale', $settings->fallback_locale);
    }

    //
    // Getters
    //

    /**
     * getFallbackLocale attempts to extract the language from the locale,
     * otherwise use the configuration.
     * @return string
     */
    protected function getFallbackLocale($locale)
    {
        if ($position = strpos($locale, '-')) {
            $target = substr($locale, 0, $position);
            $available = $this->getLocaleOptions();
            if (isset($available[$target])) {
                return $target;
            }
        }

        return Config::get('app.fallback_locale');
    }

    /**
     * getLocaleOptions returns available options for the "locale" attribute.
     * @return array
     */
    public function getLocaleOptions()
    {
        return PresetHelper::flags();
    }

    /**
     * getTimezoneOptions returns all available timezone options.
     * @return array
     */
    public function getTimezoneOptions()
    {
        return PresetHelper::timezones();
    }

    /**
     * getEditorThemeOptions returns the theme options for the backend editor.
     * @return array
     */
    public function getEditorThemeOptions()
    {
        return [
            static::DEFAULT_THEME => 'Dark',
            'sqlserver' => 'Light',
            'merbivore' => 'High Contrast',
        ];
    }
}
