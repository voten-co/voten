<template>
    <div class="sidebar-left">
        <!-- profile -->
        <a @click="pushRouter('/' + '@' + auth.username)" :class="{ 'active': activeRoute === 'user-settings'}" class="item" v-tooltip.right="{content: 'Profile', offset: 0}">
            <img :src="auth.avatar" :alt="auth.username" class="avatar">
        </a>

        <!-- Home -->
        <a @click="pushRouter('/')" :class="{ 'active': activeRoute === 'home' }" class="item" v-tooltip.right="{content: 'Home Feed (H)', offset: 0}">
            <i class="v-icon v-home" aria-hidden="true"></i>
        </a>
        
        <!-- Notifications -->
        <a class="item" :class="{'active' : activeRoute === 'notifications'}"
            v-tooltip="{content:'Notifications (N)', offset: 8}" @click="changeRoute('notifications')"
        >
            <i class="v-icon v-bell-2" aria-hidden="true"></i>
            <span class="queue-number" v-show="unreadNotifications" v-text="unreadNotifications"></span>
        </a>
            
        <!-- Messages Inbox -->
        <a class="item" id="messages-btn" :class="{'active' : activeRoute === 'messages'}" 
            @click="changeRoute('messages')" v-tooltip="{content:'Messages (M)', offset: 8}"
        >
            <i class="v-icon v-inbox" aria-hidden="true"></i>
            <span class="queue-number" v-show="unreadMessages" v-text="unreadMessages"></span>
        </a>

        <!-- Bookmarks -->
        <a @click="pushRouter('/bookmarks')" class="item" :class="{'active' : activeRoute === 'bookmarks'}" v-tooltip.right="{content: 'Bookmarks (B)', offset: 0}">
            <i class="v-icon v-bookmark" aria-hidden="true"></i>
        </a>
            
        <!-- Search -->
        <a class="item" v-tooltip="{content:'Search (/)', offset: 8}" @click="changeRoute('search')"
            :class="{'active' : activeRoute === 'search'}">
            <i class="v-icon v-search-3" aria-hidden="true"></i>
        </a>
        
        <!-- Settings -->
        <a class="item" v-tooltip="{content:'Preferences', offset: 8}" @click="pushRouter('/@' + auth.username + '/settings')"
            :class="{'active' : activeRoute === 'settings'}">
            <i class="v-icon v-gears" aria-hidden="true"></i>
        </a>
        
        <!-- Submit -->
        <a class="item" v-tooltip="{content:'Add Content', offset: 8}" @click="pushRouter('/submit')"
            :class="{'active' : activeRoute === 'submit'}">
            <i class="v-icon v-add-content" aria-hidden="true"></i>
        </a>
    </div>
</template>

<script>
    import Helpers from '../../mixins/Helpers';
    import LocalStorage from '../../mixins/LocalStorage';
    import SubmitIcon from '../../components/Icons/SubmitIcon.vue';
    import BookmarkIcon from '../../components/Icons/BookmarkIcon.vue';
    import SettingsIcon from '../../components/Icons/SettingsIcon.vue';
    import SearchIcon from '../../components/Icons/SearchIcon.vue';
    import ChatIcon from '../../components/Icons/ChatIcon.vue';
    import ChannelIcon from '../../components/Icons/ChannelIcon.vue';
    import ProfileIcon from '../../components/Icons/ProfileIcon.vue';
    import HomeIcon from '../../components/Icons/HomeIcon.vue';

    export default {
        mixins: [Helpers, LocalStorage],

        computed: {
            submitURL() {
                return this.$route.params.name ? "/submit?channel=" + this.$route.params.name : "/submit";
            }
        }, 

        components: {
            SubmitIcon,
            SettingsIcon,
            BookmarkIcon,
            SearchIcon,
            ChatIcon,
            ChannelIcon,
            ProfileIcon,
            HomeIcon
        }, 

        computed: {
            contentRoute() {
                return Store.contentRouter; 
            }, 

            activeRoute() {
                if (this.contentRoute === 'messages') {
                    return 'messages';
                }

                if (this.contentRoute === 'notifications') {
                    return 'notifications';
                }

                if (this.contentRoute === 'search') {
                    return 'search';
                }

                if (this.$route.name === 'home') {
                    return 'home';
                }

                if (this.$route.name === 'bookmarked-submissions' || this.$route.name === 'bookmarked-comments' || this.$route.name === 'bookmarked-users' || this.$route.name === 'bookmarked-categories') {
                    return 'bookmarks';
                }

                if (this.$route.name === 'user-settings-account' || this.$route.name === 'user-settings-profile' || this.$route.name === 'user-settings-feed' || this.$route.name === 'user-settings-email-and-password') {
                    return 'settings';
                }
                
                if (this.$route.name === 'submit') {
                    return 'submit';
                }
            }, 

            unreadNotifications() {
                return Store.notifications.filter(item => item.read_at == null).length;
            },

            unreadMessages() {
                return Store.contacts.filter(item => item.last_message.owner.id != auth.id && item.last_message.read_at == null).length;
            },
        }, 

        methods: {
            changeRoute(route) {
                this.$eventHub.$emit('change-route', route);
            }, 

            pushRouter(route) {
                this.$eventHub.$emit('close');
                
                this.$router.push(route);
            }
        }
    }
</script>
