const { mix } = require('laravel-mix');

mix.js('resources/assets/js/app.js', 'public/js')
   .js('resources/assets/js/landing.js', 'public/js')
   .js('resources/assets/js/backend.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .sass('resources/assets/sass/admin.scss', 'public/css')
   .sourceMaps()
   .extract([
       'vue',
       'axios',
       'lodash',
       'jquery',
       'vue-ua',
       'video.js',
       'pusher-js',
       'vue-router',
       'laravel-echo',
       'element-ui',
       'vue-clickaway',
       'moment-timezone',
       'vue-template-compiler',
   ])
   .autoload({
       vue: 'Vue',
       lodash: '_',
       'pusher-js': 'Pusher',
       jquery: ['$', 'jQuery'],
   });

// run versioning on production only
if (mix.inProduction()) {
    mix.version();
}