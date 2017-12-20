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
            // make sure the browser supports the API
            if (! ('Notification' in window)) {
                console.log('Your browser does not support desktop notifications. '); 
                return; 
            }


            let self = this;             
            console.log('test1')
            Notification.requestPermission().then(result => {
                console.log('test12')
                // already allowed web notifications for our website
                if (Notification.permission === "granted") {
                    // console.log('test1')
                    let notification = new Notification(title, {
                        body: body,
                        icon: icon
                    }); 

                    notification.onclick = function() {
                        if (url == 'new-message') {
                            Store.contentRouter = 'messages'; 
                        } else {
                            self.$router.push(url); 
                        }

                        window.focus(); 
                        notification.close(); 
                    }

                    setTimeout(notification.close.bind(notification), 5000); 

                // Hasn't asked user for permission yet 
                } else if (Notification.permission !== 'denied') {
                    console.log('test2')
                    
                    Notification.requestPermission(permission => {
                        if (permission === "granted") {
                            let notification = new Notification(title, {
                                body: body,
                                icon: '/imgs/v-logo.png'
                            }); 

                            notification.onclick = function() {
                                if (url == 'new-message') {
                                    Store.contentRouter = 'messages'; 
                                } else {
                                    self.$router.push(url); 
                                }

                                window.focus(); 
                                notification.close(); 
                            }

                            setTimeout(notification.close.bind(notification), 5000); 
                        }
                    }); 
                }
            }); 
        }
    }
};
