const path = require('path');
const { mix } = require('laravel-mix');

mix
    .setPublicPath(path.resolve(__dirname, 'public'))
    .js('resources/assets/js/app.js', 'js')
    // .js('resources/assets/js/bootstrap4.js', 'js')
    .sass('resources/assets/sass/app.scss', 'css')
    .sass('resources/assets/sass/admin.scss', 'css')
    .sourceMaps()
    .extract([
        'vue',
        'axios',
        'lodash',
        'vue-ua',
        'vue-router',
        'laravel-echo',
        'pusher-js',
        'element-ui',
        'moment-timezone',
        'vue-template-compiler'
    ])
    .autoload({
        vue: 'Vue',
        lodash: '_',
        'pusher-js': 'Pusher',
    });

// run versioning on production only
if (mix.inProduction()) {
    mix.version();
}
