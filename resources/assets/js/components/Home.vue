<template>
    <div class="home-wrapper" id="home">
        <nav class="nav has-shadow user-select">
            <div class="container">
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

                <el-tooltip content="Scroll to top" placement="bottom" transition="false" :open-delay="500">
                    <img src="/imgs/voten.png" class="logo" alt="Voten" @click="scrollToTop('submissions')">
                </el-tooltip>    

                <div class="flex-center">
                    <el-tooltip content="Refresh (R)" placement="bottom" transition="false" :open-delay="500">
                        <button class="feed-panel-button" @click="refresh">
                            <i class="el-icon-refresh" :class="{'rotate': refreshing}"></i>
                        </button>
                    </el-tooltip>

                    <el-tooltip content="Customize Feed" placement="bottom" transition="false" :open-delay="500" v-if="isLoggedIn">
                        <button class="feed-panel-button margin-right-half" @click="Store.modals.feedSettings.show = true">
                            <i class="el-icon-setting"></i>
                        </button>
                    </el-tooltip>

                    <el-button type="primary" icon="el-icon-plus" plain size="medium" @click="submit" v-if="isLoggedIn">
                        Submit
                    </el-button>
                </div>
            </div>
        </nav>

        <section id="submissions" class="home-submissions" :class="{'flex-center' : nothingFound}"
            v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore" @scroll.passive="scrolled"
        >
            <div v-for="(value, index) in uniqueList" v-bind:key="value.id">
                <suggested-channel v-if="isLoggedIn && index == 5"></suggested-channel>

                <submission :list="value"></submission>
            </div>

            <no-content v-if="Store.page.home.nothingFound"
                        :text="'No submissions at this time. Try subscribing to more channels or adjusting your feed filters.'"></no-content>

            <loading v-if="loading && page > 1"></loading>

            <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
        </section>
    </div>
</template>

<script>
    import Helpers from '../mixins/Helpers';
    import Submission from '../components/Submission.vue';
    import SuggestedChannel from '../components/SuggestedChannel.vue';
    import Loading from '../components/Loading.vue';
    import NoContent from '../components/NoContent.vue';
    import NoMoreItems from '../components/NoMoreItems.vue';

    export default {
        mixins: [Helpers],

        components: {
            Submission,
            Loading, 
            SuggestedChannel,
            NoContent,
            NoMoreItems
        },

        data() {
            return {
                refreshing: false
            }
        },

        beforeRouteEnter (to, from, next) {
            if (Store.page.home.page === 0) {
                if (typeof app != "undefined") {
                    app.$Progress.start();
                }

                Store.page.home.getSubmissions(to.query.sort).then(() => {
                    next(vm => vm.$Progress.finish());
                });
            } else {
                next();
            }
        },

        beforeRouteUpdate (to, from, next) {
            if (to.hash !== from.hash) return; 

            Store.page.home.clear();

            this.$Progress.start();

            Store.page.home.getSubmissions(to.query.sort).then(() => {
                this.$Progress.finish();
                next();
            });
        },

        created() {
            this.setPageTitle('Voten: ' + config.title, true);
            this.askNotificationPermission();
            this.$eventHub.$on('refresh-home', this.refresh);
        },

        beforeDestroy() {
            this.$eventHub.$off('refresh-home', this.refresh);                
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
                Store.page.home.getSubmissions(this.sort);
            },

            refresh: _.debounce(function () {
                this.refreshing = true;
                Store.page.home.clear();
                Store.page.home.getSubmissions(this.sort).then(() => this.refreshing = false);
            }, 200, { leading: true, trailing: false }),
            
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
                        this.$notify({
                          title: 'Too bad!',
                          message: 'Your browser does not support desktop notifications.',
                          type: 'warning'
                        });
                    }
                }
            }
        },
    }
</script>
