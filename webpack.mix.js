const { mix } = require('laravel-mix');


mix.js('resources/assets/js/app.js', 'public/js')
	.js('resources/assets/js/landing.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/admin.scss', 'public/css')
    .sourceMaps()
    .extract([
        'vue',
        'axios',
        'lodash',
        'jquery',
        'vue-ua',
        'autosize',
        'video.js',
        'pusher-js',
        'vue-focus',
        'vue-router',
        'laravel-echo',
        'vue-clickaway',
        'vue-multiselect',
        'moment-timezone',
        'vue-template-compiler',
    ])
    .autoload({
        vue : 'Vue',
        lodash : '_',
        'pusher-js' : 'Pusher',
        jquery: ['$', 'jQuery'],
    })
    .version()