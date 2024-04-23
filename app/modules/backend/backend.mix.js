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
    // Backend LESS
    mix.less('modules/backend/assets/less/october.less', 'modules/backend/assets/css/');
    mix.less('modules/backend/behaviors/relationcontroller/assets/less/relation.less', 'modules/backend/behaviors/relationcontroller/assets/css/');
    mix.less('modules/backend/behaviors/importexportcontroller/assets/less/export.less', 'modules/backend/behaviors/importexportcontroller/assets/css/');
    mix.less('modules/backend/behaviors/importexportcontroller/assets/less/import.less', 'modules/backend/behaviors/importexportcontroller/assets/css/');

    // Component LESS
    mix.lessList('modules/backend/vuecomponents');
    mix.lessList('modules/backend/formwidgets', ['richeditor']);
    mix.lessList('modules/backend/behaviors');
    mix.lessList('modules/backend/widgets');

    // Vendor Source
    mix.combine([
        'modules/backend/assets/js/vendor/jquery.waterfall.js',
        'modules/backend/assets/vendor/dropzone/dropzone.js',
        'modules/backend/assets/vendor/jcrop/js/jquery.Jcrop.js',
        'modules/backend/assets/vendor/sortablejs/sortable.js',
        'modules/system/assets/vendor/prettify/prettify.js',

        'modules/backend/assets/vendor/js-cookie/js.cookie.js',
        'modules/backend/assets/vendor/modernizr/modernizr.js',
        'modules/backend/assets/vendor/select2/js/select2.full.js',
        'modules/backend/assets/vendor/mousewheel/mousewheel.js',
        'modules/backend/assets/vendor/moment/moment.js',
        'modules/backend/assets/vendor/moment/moment-timezone-with-data.js',
        'modules/backend/assets/vendor/pikaday/js/pikaday.js',
        'modules/backend/assets/vendor/pikaday/js/pikaday.jquery.js',
        'modules/backend/assets/vendor/clockpicker/js/jquery-clockpicker.js',
        'modules/backend/assets/vendor/mustache/mustache.js',
        'modules/backend/assets/vendor/popperjs/popper.min.js',

        'modules/backend/assets/foundation/migrate/vendor/raphael/raphael.js',
        'modules/backend/assets/foundation/migrate/vendor/flot/jquery.flot.js',
        'modules/backend/assets/foundation/migrate/vendor/flot/jquery.flot.tooltip.js',
        'modules/backend/assets/foundation/migrate/vendor/flot/jquery.flot.resize.js',
        'modules/backend/assets/foundation/migrate/vendor/flot/jquery.flot.time.js',
        'modules/backend/assets/foundation/migrate/vendor/sortable/jquery-sortable.js',

    ], 'modules/backend/assets/js/vendor-min.js');

    // Backend Source
    mix.combine([
        ...require('./assets/foundation/scripts/build.js').map(name => `modules/backend/assets/foundation/scripts/${name}`),
        ...require('./assets/foundation/controls/build.js').map(name => `modules/backend/assets/foundation/controls/${name}`),
        ...require('./assets/foundation/migrate/build.js').map(name => `modules/backend/assets/foundation/migrate/${name}`),
        ...require('./assets/js/vueapp/build.js').map(name => `modules/backend/assets/js/vueapp/${name}`),
        ...require('./assets/js/build.js').map(name => `modules/backend/assets/js/${name}`),
    ], 'modules/backend/assets/js/october-min.js');

    // Repeater Widget
    mix.combine([
        'modules/backend/formwidgets/repeater/assets/js/repeater.js',
        'modules/backend/formwidgets/repeater/assets/js/repeater.builder.js',
        'modules/backend/formwidgets/repeater/assets/js/repeater.accordion.js'
    ], 'modules/backend/formwidgets/repeater/assets/js/repeater-min.js');

    // Monaco Editor
    // Disabled for performance, and some files are manually pruned
    // mix
    //     .js(
    //         'modules/backend/vuecomponents/monacoeditor/assets/vendor/monaco-yaml/monaco-yaml.js',
    //         'modules/backend/vuecomponents/monacoeditor/assets/vendor/monaco-yaml/monaco-yaml.min.js'
    //     )
    //     // This must be compiled without compression in terser options
    //     // .js(
    //     //     'modules/backend/vuecomponents/monacoeditor/assets/vendor/monaco-yaml/yaml.worker.js',
    //     //     'modules/backend/vuecomponents/monacoeditor/assets/vendor/monaco-yaml/yaml.worker.min.js'
    //     // )
    //     .copy(
    //         'node_modules/emmet-monaco-es/dist/emmet-monaco.min.js',
    //         'modules/backend/vuecomponents/monacoeditor/assets/vendor/emmet-monaco-es'
    //     )
    //     .copy(
    //         'node_modules/monaco-editor/min',
    //         'modules/backend/vuecomponents/monacoeditor/assets/vendor/monaco'
    //     );
};
