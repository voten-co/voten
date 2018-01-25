const { mix } = require('laravel-mix');

mix
    .js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/backend.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/admin.scss', 'public/css')
    .sourceMaps()
    .extract([
        'vue',
        'axios',
        'lodash',
        'vue-ua',
        'pusher-js',
        'vue-router',
        'laravel-echo',
        'element-ui',
        'moment-timezone',
        'vue-template-compiler'
    ])
    .autoload({
        vue: 'Vue',
        lodash: '_',
        'pusher-js': 'Pusher'
    });

// run versioning on production only
if (mix.inProduction()) {
    mix.version();
}
