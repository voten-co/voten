<template>
    <div class="home-wrapper" id="home">
        <nav class="nav has-shadow user-select">
            <div class="container">
                <h1 class="title">
                    Home
                </h1>

                <div class="nav-left">
                    <router-link :to="{ path: '/' }" class="nav-item is-tab" :class="{ 'is-active': sort == 'hot' }">
                        Hot
                    </router-link>

                    <router-link :to="{ path: '/?sort=new' }" class="nav-item is-tab"
                                 :class="{ 'is-active': sort == 'new' }">
                        New
                    </router-link>

                    <router-link :to="{ path: '/?sort=rising' }" class="nav-item is-tab"
                                 :class="{ 'is-active': sort == 'rising' }">
                        Rising
                    </router-link>
                </div>

                <div class="flex-center">
                    <!--<div class="ui icon top right active-blue pointing dropdown feed-panel-button" @click="mustBeLogin" v-tooltip.bottom="{content: 'Customize'}">-->
                    <!--<i class="v-icon v-sliders"></i>-->

                    <!--<div class="menu">-->
                    <!--<div class="header">-->
                    <!--Filter by-->
                    <!--</div>-->

                    <!--<button class="item" @click="changeFilter('subscribed-channels')" :class="{ 'active' : filter == 'subscribed-channels' }">-->
                    <!--Subscribed channels-->
                    <!--</button>-->

                    <!--<button class="item" @click="changeFilter('all-channels')" :class="{ 'active' : filter == 'all-channels' }">-->
                    <!--All channels-->
                    <!--</button>-->

                    <!--<button class="item" @click="changeFilter('moderating-channels')" :class="{ 'active' : filter == 'moderating-channels' }"-->
                    <!--v-if="isModerating">-->
                    <!--Moderating channels-->
                    <!--</button>-->

                    <!--<button class="item" @click="changeFilter('bookmarked-channels')" :class="{ 'active' : filter == 'bookmarked-channels' }">-->
                    <!--Bookmarked channels-->
                    <!--</button>-->

                    <!--<button class="item" @click="changeFilter('by-bookmarked-users')" :class="{ 'active' : filter == 'by-bookmarked-users' }">-->
                    <!--By bookmarked users-->
                    <!--</button>-->
                    <!--</div>-->
                    <!--</div>-->

                    <el-tooltip content="Refresh (R)" placement="bottom" transition="false" :open-delay="500">
                        <button class="feed-panel-button" @click="refresh">
                            <i class="el-icon-refresh" :class="{'rotate': refreshing}"></i>
                        </button>
                    </el-tooltip>

                    <el-tooltip content="Customize Feed" placement="bottom" transition="false" :open-delay="500">
                        <button class="feed-panel-button margin-right-half" @click="showFeedSettings = true">
                            <i class="el-icon-setting"></i>
                        </button>
                    </el-tooltip>

                    <el-button type="primary" icon="el-icon-plus" plain size="medium" @click="submit">
                        Submit
                    </el-button>
                </div>
            </div>
        </nav>

        <!-- <announcement></announcement> -->
        <!--<el-alert-->
        <!--title="success alert"-->
        <!--type="success">-->
        <!--</el-alert>-->

        <section id="submissions" class="home-submissions" v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore">
            <div v-for="(value, index) in uniqueList" v-bind:key="value.id">
                <suggested-category v-if="index == 5"></suggested-category>

                <submission :list="value"></submission>
            </div>

            <no-content v-if="Store.page.home.nothingFound"
                        :text="'No submissions at this time. Try subscribing to more channels or changing your feed filter. '"></no-content>

            <div class="flex-center padding-top-bottom-1" v-if="loading && page > 1">
                <i class="el-icon-loading"></i>
            </div>

            <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
        </section>


        <scroll-button scrollable="submissions"></scroll-button>

        <el-dialog
                title="Customize Your Feed"
                :visible.sync="showFeedSettings"
                :width="isMobile ? '99%' : '35%'"
        >
            <feed-settings></feed-settings>
        </el-dialog>
    </div>
</template>

