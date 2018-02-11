window.moment = require('moment-timezone');
window.moment.tz.setDefault('UTC');

import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/en';
Vue.use(ElementUI, { locale });

import LocalStorage from './plugins/local-storage';
Vue.use(LocalStorage);

import VueProgressBar from 'vue-progressbar';
const VueProgressBarOptions = {
    color: '#5587d7',
    failedColor: '#db6e6e',
    thickness: '3px'
};
Vue.use(VueProgressBar, VueProgressBarOptions);

import infiniteScroll from 'vue-infinite-scroll';
Vue.use(infiniteScroll);

window.Push = require('push.js');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.axios = require('axios');
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = 'http://voten.localhost/api/';

axios.interceptors.response.use(
    function(response) {
        return response;
    },
    function(error) {
        app.$Progress.fail();

        if (error.response.status !== 422) {
            let errorMessage =
                typeof error.response.data.errors.more_info == 'undefined'
                    ? error.response.data.message
                    : error.response.data.errors.more_info;

            app.$message({
                message: errorMessage,
                type: 'error'
            });
        }

        return Promise.reject(error);
    }
);

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */
import Echo from 'laravel-echo';

if (Laravel.env == 'local') {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: Laravel.pusherKey,
        cluster: Laravel.pusherCluster
    });
} else {
    window.Echo = new Echo({
        broadcaster: 'socket.io',
        host: 'https://echo.voten.co:6001',
        auth: {
            headers: {
                Authorization: 'Bearer ' + 'nb35mdq2ca9928qgl4sgjf3imil5811sn41qsmcaph0p3h6sa5ht8hoktdeg'
            }
        }
    });
}

window.emojione = require('./libs/emojione.min');
