const GuestSidebar = resolve => {
    require.ensure(
        ['./components/GuestSidebar.vue'],
        () => {
            resolve(require('./components/GuestSidebar.vue'));
        },
        'guest'
    );
};
const LoginModal = resolve => {
    require.ensure(
        ['./components/LoginModal.vue'],
        () => {
            resolve(require('./components/LoginModal.vue'));
        },
        'guest'
    );
};
const GoogleLoginButton = resolve => {
    require.ensure(
        ['./components/GoogleLoginButton.vue'],
        () => {
            resolve(require('./components/GoogleLoginButton.vue'));
        },
        'guest'
    );
};

const Feedback = resolve => {
    require.ensure(
        ['./components/Feedback.vue'],
        () => {
            resolve(require('./components/Feedback.vue'));
        },
        'logged-in'
    );
};
const Notifications = resolve => {
    require.ensure(
        ['./components/Notifications.vue'],
        () => {
            resolve(require('./components/Notifications.vue'));
        },
        'logged-in'
    );
};
const Messages = resolve => {
    require.ensure(
        ['./components/Messages.vue'],
        () => {
            resolve(require('./components/Messages.vue'));
        },
        'logged-in'
    );
};
const Settings = resolve => {
    require.ensure(
        ['./components/Settings.vue'],
        () => {
            resolve(require('./components/Settings.vue'));
        },
        'logged-in'
    );
};
const RightSidebar = resolve => {
    require.ensure(
        ['./components/auth/RightSidebar.vue'],
        () => {
            resolve(require('./components/auth/RightSidebar.vue'));
        },
        'logged-in'
    );
};
const NewSubmission = resolve => {
    require.ensure(
        ['./components/NewSubmission.vue'],
        () => {
            resolve(require('./components/NewSubmission.vue'));
        },
        'logged-in'
    );
};
const NewChannel = resolve => {
    require.ensure(
        ['./components/NewChannel.vue'],
        () => {
            resolve(require('./components/NewChannel.vue'));
        },
        'logged-in'
    );
};
const ReportSubmission = resolve => {
    require.ensure(
        ['./components/ReportSubmission.vue'],
        () => {
            resolve(require('./components/ReportSubmission.vue'));
        },
        'logged-in'
    );
};
const ReportComment = resolve => {
    require.ensure(
        ['./components/ReportComment.vue'],
        () => {
            resolve(require('./components/ReportComment.vue'));
        },
        'logged-in'
    );
};
const FeedSettings = resolve => {
    require.ensure(
        ['./components/FeedSettings.vue'],
        () => {
            resolve(require('./components/FeedSettings.vue'));
        },
        'logged-in'
    );
};
const SidebarSettings = resolve => {
    require.ensure(
        ['./components/RightSidebarSettings.vue'],
        () => {
            resolve(require('./components/RightSidebarSettings.vue'));
        },
        'logged-in'
    );
};