<script>
    import Announcement from '../components/Announcement.vue';
    import Helpers from '../mixins/Helpers';
    import ScrollButton from '../components/ScrollButton.vue';
    import FeedSettings from '../components/FeedSettings.vue';
    import Submission from '../components/Submission.vue';
    import SuggestedCategory from '../components/SuggestedCategory.vue';
    import NoContent from '../components/NoContent.vue';
    import NoMoreItems from '../components/NoMoreItems.vue';

    function getSubmissions(sort = 'hot') {
        return new Promise((resolve, reject) => {
            Store.page.home.page++;
            Store.page.home.loading = true;

            // if landed on the home page as guest
            if (preload.submissions && app.$route.name == 'home') {
                Store.page.home.submissions = preload.submissions.data;
                if (!Store.page.home.submissions.length) Store.page.home.nothingFound = true;
                if (preload.submissions.next_page_url == null) Store.page.home.NoMoreItems = true;
                Store.page.home.loading = false;
                delete preload.submissions;
                return;
            }

            axios.get(auth.isGuest == true ? '/auth/home' : '/home', {
                params: {
                    sort: sort,
                    page: Store.page.home.page,
                    filter: Store.feedFilter
                }
            }).then((response) => {
                Store.page.home.submissions = [...Store.page.home.submissions, ...response.data.data];

                if (!Store.page.home.submissions.length) Store.page.home.nothingFound = true;
                if (response.data.next_page_url == null) Store.page.home.NoMoreItems = true;

                Store.page.home.loading = false;

                resolve(response);
            }).catch((error) => {
                reject(error);
            });
        });
    }

    export default {
        mixins: [Helpers],

        components: {
            Announcement,
            ScrollButton,
            FeedSettings,
            Submission,
            SuggestedCategory,
            NoContent,
            NoMoreItems
        },

        data() {
            return {
                showFeedSettings: false,
                refreshing: false
            }
        },

        beforeRouteEnter (to, from, next) {
            if (typeof app != "undefined") {
                app.$Progress.start();
            }

            getSubmissions(to.query.sort).then(() => {
                next(vm => vm.$Progress.finish());
            });
        },

        beforeRouteUpdate (to, from, next) {
            this.clearContent();

            this.$Progress.start();

            getSubmissions(to.query.sort).then(() => {
                this.$Progress.finish();
                next();
            });
        },

        created() {
            this.setPageTitle('Voten - Social Bookmarking For The 21st Century', true);
            this.askNotificationPermission();
        },

        computed: {
            cantLoadMore() {
                return this.loading || this.NoMoreItems || this.nothingFound;
            },

            NoMoreItems() {
                return Store.page.home.NoMoreItems;
            },

            nothingFound() {
                return Store.page.home.nothingFound;
            },

            submissions() {
                return Store.page.home.submissions;
            },

            loading() {
                return Store.page.home.loading;
            },

            sort() {
                return this.$route.query.sort ? this.$route.query.sort : 'hot';
            },

            page() {
                return Store.page.home.page;
            },

            uniqueList() {
                return _.uniq(this.submissions);
            },
        },

        methods: {
            loadMore() {
                getSubmissions(this.sort);
            },

            /**
             * Resets all the basic data
             *
             * @return void
             */
            clearContent() {
                Store.page.home.page = 0;
                Store.page.home.nothingFound = false;
                Store.page.home.NoMoreItems = false;
                Store.page.home.submissions = [];
                Store.page.home.loading = true;
            },

            refresh: _.debounce(function () {
                this.refreshing = true;
                this.clearContent();
                getSubmissions(this.sort).then(() => this.refreshing = false);
            }, 700, { leading: true, trailing: false }),

            /**
             * changes the filter for home feed
             *
             * @return void
             */
            changeFilter(filter) {
                if (Store.feedFilter == filter) return;

                Store.feedFilter = filter;

                Vue.putLS('feed-filter', filter);

                this.refresh();
            },

            /**
             * fires the submit event
             *
             * @return void
             */
            submit() {
                this.$eventHub.$emit('submit');
            },

            /**
             * In case the user has just joined to the Voten community let's ask them for the awesome Desktop notifications permission.
             *
             * @return void
             */
            askNotificationPermission() {
                if (this.$route.query.newbie == 1) {
                    if ('Notification' in window) {
                        Notification.requestPermission();
                    } else {
                        console.log('Your browser does not support desktop notifications. ');
                    }
                }
            }
        },
    }
</script>
