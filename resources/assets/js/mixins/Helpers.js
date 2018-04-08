import WebFont from 'webfontloader';

export default {
    data() {
        return {
            Store,
            meta,
            auth,
            Laravel,
            csrf: window.Laravel.csrfToken
        };
    },

    computed: {
        activeTour() {
            return Store.tour.items[Store.tour.step - 1];
        },

        showTour() {
            return Store.tour.show;
        },

        /**
         * Is the user a guest
         *
         * @return bool
         */
        isGuest() {
            return meta.isGuest;
        },

        /**
         * Is the user an authinticated user
         *
         * @return bool
         */
        isLoggedIn() {
            return !meta.isGuest;
        },

        /**
         * Is visitor browsing via a mobile device
         *
         * @return bool
         */
        isMobile() {
            return meta.isMobileDevice;
        },

        /**
         * Is visitor browsing via a desktop device
         *
         * @return bool
         */
        isDesktop() {
            return !meta.isMobileDevice;
        },

        /**
         * is user a moderator?
         *
         * @return bool
         */
        isModerating() {
            return Store.state.moderatingAt.length > 0;
        },

        isVotenAdministrator() {
            return meta.isVotenAdministrator;
        }
    },

    methods: {
        /**
         * Loads the web-font.
         *
         * @param string font
         * @return void
         */
        loadWebFont() {
            WebFont.load({ google: { families: [Store.settings.font] } });
        },

        /**
         * sets the page title
         *
         * @param string title
         * @param bool explicit
         * @return void
         */
        setPageTitle(title, explicit = false) {
            if (explicit == true) {
                document.title = title;
                return;
            }

            document.title = title;
        },

        /**
         * the user must be login other wise rais a warning
         *
         * @return void
         */
        mustBeLogin() {
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
        str_limit(str, length) {
            if (typeof str == typeof null) {
                return null;
            }

            if (str.length > length) return (str = str.substring(0, length) + '...');

            return str;
        },

        /**
         * Slugifies the string.
         *
         * @param string str
         * @return string
         */
        str_slug(str) {
            return str
                .toString()
                .toLowerCase()
                .trim()
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
        whileTyping(event) {
            return event.target.tagName.toLowerCase() === 'textarea' || event.target.tagName.toLowerCase() === 'input';
        },

        /**
         * determines if the timestamp is for today's date
         *
         * @param string timestamp
         * @return boolean
         */
        isItToday(timestamp) {
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
        parseDateForToday(timestamp, timezone) {
            if (typeof timestamp != 'string') {
                timestamp = timestamp.date;
            }

            if (!timezone) {
                timezone = moment.tz.guess();
            }

            return moment(timestamp)
                .tz(timezone)
                .format('LT');
        },

        /**
         * parses the date in a neat and user-friendly format
         *
         * @param string timestamp
         * @param string timezone
         * @return string
         */
        parseDate(timestamp, timezone) {
            if (typeof timestamp != 'string') {
                timestamp = timestamp.date;
            }

            if (!timezone) {
                timezone = moment.tz.guess();
            }

            return moment(timestamp)
                .tz(timezone)
                .format('MMM Do');
        },

        /**
         * Parses the date in a in full format.
         *
         * @param string timestamp
         * @param string timezone
         * @return string
         */
        parseFullDate(timestamp, timezone) {
            if (typeof timestamp != 'string') {
                timestamp = timestamp.date;
            }

            if (!timezone) {
                timezone = moment.tz.guess();
            }

            return moment(timestamp)
                .tz(timezone)
                .format('LLL');
        },

        /**
         * Parses the date in "n days ago" format.
         *
         * @param string timestamp
         * @param string timezone
         * @return string
         */
        parsDiffForHumans(timestamp, timezone) {
            if (typeof timestamp != 'string') {
                timestamp = timestamp.date;
            }

            if (!timezone) {
                timezone = moment.tz.guess();
            }

            return moment(timestamp)
                .tz(timezone)
                .fromNow(true);
        },

        /**
         * Parses a timestamp for current moment.
         *
         * @return string
         */
        now() {
            return moment()
                .utc()
                .format('YYYY-MM-DD HH:mm:ss');
        },

        /**
         * prefixes the route with /auth if it's for authenticated users
         *
         * @param string route
         * @return string
         */
        authUrl(route) {
            return !this.isGuest ? '/auth/' + route : '/' + route;
        },

        /**
         * Catches the scroll event and fires the neccessary ones for componenets. (Such as Inifinite Scrolling)
         *
         * @return void
         */
        scrolled: _.throttle(
            function(event) {
                this.$eventHub.$emit('scrolled');
                console.log('scrolled');

                let box = event.target;

                if (box.scrollHeight - box.scrollTop < box.clientHeight + 100) {
                    this.$eventHub.$emit('scrolled-to-bottom');
                    console.log('scrolled-to-bottom');
                }

                if (box.scrollTop < 100) {
                    this.$eventHub.$emit('scrolled-to-top');
                    console.log('scrolled-to-top');
                } else if (box.scrollTop < 1500) {
                    this.$eventHub.$emit('scrolled-a-bit');
                    console.log('scrolled-a-bit');
                } else {
                    this.$eventHub.$emit('scrolled-a-lot');
                    console.log('scrolled-a-bit');
                }
            },
            200,
            { leading: true, trailing: true }
        ),

        /**
         * Scroll the page to the top of the element with the passed ID.
         *
         * @param string scrollable
         */
        scrollToTop(scrollable) {
            document.getElementById(scrollable).scrollTop = 0;
        },

        /**
         * Scroll the page to the bottom of the element with the passed ID.
         *
         * @param string scrollable
         */
        scrollToBottom(scrollable) {
            let el = document.getElementById(scrollable);

            el.scrollTop = el.scrollHeight;
        },

        /**
         * Generates a valid URL to channel route.
         *
         * @param {string} name
         */
        channelUrl(name) {
            return `/c/${name}`;
        },

        /**
         * Generates a valid URL to user profile route.
         *
         * @param {string} username
         */
        userUrl(username) {
            return `/@${username}`;
        },

        /**
         * Generates a valid URL to submission route.
         *
         * @param {SubmissionResource} submission
         */
        submissionUrl(submission) {
            return `/c/${submission.channel_name}/${submission.slug}`;
        }
    }
};