import KeyboardShortcutsGuide from './components/KeyboardShortcutsGuide.vue';
import MarkdownGuide from './components/MarkdownGuide.vue';
import SearchModal from './components/SearchModal.vue';
import NotFound from './components/NotFound.vue';
import StoreStorage from './mixins/StoreStorage';
import LeftSidebar from './components/auth/LeftSidebar.vue';
import Helpers from './mixins/Helpers';
import FontLoader from './mixins/FontLoader';
import router from './routes';
import Announcement from './components/Announcement.vue';
import PhotoViewer from './components/PhotoViewer.vue';
import GifPlayer from './components/GifPlayer.vue';
import EmbedViewer from './components/Embed.vue';

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
        SidebarSettings, 
        GoogleLoginButton,
        ReportSubmission, 
        MarkdownGuide,
        FeedSettings, 
        Notifications,
        ReportComment, 
        NewSubmission,
        Announcement,
        RightSidebar,
        GuestSidebar,
        PhotoViewer,
        EmbedViewer,
        LeftSidebar,
        SearchModal,
        LoginModal,
        NewChannel,
        GifPlayer,
        NotFound,
        Messages,
        Settings,
        Feedback
    },

    data: {
        showSidebars: true,
        pageTitle: document.title
    },

    computed: {
        unreadNotifications() {
            return Store.state.notifications.filter(item => item.read_at == null).length;
        },

        unreadMessages() {
            return Store.state.contacts.filter(
                item => item.last_message.owner.id != auth.id && item.last_message.read_at == null
            ).length;
        }
    },

    watch: {
        unreadNotifications() {
            this.updatePageTitle();
        },

        unreadMessages() {
            this.updatePageTitle();
        },

        '$route.query'() {
            this.setQueries();
        }
    },

    created() {
        this.loadWebFont();
        this.fillBasicStore();

        window.addEventListener('keydown', this.keydown);
        window.addEventListener('hashchange', this.setHashes);

        // Let's hear it for the events, shall we?
        this.$eventHub.$on('start-conversation', this.startConversation);
        this.$eventHub.$on('submit', this.showNewSubmission);
        this.$eventHub.$on('login-modal', this.loginModal);
        this.$eventHub.$on('markdown-guide', this.openMarkdownGuide);
        this.$eventHub.$on('push-notification', this.pushNotification);

        if (this.$route.query.search) {
            Store.modals.search.show = true; 
        }

        this.setQueries();
        this.setHashes();
    },

    methods: {
        setHashes() {
            let hash = window.location.hash;

            if (!hash) {
                _.forEach(Store.modals, item => {
                    item.show = false;
                });
            } else {
                let modal = _.find(Store.modals, item => {
                    return item.hash == hash.substr(1);
                });

                // if didn't found, means it's supposed to be closed. So leave it be and just unset window.location.hash
                if (modal == undefined) {
                    window.location.hash = '';
                } else {
                    modal.show = true;
                }
            }
        },

        setQueries() {
            // sidebar
            if (this.$route.query.sidebar == 1) {
                this.showSidebars = true;
            } else if (this.$route.query.sidebar == 0) {
                this.showSidebars = false;
            }

            // feedback
            if (this.$route.query.feedback == 1) {
                Store.modals.feedback.show = true;
            }
        },

        loginModal() {
            Store.modals.authintication.show = true;
        },

        openMarkdownGuide() {
            Store.modals.markdownGuide.show = true;
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
                onClick: function() {
                    if (data.url == 'new-message') {
                        Store.modals.messages.show = true;
                    } else {
                        self.$router.push(data.url);
                    }

                    window.focus();
                    this.close();
                }
            });
        },

        /**
         * Opens the messages component and starts the conversation with the sent user.
         *
         * @return void
         */
        startConversation(contact) {
            Store.modals.messages.show = true; 
            this.$eventHub.$emit('conversation', contact);
        },
      
        /**
         * show the submit modal.
         *
         * @return void
         */
        showNewSubmission() {
            Store.modals.newSubmission.show = true;
        },

        /**
         * show the submit modal.
         *
         * @return void
         */
        showNewChannel() {
            Store.modals.newChannel.show = true;
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
         * Catches the event fired for the pressed key, and runs the neccessary methods.
         *
         * @param {keydown} event
         * @return void
         */
        keydown(event) {
            // esc
            if (event.keyCode == 27) {
                this.$eventHub.$emit('pressed-esc');
            }

            // all shortcuts after this one need to be prevented if user is typing
            if (this.whileTyping(event)) return;

            // alt + s == event.altKey && event.keyCode == 83
            if (event.altKey && event.keyCode == 83) {
                // alt + s
                this.showNewSubmission();
                return;
            }

            if (event.altKey && event.keyCode == 67) {
                // alt + c
                this.showNewChannel();
                return;
            }

            if (event.shiftKey && event.keyCode == 191) {
                // shift + /
                Store.modals.keyboardShortcutsGuide.show = true;
                return;
            }

            switch (event.keyCode) {
                case 78: // "n"
                    if (this.isGuest) break;

                    Store.modals.notifications.show = true;
                    break;
                case 77: // "m"
                    if (this.isGuest) break;

                    event.preventDefault();                    
                    Store.modals.messages.show = true; 
                    break;
                case 191: // "/"
                    event.preventDefault();
                    Store.modals.search.show = true; 
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
        }
    }
}).$mount('#voten-app');
