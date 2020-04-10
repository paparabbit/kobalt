let mix = require('laravel-mix');

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

mix
    .js('src/resources/src/js/app.js', 'src/resources/assets/admin.js')
    .less('src/resources/src/less/app.less', 'src/resources/assets/admin.css')
    .options({
    })

if (mix.inProduction()) {
    mix.options({
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true
                }
            },
            extractComments: false
        }
    });
}
