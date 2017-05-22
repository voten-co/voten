export default {
    methods: {
        /**
         * Does the neccessary checks and then pushes the notification to the user's OS.
         *
         * @return void
         * @param {String} title
         * @param {String} body
         * @param {String} url
         * @param {String} icon (optional)
         */
        webNotification: function (title, body, url, icon = '/imgs/v-logo.png') {
            let self = this

            // if browser supports the API
            if ('Notification' in window) {
                Notification.requestPermission().then(function(result) {
                    // already allowed web notifications for our website
                    if (Notification.permission === "granted") {
                        var notification = new Notification(title, {
                            body: body,
                            icon: icon
                        })

                        notification.onclick = function() {
                            if (url == 'new-message') {
                                Store.contentRouter = 'messages'
                            } else {
                                self.$router.push(url)
                            }

                            window.focus()
                            notification.close()
                        }

                        setTimeout(notification.close.bind(notification), 5000)

                    // Hasn't asked user web notifications permission
                    } else if (Notification.permission !== 'denied') {
                        Notification.requestPermission(function (permission) {
                            if (permission === "granted") {
                                var notification = new Notification(title, {
                                    body: body,
                                    icon: '/imgs/v-logo.png'
                                })
                                notification.onclick = function() {
                                    if (url == 'new-message') {
                                        Store.contentRouter = 'messages'
                                    } else {
                                        self.$router.push(url)
                                    }

                                    window.focus()
                                    notification.close()
                                }
                                setTimeout(notification.close.bind(notification), 5000)
                            }
                        })
                    }
                })
            } else {
                console.log('Your browser does not support desktop notifications. ')
            }
        },
    }
};
