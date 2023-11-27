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
    // Tailor LESS
    mix.less('modules/tailor/assets/less/october.tailor.less', 'modules/tailor/assets/css/');

    // Component LESS
    mix.lessList('modules/tailor/vuecomponents');
};
