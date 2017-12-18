<template>
    <div>
        <div v-bind:style="{ background: coverBackground }" class="category-header-big profile-cover"
             v-show="showFirstHeader">
            <div class="container user-select full-width">
                <div class="cols-flex">
                    <div class="category-header-left">
                        <!-- avatar -->
                        <div class="profile-avatar">
                            <router-link :to="'/c/' + Store.page.category.temp.name">
                                <img v-bind:src="Store.page.category.temp.avatar" v-bind:alt="Store.page.category.temp.name"/>
                            </router-link>
                        </div>
                        <!-- end avatar -->
                    </div>

                    <div class="category-header-middle flex-align-center">
                        <p>
                            {{ Store.page.category.temp.description }}
                        </p>
                    </div>

                    <div class="category-header-right">
                        <div class="karma">
                            <div class="karma-number">
                                {{ Store.page.category.temp.subscribers }}
                            </div>

                            <div class="karma-text margin-bottom-1">
                                Subscribers
                            </div>

                            <subscribe v-if="!isGuest" subscribed-class="unsubscribe"
                                       unsubscribed-class="subscribe"></subscribe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <nav class="nav has-shadow user-select">
            <div class="container">
                <h1 class="title">
                    <i class="v-icon v-channel" aria-hidden="true"></i>{{ Store.page.category.temp.name }}
                </h1>

                <div class="nav-left">
                    <router-link :to="{ path: '/c/' + $route.params.name }" class="nav-item is-tab"
                                 :class="{ 'is-active': sort == 'hot' }">
                        Hot
                    </router-link>

                    <router-link :to="{ path: '/c/' + $route.params.name + '?sort=new' }" class="nav-item is-tab"
                                 :class="{ 'is-active': sort == 'new' }">
                        New
                    </router-link>

                    <router-link :to="{ path: '/c/' + $route.params.name + '?sort=rising'  }" class="nav-item is-tab"
                                 :class="{ 'is-active': sort == 'rising' }">
                        Rising
                    </router-link>
                </div>

                <div class="channel-admin-btn">
                    <el-dropdown size="medium" type="primary" trigger="click" :show-timeout="0" :hide-timeout="0">
                        <i class="v-icon v-more-vertical"></i>

                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native="showModeratorsModal = true">
                                Moderators
                            </el-dropdown-item>

                            <el-dropdown-item @click.native="showRulesModal = true">
                                Rules
                            </el-dropdown-item>

                            <el-dropdown-item @click.native="block">
                                Block
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>

                    <el-tooltip :content="bookmarked ? 'Unbookmark this channel' : 'Bookmark this channel'"
                                placement="bottom" transition="false" :open-delay="500">
                        <i class="v-icon h-yellow pointer"
                           :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark go-gray'" @click="bookmark"></i>
                    </el-tooltip>

                    <el-button type="primary" @click="$eventHub.$emit('submit')"
                               v-if="!isGuest" plain size="medium">
                        Submit
                    </el-button>

                    <el-button type="success" @click="$router.push('/c/' + $route.params.name + '/mod')"
                               v-if="isModerator" plain size="medium">
                        Moderation
                    </el-button>
                </div>
            </div>
        </nav>

        <moderators :visible.sync="showModeratorsModal" v-if="showModeratorsModal"></moderators>
        <rules :visible.sync="showRulesModal" v-if="showRulesModal"></rules>
    </div>
</template>

<script>
    import Subscribe from '../components/SubscribeButton.vue';
    import Moderators from '../components/Moderators.vue';
    import Rules from '../components/Rules.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        mixins: [Helpers],

        components: {
            Subscribe,
            Moderators,
            Rules
        },

        data() {
            return {
                showModeratorsModal: false,
                showRulesModal: false,
                showFirstHeader: true
            }
        },

        created () {
            this.$eventHub.$on('scrolled-to-top', () => {
                this.showFirstHeader = true
            });

            this.$eventHub.$on('scrolled-a-bit', () => {
                this.showFirstHeader = false
            });
        },

        methods: {
            block() {
                this.$confirm(`Blocking a channel will exclude it form your feed. Are you sure about this?`, 'Warning', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Never mind',
                    type: 'warning'
                }).then(() => {
                    axios.post('/category-block', {
                        category_id: Store.page.category.temp.id
                    }).then(() => {
                        this.$router.push('/');

                        this.$message({
                            type: 'success',
                            message: `You no longer will see submissions from #${Store.page.category.temp.name} in your feed. `
                        });
                    });
                }).catch(() => {
                });
            },

            bookmark: _.debounce(function () {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                this.bookmarked = !this.bookmarked;

                axios.post('/bookmark-category', {
                    id: Store.page.category.temp.id
                }).catch(() => {
                    this.bookmarked = !this.bookmarked;
                });
            }, 200, { leading: true, trailing: false }),
        },

        computed: {
            sort() {
                if (this.$route.name != 'category-submissions')
                    return null;

                if (this.$route.query.sort == 'new')
                    return 'new';

                if (this.$route.query.sort == 'rising')
                    return 'rising';

                return 'hot';
            },

            bookmarked: {
                get() {
                    return Store.state.bookmarks.categories.indexOf(Store.page.category.temp.id) !== -1 ? true : false;
                },

                set() {
                    if (Store.state.bookmarks.categories.indexOf(Store.page.category.temp.id) !== -1) {
                        let index = Store.state.bookmarks.categories.indexOf(Store.page.category.temp.id);
                        Store.state.bookmarks.categories.splice(index, 1);

                        let removeItem = Store.page.category.temp.id; 
                        Store.state.bookmarkedCategories = Store.state.bookmarkedCategories.filter(category => category.id != removeItem);

                        return;
                    }

                    Store.state.bookmarks.categories.push(Store.page.category.temp.id);
                    Store.state.bookmarkedCategories.push(Store.page.category.temp); 
                }
            },

            date () {
                return moment(Store.page.category.temp.created_at).utc(moment().format("MMM Do")).format("MMM Do")
            },

            isModerator () {
                return Store.state.moderatingAt.indexOf(Store.page.category.temp.id) != -1
            },

            coverBackground () {
                if (Store.page.category.temp.color == 'Red') {
                    return '#9a4e4e'
                } else if (Store.page.category.temp.color == 'Blue') {
                    return '#5487d4'
                } else if (Store.page.category.temp.color == 'Dark Blue') {
                    return '#2f3b49'
                } else if (Store.page.category.temp.color == 'Dark Green') {
                    return '#507e75'
                } else if (Store.page.category.temp.color == 'Bright Green') {
                    return 'rgb(117, 148, 127)'
                } else if (Store.page.category.temp.color == 'Purple') {
                    return '#4d4261'
                } else if (Store.page.category.temp.color == 'Orange') {
                    return '#ffaf40'
                } else if (Store.page.category.temp.color == 'Pink') {
                    return '#ec7daa'
                } else { // userStore.color == 'Black'
                    return '#424242'
                }
            }
        }
    }
</script>
