const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/editor-setup.js', 'public/js/editor-setup.js')
    .js('resources/js/cabinet.js', 'public/js/cabinet.js')
    .styles('resources/css/common.css', 'public/css/common.css', [require('autoprefixer')])
    .postCss('resources/css/edit-room.css', 'public/css/edit-room.css')
    .styles('resources/css/common.mobile.css', 'public/css/common.mobile.css', [require('autoprefixer')])
    .styles('resources/css/login.css', 'public/css/login.css', [require('autoprefixer')])
    .styles('resources/css/login.mobile.css', 'public/css/login.mobile.css', [require('autoprefixer')])
    .styles('resources/css/cabinet.mobile.css', 'public/css/cabinet.mobile.css', [require('autoprefixer')])
    .styles('resources/css/cabinet.css', 'public/css/cabinet.css', [require('autoprefixer')]);
