import KeyboardShortcutsGuide from './components/KeyboardShortcutsGuide.vue';
import Notifications from './components/Notifications.vue';
import MarkdownGuide from './components/MarkdownGuide.vue';
import GuestSidebar from './components/GuestSidebar.vue';
import SearchModal from './components/SearchModal.vue';
import LoginModal from './components/LoginModal.vue';
import Dashboard from './components/Dashboard.vue';
import NotFound from './components/NotFound.vue';
import Messages from './components/Messages.vue';
import Settings from './components/Settings.vue';
import StoreStorage from './mixins/StoreStorage';
import LeftSidebar from './components/auth/LeftSidebar.vue';
import RightSidebar from './components/auth/RightSidebar.vue';
import NewSubmission from './components/NewSubmission.vue';
import NewChannel from './components/NewChannel.vue';
import Helpers from './mixins/Helpers';
import FontLoader from './mixins/FontLoader';
import router from './routes';


/**
 * This is our event bus, which is used for event dispatching. The base is that we create an empty
 * Vue instance. First we fire the event by: "this.$eventHub.$emit('eventName', 'data')"
 * and later we listen for it by: "this.$eventHub.$on('eventName', this.newComment)"
 *
 *
 * (which is defined in the created() function of the vue componentr (or root instance), after catching the
 * event, passes the data to the defined funciton. In this example case it's newComment() but notice that
 * it doesn't require to be actually written as argumans! ) Happy eventing in your awesome components.
 */
Vue.prototype.$eventHub = new Vue();


/**
 * The very serious and important vue instance!!! This is what gives power to voten's
 * front-end. Try to love it, maintain it, appriciate it and maybe even more! This
 * also plays a role in switching states and maintaining the Store.
 */
