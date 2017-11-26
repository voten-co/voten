import KeyboardShortcutsGuide from './components/KeyboardShortcutsGuide.vue';
import Notifications from './components/Notifications.vue';
import MarkdownGuide from './components/MarkdownGuide.vue';
import GuestSidebar from './components/GuestSidebar.vue';
import SearchModal from './components/SearchModal.vue';
import WebNotification from './mixins/WebNotification';
import LoginModal from './components/LoginModal.vue';
import Dashboard from './components/Dashboard.vue';
import NotFound from './components/NotFound.vue';
import Messages from './components/Messages.vue';
import LocalStorage from './mixins/LocalStorage';
import StoreStorage from './mixins/StoreStorage';
import LeftSidebar from './components/auth/LeftSidebar.vue';
import RightSidebar from './components/auth/RightSidebar.vue';
import NewSubmission from './components/NewSubmission.vue';
import NewCategory from './components/NewCategory.vue';
import Helpers from './mixins/Helpers';
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
 * A great wrapper for using LocalStorage which we take advantage of a looot!
 */
import VueLocalStorage from 'vue-ls';
const localStorageConfig = {
    namespace: auth.username + '__voten__'
};
Vue.use(VueLocalStorage, localStorageConfig);


/**
 * The very serious and important vue instance!!! This is what gives power to voten's
 * front-end. Try to love it, maintain it, appriciate it and maybe even more! This
 * also plays a role in switching states and maintaining the Store.
 */
const app = new Vue({
    router,

    mixins: [Helpers, LocalStorage, StoreStorage, WebNotification],

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
        NewCategory,
    },

    data: {
        showNewSubmissionModal: false,
        showNewCategoryModal: false,
        showKeyboardShortcutsGuide: false,
        showMarkdownGuide: false,
        sortFilter: 'hot',
        pageTitle: document.title,
    },

    computed: {
        unreadNotifications() {
            return Store.notifications.filter(item => item.read_at == null).length;
        },

        unreadMessages() {
            return Store.contacts.filter(item => item.last_message.owner.id != auth.id && item.last_message.read_at == null).length;
        },

        showRightSidebar() {
            if (Store.contentRouter === 'notifications') {
                return false;
            }

            if (Store.contentRouter === 'messages') {
                return false;
            }

            if (Store.contentRouter === 'search') {
                return false;
            }

            return true;
        }
    },


    watch: {
        '$route' () {
            this.closeModals();
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
        this.$eventHub.$on('mark-notifications-read', this.markAllNotificationsAsRead);

        if (this.$route.query.search) {
            this.changeRoute('search');
        }
    },

    methods: {
        searchHeader(keyword) {
            this.$eventHub.$emit('search-header', keyword);
        },

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
            this.webNotification(data.title, data.body, data.url, data.icon);
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

                Store.user = preload.user

                if (Store.user.id == auth.id) {
                    auth.stats = Store.user.stats
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
                Store.user = response.data

                if (Store.user.id == auth.id) {
                    auth.stats = Store.user.stats
                }
            }).catch((error) => {
                if (error.response.status === 404) {
                    this.$router.push('/404')
                }
            });
        },

        /**
         * navigates to home route. aaaand bit more in case the current route IS "home"
         *
         * @return void
         */
        homeRoute() {
            this.closeModals();

            if (this.$route.name === 'home') {
                this.$eventHub.$emit('refresh-home');
            }
        },

        /**
         * Fetches the info about the category which we need later.
         *
         * @param string name
         */
        getCategoryStore: _.throttle(function (name) {
            // if landed on a submission page
            if (preload.category && preload.category.name == this.$route.params.name) {
                Store.category = preload.category;
                delete preload.category;
                return;
            }

            if (Store.category.name == undefined || Store.category.name != this.$route.params.name) {
                axios.get('/get-category-store', {
                    params: {
                        name: name
                    }
                }).then((response) => {
                    Store.category = response.data

                    // update the category in the user's subscriptions (avatar might have changed)
                    let category_id = Store.category.id

                    function findObject(ob) {
                        return ob.id === category_id
                    }

                    let i = Store.subscribedCategories.findIndex(findObject)

                    if (i != -1 && Store.subscribedCategories[i].avatar != response.data.avatar) {
                        Store.subscribedCategories[i].avatar = response.data.avatar
                        this.putLS('subscribedCategories', Store.subscribedCategories)
                    }

                    // update the category in the user's moderating (avatar might have changed)
                    i = Store.moderatingCategories.findIndex(findObject)

                    if (i != -1 && Store.moderatingCategories[i].avatar != response.data.avatar) {
                        Store.moderatingCategories[i].avatar = response.data.avatar
                        this.putLS('moderatingCategories', Store.moderatingCategories)
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
        showNewCategory() {
            this.showNewCategoryModal = true;
        },

        /**
         * Updates the <title> by adding the number of notifications and messages
         *
         * @return void
         */
        updatePageTitle() {
            // disable for now
            return;

            let total = this.unreadMessages + this.unreadNotifications;

            if (total > 0) {
                document.title = '(' + total + ') ' + this.pageTitle;
                return;
            }

            document.title = this.pageTitle;
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

            if (newRoute === 'notifications') {
                this.seenAllNotifications();
            }

            this.updatePageTitle();
        },

        /**
         * Marks all user notifications as read
         *
         * @return void
         */
        markAllNotificationsAsRead() {
            axios.post('/mark-notifications-read');

            Store.notifications.forEach(function (element, index) {
                if (!element.read_at) {
                    element.read_at = moment().utc().format('YYYY-MM-DD HH:mm:ss');
                }
            });
        },

        seenAllNotifications() {
            this.markAllNotificationsAsRead();
            this.crossWindowEvent('mark-notifications-read');
        },

        // Used for keyup.esc
        closeModals() {
            Store.contentRouter = 'content';
        },

        // Tells the component (that contains submissions) to change the sort method.
        sortBy(sort, event) {
            if (this.sortFilter == sort) {
                return;
            }

            event.preventDefault();
            this.sortFilter = sort;
            this.$eventHub.$emit('sort-by', sort);
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
                this.showNewCategory();
                return;
            }

            if (event.shiftKey && event.keyCode == 191) { // shift + /
                this.openMarkdownGuide();
                return;
            }

            switch (event.keyCode) {
                case 78: // "n"
                    if (this.isGuest) break;

                    this.changeRoute('notifications');
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
                    } else if (this.$route.name === 'category-submissions') {
                        this.$eventHub.$emit('refresh-category-submissions');
                    }

                    break;
                default:
                    return;
            }
        },
    },
}).$mount('#voten-app');
