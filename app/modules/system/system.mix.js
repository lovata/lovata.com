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
    // System LESS
    mix.less('modules/system/assets/less/styles.less', 'modules/system/assets/css/');
    mix.less('modules/system/assets/less/settings/settings.less', 'modules/system/assets/css/settings/');
    mix.less('modules/system/assets/less/market/market.less', 'modules/system/assets/css/market/');

    // Component LESS
    mix.lessList('modules/system/widgets');

    // AJAX Framework
    mix.less('modules/system/assets/less/framework-extras.less', 'modules/system/assets/css/');

    // Code Editor Form Widget
    mix.combine([
        'modules/backend/formwidgets/codeeditor/assets/vendor/emmet/emmet.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/ace.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/ext-emmet.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/ext-language_tools.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-php.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-twig.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-markdown.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-plain_text.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-html.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-less.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-css.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-scss.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-sass.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-yaml.js',
        'modules/backend/formwidgets/codeeditor/assets/vendor/ace/mode-javascript.js',
        'modules/backend/formwidgets/codeeditor/assets/js/codeeditor.js',
    ], 'modules/backend/formwidgets/codeeditor/assets/js/build-min.js');

    // Table Widget
    mix.combine([
        'modules/backend/widgets/table/assets/js/table.js',
        'modules/backend/widgets/table/assets/js/table.helper.navigation.js',
        'modules/backend/widgets/table/assets/js/table.helper.search.js',
        'modules/backend/widgets/table/assets/js/table.datasource.base.js',
        'modules/backend/widgets/table/assets/js/table.datasource.client.js',
        'modules/backend/widgets/table/assets/js/table.datasource.server.js',
        'modules/backend/widgets/table/assets/js/table.processor.base.js',
        'modules/backend/widgets/table/assets/js/table.processor.string.js',
        'modules/backend/widgets/table/assets/js/table.processor.checkbox.js',
        'modules/backend/widgets/table/assets/js/table.processor.dropdown.js',
        'modules/backend/widgets/table/assets/js/table.processor.autocomplete.js',
        'modules/backend/widgets/table/assets/js/table.validator.base.js',
        'modules/backend/widgets/table/assets/js/table.validator.required.js',
        'modules/backend/widgets/table/assets/js/table.validator.basenumber.js',
        'modules/backend/widgets/table/assets/js/table.validator.integer.js',
        'modules/backend/widgets/table/assets/js/table.validator.float.js',
        'modules/backend/widgets/table/assets/js/table.validator.length.js',
        'modules/backend/widgets/table/assets/js/table.validator.regex.js',
    ], 'modules/backend/widgets/table/assets/js/build-min.js');

    // Vue Source
    mix.combine([
        'modules/system/assets/vendor/vue/vue.min.js',
        'modules/system/assets/vendor/vue-router/vue-router.js',
        'modules/system/assets/vendor/bluebird/bluebird.min.js',
        'modules/system/assets/vendor/promise-queue/promise-queue.js',
        'modules/system/assets/js/vue.hotkey.js',
        'modules/system/assets/js/vue.main.js'
    ], 'modules/system/assets/js/vue.bundle-min.js');
};