window.app = new Vue({
    router,

    mixins: [Helpers, StoreStorage, FontLoader],

    components: {
        KeyboardShortcutsGuide,
        MarkdownGuide,
        Notifications,
        RightSidebar,
        GuestSidebar,
        LeftSidebar,
        SearchModal,
        LoginModal,
        Dashboard,
        NotFound,
        Messages,
        NewSubmission,
        NewChannel,
        Settings, 
    },

    data: {
        showNewSubmissionModal: false,
        showNewChannelModal: false,
        showKeyboardShortcutsGuide: false,
        showMarkdownGuide: false,
        pageTitle: document.title,
    },

    computed: {
        unreadNotifications() {
            return Store.state.notifications.filter(item => item.read_at == null).length;
        },

        unreadMessages() {
            return Store.state.contacts.filter(item => item.last_message.owner.id != auth.id && item.last_message.read_at == null).length;
        },

        showRightSidebar() {
            return Store.contentRouter === 'notifications' || Store.contentRouter === 'messages' || Store.contentRouter === 'search' ? false : true;
        },
    },

    watch: {
        '$route' () {
            this.closeModals();
        },

        'unreadNotifications'() {
            this.updatePageTitle();
        },

        'unreadMessages'() {
            this.updatePageTitle();
        },
    },

    created: function () {
        this.loadWebFont();

        window.addEventListener('keydown', this.keydown);

        this.fillBasicStore();

        // Let's hear it for the events, shall we?
        this.$eventHub.$on('start-conversation', this.startConversation);
        this.$eventHub.$on('new-route', this.newRoute);
        this.$eventHub.$on('close', this.closeModals);
        this.$eventHub.$on('submit', this.showNewSubmission);
        this.$eventHub.$on('login-modal', this.loginModal);
        this.$eventHub.$on('change-route', this.changeRoute);
        this.$eventHub.$on('markdown-guide', this.openMarkdownGuide);
        this.$eventHub.$on('push-notification', this.pushNotification)

        if (this.$route.query.search) {
            this.changeRoute('search');
        }
    },

    methods: {
        openMarkdownGuide() {
            this.showMarkdownGuide = true;
        },

        /**
         * Catches the notification event and passes it in case it should.
         *
         * @param {Object} data
         * @return void
         */
        pushNotification(data) {
            let self = this; 
            
            Push.create(data.title, {
                body: data.body,
                icon: data.icon ? data.icon : '/imgs/v-logo.png',
                timeout: 5000,
                onClick: function () {
                    if (data.url == 'new-message') {
                        Store.contentRouter = 'messages'; 
                    } else {
                        self.$router.push(data.url); 
                    }

                    window.focus();
                    this.close();
                }
            });
        },

        /**
         * Fetches the info about the user which we need later
         *
         * @return void
         */
        getUserStore() {
            // if landed on the user page as guest
            if (preload.user) {
                this.submissions = preload.user;

                Store.page.user = preload.user

                if (Store.page.user.id == auth.id) {
                    auth.stats = Store.page.user.stats
                }

                // clear the preload
                delete preload.user;

                return;
            }

            axios.get('/get-user-store', {
                params: {
                    username: this.$route.params.username
                }
            }).then((response) => {
                Store.page.user = response.data

                if (Store.page.user.id == auth.id) {
                    auth.stats = Store.page.user.stats
                }
            }).catch((error) => {
                if (error.response.status === 404) {
                    this.$router.push('/404')
                }
            });
        },

        /**
         * Fetches the info about the channel which we need later.
         *
         * @param string name
         */
        getChannelStore: _.throttle(function (name) {
            // if landed on a submission page
            if (preload.channel && preload.channel.name == this.$route.params.name) {
                Store.page.channel.temp = preload.channel;
                delete preload.channel;
                return;
            }

            if (Store.page.channel.temp.name == undefined || Store.page.channel.temp.name != this.$route.params.name) {
                axios.get('/get-channel-store', {
                    params: {
                        name: name
                    }
                }).then((response) => {
                    Store.page.channel.temp = response.data

                    // update the channel in the user's subscriptions (avatar might have changed)
                    let channel_id = Store.page.channel.temp.id

                    function findObject(ob) {
                        return ob.id === channel_id
                    }

                    let i = Store.state.subscribedChannels.findIndex(findObject)

                    if (i != -1 && Store.state.subscribedChannels[i].avatar != response.data.avatar) {
                        Store.state.subscribedChannels[i].avatar = response.data.avatar
                        Vue.putLS('subscribedChannels', Store.state.subscribedChannels)
                    }

                    // update the channel in the user's moderating (avatar might have changed)
                    i = Store.state.moderatingChannels.findIndex(findObject)

                    if (i != -1 && Store.moderatingChannels[i].avatar != response.data.avatar) {
                        Store.moderatingChannels[i].avatar = response.data.avatar
                        Vue.putLS('moderatingChannels', Store.moderatingChannels)
                    }
                }).catch((error) => {
                    if (error.response.status === 404) {
                        this.$router.push('/404')
                    }
                });
            }
        }, 600),

        /**
         * Opens the messages component and starts the conversation with the sent user.
         *
         * @return void
         */
        startConversation(contact) {
            this.changeRoute('messages');
            this.$eventHub.$emit('conversation', contact);
        },

        /**
         * Switches the to the dispatched route (without any checking)
         *
         * @return void
         */
        newRoute(route) {
            Store.contentRouter = route
        },

        /**
         * show the submit modal.
         *
         * @return void
         */
        showNewSubmission() {
            this.showNewSubmissionModal = true;
        },

        /**
         * show the submit modal.
         *
         * @return void
         */
        showNewChannel() {
            this.showNewChannelModal = true;
        },

        /**
         * Updates the <title> by adding the number of notifications and messages
         *
         * @return void
         */
        updatePageTitle() {
            let total = this.unreadMessages + this.unreadNotifications;

            if (total > 0) {
                document.title = '(' + total + ') ' + this.pageTitle;
            } else {
                document.title = this.pageTitle;
            }
        },

        /**
         * Switches the contentRouter.
         *
         * @param  string
         * @return void
         */
        changeRoute(newRoute) {
            this.$eventHub.$emit('close');

            Store.contentRouter = newRoute;
        },
        

        /**
         * Used for keyup.esc
         *
         * @return void
         */
        closeModals() {
            Store.contentRouter = 'content';
            Store.showNotifications = false; 
            Store.showPreferences = false; 
        },

        /**
         * Catches the event fired for the pressed key, and runs the neccessary methods.
         *
         * @param {keydown} event
         * @return void
         */
        keydown(event) {
            // esc
            if (event.keyCode == 27) {
                this.closeModals();
                this.$eventHub.$emit('pressed-esc');
            }

            // all shortcuts after this one need to be prevented if user is typing
            if (this.whileTyping(event)) return;

            // alt + s == event.altKey && event.keyCode == 83
            if (event.altKey && event.keyCode == 83) { // alt + s
                this.showNewSubmission();
                return;
            }

            if (event.altKey && event.keyCode == 67) { // alt + c
                this.showNewChannel();
                return;
            }

            if (event.shiftKey && event.keyCode == 191) { // shift + /
                this.openMarkdownGuide();
                return;
            }

            switch (event.keyCode) {
                case 78: // "n"
                    if (this.isGuest) break;

                    Store.showNotifications = true; 
                    break;
                case 77: // "m"
                    if (this.isGuest) break;

                    this.changeRoute('messages');
                    break;
                case 191: // "/"
                    event.preventDefault();
                    this.changeRoute('search');
                    break;
                case 66: // "b"
                    if (this.isGuest) break;

                    this.$router.push('/bookmarks');
                    break;
                case 72: // "h"
                    this.$router.push('/');
                    break;
                case 80: // "p"
                    if (this.isGuest) break;

                    this.$router.push('/@' + this.auth.username);
                    break;
                case 82: // "r"
                    if (this.$route.name === 'home') {
                        this.$eventHub.$emit('refresh-home');
                    } else if (this.$route.name === 'channel-submissions') {
                        this.$eventHub.$emit('refresh-channel-submissions');
                    }

                    break;
                default:
                    return;
            }
        },
    },
}).$mount('#voten-app');
