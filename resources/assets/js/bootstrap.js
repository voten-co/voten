window.moment = require('moment-timezone');
window.moment.tz.setDefault("UTC");

// Tooltip plugin
import Tooltip from 'vue-directive-tooltip';
Vue.use(Tooltip, {
    delay: 0,
    placement: 'auto',
    class: '',
    triggers: ['hover'],
    offset: 5
});

// Toggle plugin
import ToggleButton from 'vue-js-toggle-button';
Vue.use(ToggleButton);


/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.axios = require('axios');
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


axios.interceptors.response.use(function (response) {
	return response;
}, function (error) {
	if (error.response.status === 401) location.reload();

	return Promise.reject(error);
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */
import Echo from "laravel-echo";

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
	    auth:
	    {
	        headers:
	        {
	            'Authorization': 'Bearer ' + 'nb35mdq2ca9928qgl4sgjf3imil5811sn41qsmcaph0p3h6sa5ht8hoktdeg'
	        }
	    }
	});
}


// The rest of (non-NPM) packages
require('./libs/transition');
require('./libs/dropdown');
require('./libs/popup');
require('./libs/form');
require('./libs/Jcrop');


window.emojione = require('./libs/emojione.min');
