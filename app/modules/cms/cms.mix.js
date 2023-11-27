/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your theme assets. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

module.exports = (mix) => {
    // Theme Selector
    mix.less('modules/cms/assets/less/october.theme-selector.less', 'modules/cms/assets/css/');

    // Component LESS
    mix.lessList('modules/cms/widgets');
    mix.lessList('modules/cms/formwidgets');
    mix.lessList('modules/cms/vuecomponents');
};
