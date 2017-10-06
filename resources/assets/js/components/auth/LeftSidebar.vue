<template>
    
    <div class="sidebar-left">
        <router-link :to="'/' + '@' + auth.username" :class="{ 'active': ($route.name == 'user-submissions' || $route.name == 'user-comments' || $route.name == 'user-upvotes' || $route.name == 'user-downvotes') }" class="item" v-tooltip.right="{content: 'Profile', offset: 0}">
            <img :src="auth.avatar" :alt="auth.username" class="avatar">
        </router-link>
        <router-link :to="'/'" :class="{ 'active': $route.name == 'home' }" class="item" v-tooltip.right="{content: 'Home Feed', offset: 0}">
            <home-icon width="30" height="30"></home-icon>
        </router-link>
        <router-link :to="{ path: '/bookmarks' }" active-class="active" class="item" v-tooltip.right="{content: 'Bookmarks', offset: 0}">
            <bookmark-icon width="30" height="30"></bookmark-icon>
        </router-link>
        <router-link :to="{ path: submitURL }" active-class="active" class="item" v-tooltip.right="{content: 'Submit Content', offset: 0}">
            <submit-icon width="30" height="30"></submit-icon>
        </router-link>
        <router-link :to="{ path: '/@' + auth.username + '/settings' }" active-class="active" class="item" v-tooltip.right="{content: 'Settings', offset: 0}">
            <settings-icon width="30" height="30"></settings-icon>
        </router-link>
        <router-link :to="{ path: '/find-channels' }" active-class="active" class="item" v-tooltip.right="{content: 'Find Channels', offset: 0}">
            <search-icon width="30" height="30"></search-icon>
        </router-link>
        <router-link :to="{ path: '/channel' }" active-class="active" class="item" v-tooltip.right="{content: 'Create New Channel', offset: 0}">
            <channel-icon width="30" height="30"></channel-icon>
        </router-link>
        <router-link :to="{ path: '/live' }" active-class="active" class="item" v-tooltip.right="{content: 'Live (coming soon)', offset: 0}">
            <chat-icon width="30" height="30"></chat-icon>
        </router-link>
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

    data: function() {
        return {
            subscribedFilter: '',
            categoriesLimit: 50,
        };
    },

    watch: {
        '$route': function() {
            this.subscribedFilter = ''
        },

        'categoriesLimit': function() {
            this.putLS('sidebar-categories-limit', this.categoriesLimit);
        }
    },

    created() {
        if (this.isSetLS('sidebar-categories-limit')) {
            this.categoriesLimit = this.getLS('sidebar-categories-limit');
        }
    },

    computed: {
        filter() {
            return Store.sidebarFilter;
        },

        submitURL() {
            if (this.$route.params.name)
                return "/submit?channel=" + this.$route.params.name

            return "/submit"
        },

    	/**
    	 * The sorted version of comments
    	 *
    	 * @return {Array} comments
    	 */
        sortedSubscribeds() {
            var self = this

            return _.orderBy(Store.subscribedCategories.filter(function(category) {
                return category.name.toLowerCase().indexOf(self.subscribedFilter.toLowerCase()) !== -1
            }), 'subscribers', 'desc').slice(0, (this.categoriesLimit > 2 ? this.categoriesLimit : 2))
        },
    },

    methods: {
    	/**
         * navigates to home route. aaaand bit more in case the current route IS "home"
         *
         * @return void
         */
        homeRoute() {
            this.$eventHub.$emit('close');

            if (this.$route.name === 'home') {
                this.$eventHub.$emit('refresh-home');
            }
        },

    	/**
    	 * changes the filter for sidebar
    	 *
    	 * @return void
    	 */
        changeFilter(filter) {
            if (Store.sidebarFilter == filter) return;

            Store.sidebarFilter = filter;

            this.putLS('sidebar-filter', filter);

            axios.get(this.authUrl('sidebar-categories'), {
                params: {
                    sidebar_filter: Store.sidebarFilter
                }
            }).then((response) => {
                Store.subscribedCategories = response.data;
            });
        },

        changeRoute: function(newRoute) {
            this.$eventHub.$emit('new-route', newRoute)
        },
    },
}
</script>
