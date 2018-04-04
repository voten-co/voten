import Raven from 'raven-js';
import RavenVue from 'raven-js/plugins/vue';
if (Laravel.env !== 'local') {
    Raven.config(Laravel.sentry)
        .addPlugin(RavenVue, Vue)
        .install();
}

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

if (meta.isGuest === true) {
    window.axios.defaults.baseURL = Laravel.url + '/api/guest/';
} else {
    window.axios.defaults.baseURL = Laravel.url + '/api/';
}

axios.interceptors.response.use(
    function(response) {
        return response;
    },
    function(error) {
        app.$Progress.fail();

        if (error.response.status !== 422) {
            try {
                let errorMessage =
                    typeof error.response.data.errors.more_info == 'undefined'
                        ? error.response.data.message
                        : error.response.data.errors.more_info;

                app.$message({
                    message: errorMessage,
                    type: 'error'
                });
            } catch (error) {
                app.$message({ message: 'Oops, something went wrong.', type: 'error', showClose: true });
            }
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

window.io = require('socket.io-client');

if (Laravel.broadcasting.service == 'pusher' && Laravel.broadcasting.pusher.key.trim()) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: Laravel.broadcasting.pusher.key,
        cluster: Laravel.broadcasting.pusher.cluster,
        encrypted: true
    });
} else if (Laravel.broadcasting.service == 'echo' && Laravel.broadcasting.echo.key.trim()) {
    window.Echo = new Echo({
        broadcaster: 'socket.io',
        key: Laravel.broadcasting.echo.key,
        host: Laravel.broadcasting.echo.host + ':' + Laravel.broadcasting.echo.port
    });
}

/**
 * A small lirary that helps us with supporting Emojis in Voten's
 * Great Markdown editor.
 */
window.emojione = require('./libs/emojione.min');
