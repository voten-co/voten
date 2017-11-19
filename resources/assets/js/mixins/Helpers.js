import WebFont from 'webfontloader';

export default {
    data() {
        return {
            Store,
            auth,
            csrf: window.Laravel.csrfToken,
        }
    },

    computed: {
        /**
         * Is the user a guest
         *
         * @return bool
         */
        isGuest()
        {
            return auth.isGuest;
        },

        /**
         * Is visitor browsing via a mobile device
         *
         * @return bool
         */
        isMobile()
        {
            return auth.isMobileDevice;
        },

        /**
         * is user a moderator?
         *
         * @return bool
         */
        isModerating() {
            return Store.moderatingAt.length > 0;
        },
    },

    methods: {
        /**
         * Loads the web-font.
         *
         * @param string font
         * @return void
         */
        loadWebFont()
        {
            WebFont.load({
                google: {
                    families: [auth.font]
                }
            });
        },

        /**
         * sets the page title
         *
         * @param string title
         * @param bool explicit
         * @return void
         */
        setPageTitle(title, explicit = false)
        {
            if (explicit == true) {
                document.title = title;
                return;
            }

            document.title = title + ' - Voten';
        },

        /**
         * the user must be login other wise rais a warning
         *
         * @return void
         */
        mustBeLogin()
        {
            if (!this.isGuest) return;

            this.$eventHub.$emit('login-modal');
        },

        /**
         * simulates Laravel's str_limit in JS
         *
         * @param string str
         * @param integer length
         * @return string
         */
        str_limit(str, length)
        {
            if (str.length > length)
                return str = str.substring(0, length) + '...';
            return str;
        },

        /**
         * Slugifies the string.
         *
         * @param string str
         * @return string
         */
        str_slug(str) {
            return str.toString().toLowerCase().trim()
                      .replace(/\s+/g, '-')
                      .replace(/&/g, '-and-')
                      .replace(/[^\w\-]+/g, '')
                      .replace(/\-\-+/g, '-');
        },

        /**
         * determines if the user is typing in either an input or textarea
         *
         * @return boolean
         */
        whileTyping (event)
        {
            return event.target.tagName.toLowerCase() === 'textarea' || event.target.tagName.toLowerCase() === 'input';
        },

        /**
         * determines if the timestamp is for today's date
         *
         * @param string timestamp
         * @return boolean
         */
        isItToday(timestamp)
        {
            if (typeof timestamp != 'string') {
                timestamp = timestamp.date;
            }

            return moment(timestamp).format('DD/MM/YYYY') == moment(new Date()).format('DD/MM/YYYY');
        },

        /**
         * parses the date in a neat and user-friendly format for today
         *
         * @param string timestamp
         * @param string timezone
         * @return string
         */
        parseDateForToday(timestamp, timezone)
        {
            if (typeof timestamp != 'string') {
                timestamp = timestamp.date;
            }

            if (!timezone) {
                timezone = moment.tz.guess();
            }

            return moment(timestamp).tz(timezone).format("LT");
        },

        /**
         * parses the date in a neat and user-friendly format
         *
         * @param string timestamp
         * @param string timezone
         * @return string
         */
        parseDate(timestamp, timezone)
        {
            if (typeof timestamp != 'string') {
                timestamp = timestamp.date;
            }

            if (!timezone) {
                timezone = moment.tz.guess();
            }

            return moment(timestamp).tz(timezone).format("MMM Do");
        },

        /**
         * Parses the date in a in full format.
         *
         * @param string timestamp
         * @param string timezone
         * @return string
         */
        parseFullDate(timestamp, timezone)
        {
            if (typeof timestamp != 'string') {
                timestamp = timestamp.date;
            }

            if (!timezone) {
                timezone = moment.tz.guess();
            }

            return moment(timestamp).tz(timezone).format("LLL");
        },

        /**
         * Parses the date in "n days ago" format.
         *
         * @param string timestamp
         * @param string timezone
         * @return string
         */
        parsDiffForHumans(timestamp, timezone)
        {
            if (typeof timestamp != 'string') {
                timestamp = timestamp.date;
            }

            if (!timezone) {
                timezone = moment.tz.guess();
            }

            return moment(timestamp).tz(timezone).fromNow();
        },

        /**
         * prefixes the route with /auth if it's for authenticated users
         *
         * @param string route
         * @return string
         */
        authUrl(route)
        {
            return !this.isGuest ? '/auth/' + route : '/' + route;
        },

        /**
         * Catches the scroll event and fires the neccessary ones for componenets. (Such as Inifinite Scrolling)
         *
         * @return void
         */
        scrolled(event) {
            this.$eventHub.$emit('scrolled');

            let box = event.target;

            if ((box.scrollHeight - box.scrollTop) < (box.clientHeight + 100)) {
                this.$eventHub.$emit('scrolled-to-bottom');
            }

            if (box.scrollTop < 100) {
                this.$eventHub.$emit('scrolled-to-top');
            } else if (box.scrollTop < 1500) {
                this.$eventHub.$emit('scrolled-a-bit');
            } else {
                this.$eventHub.$emit('scrolled-a-lot');
            }
        },
    }
};
