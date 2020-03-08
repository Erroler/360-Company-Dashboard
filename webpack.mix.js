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

// mix.js('resources/js/app.js', 'public/js')
//     .sass('resources/sass/app.scss', 'public/css');

mix.babel([
    'node_modules/tabler-ui/dist/assets/js/vendors/bootstrap.bundle.min.js',
    'node_modules/tabler-ui/dist/assets/js/core.js'
  ],'public/js/tabler.js')
  .sass('resources/sass/tabler.scss', 'public/css/tabler.css')
  .browserSync({
      proxy: {
          target: 'http://localhost:8000',
      },
  });