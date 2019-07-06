const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');


mix.styles([

    // Bootstrap
    'bootstrap/dist/css/bootstrap.css',

    // Slider component
    'bootstrap-slider/dist/css/bootstrap-slider.css',

    // Selectize
    'selectize/dist/css/selectize.css',

    // Icon fonts
    'font-awesome/css/font-awesome.css',
    'material-design-iconic-font/dist/css/material-design-iconic-font.css',

    // Froala WYSIWYG editor
    'froala-editor/css/froala_editor.min.css',
    'froala-editor/css/froala_style.min.css',
    'froala-editor/css/plugins/char_counter.min.css',
    'froala-editor/css/plugins/code_view.min.css',
    'froala-editor/css/plugins/fullscreen.min.css',
    //'froala-editor/css/plugins/image_manager.min.css',
    //'froala-editor/css/plugins/image.min.css',
    'froala-editor/css/plugins/line_breaker.min.css',
    'froala-editor/css/plugins/table.min.css',

], 'public/css/vendor.css');


mix.scripts([

    // jQuery
    'jquery/dist/jquery.js',

    // Bootstrap (Uncomment if we start using any of the JS components)
    // 'bootstrap/dist/js/bootstrap.js',

    // Slider component
    'bootstrap-slider/js/bootstrap-slider.js',

    // Selectize
    'microplugin/src/microplugin.js',
    'sifter/sifter.js',
    'selectize/dist/js/selectize.js',

], 'public/js/vendor.js');

mix.scripts([

    // Froala WYSIWYG editor
    'froala-editor/js/froala_editor.min.js',
    'froala-editor/js/plugins/align.min.js',
    'froala-editor/js/plugins/char_counter.min.js',
    'froala-editor/js/plugins/code_beautifier.min.js',
    'froala-editor/js/plugins/code_view.min.js',
    'froala-editor/js/plugins/entities.min.js',
    'froala-editor/js/plugins/fullscreen.min.js',
    // 'froala-editor/js/plugins/image.min.js',
    // 'froala-editor/js/plugins/image_manager.min.js',
    'froala-editor/js/plugins/inline_style.min.js',
    'froala-editor/js/plugins/line_breaker.min.js',
    'froala-editor/js/plugins/link.min.js',
    'froala-editor/js/plugins/lists.min.js',
    'froala-editor/js/plugins/paragraph_format.min.js',
    'froala-editor/js/plugins/quote.min.js',
    'froala-editor/js/plugins/save.min.js',
    'froala-editor/js/plugins/table.min.js',
    'froala-editor/js/plugins/url.min.js',

], 'public/js/editing.js');


mix.version([
    'public/css/app.css',
    'public/css/vendor.css',
    'public/js/vendor.js',
    'public/js/editing.js',
]);

mix.copy('node_modules/font-awesome/fonts', 'public/build/fonts');

mix.copy('node_modules/material-design-iconic-font/dist/fonts', 'public/build/fonts');
