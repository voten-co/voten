<template>
    <div>
        <div v-bind:style="{ background: coverBackground }" class="channel-header-big profile-cover"
             v-show="showFirstHeader">
            <div class="container user-select full-width">
                <div class="cols-flex">
                    <div class="channel-header-left">
                        <!-- avatar -->
                        <div class="profile-avatar">
                            <router-link :to="'/c/' + Store.page.channel.temp.name">
                                <img v-bind:src="Store.page.channel.temp.avatar" v-bind:alt="Store.page.channel.temp.name"/>
                            </router-link>
                        </div>
                        <!-- end avatar -->
                    </div>

                    <div class="channel-header-middle flex-align-center">
                        <p>
                            {{ Store.page.channel.temp.description }}
                        </p>
                    </div>

                    <div class="channel-header-right">
                        <div class="xp">
                            <div class="xp-number">
                                {{ Store.page.channel.temp.subscribers_count }}
                            </div>

                            <div class="xp-text margin-bottom-1">
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

                <el-tooltip content="Scroll to top" placement="bottom" transition="false" :open-delay="500">
                    <h1 class="title pointer" @click="scrollToTop('submissions')">
                        <i class="v-icon v-channel" aria-hidden="true"></i>{{ Store.page.channel.temp.name }}
                    </h1>
                </el-tooltip>
                
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

                    <el-button round type="primary" @click="$eventHub.$emit('submit')"
                               v-if="!isGuest" size="mini">
                        Submit
                    </el-button>

                    <el-button round type="success" @click="$router.push('/c/' + $route.params.name + '/mod')"
                               v-if="isModerator" size="mini">
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
        };
    },

    created() {
        this.$eventHub.$on('scrolled-to-top', () => {
            this.showFirstHeader = true;
        });

        this.$eventHub.$on('scrolled-a-bit', () => {
            this.showFirstHeader = false;
        });
    },

    beforeDestroy() {
        this.$eventHub.$off('scrolled-to-top', () => {
            this.showFirstHeader = true;
        });

        this.$eventHub.$off('scrolled-a-bit', () => {
            this.showFirstHeader = false;
        });
    },

    methods: {
        block() {
            if (this.isGuest) {
                this.mustBeLogin();
                return;
            }

            this.$confirm(
                `Blocking a channel will exclude it form your feed. Are you sure about this?`,
                'Warning',
                {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Never mind',
                    type: 'warning'
                }
            )
                .then(() => {
                    axios
                        .post('/channel-block', {
                            channel_id: Store.page.channel.temp.id
                        })
                        .then(() => {
                            this.$router.push('/');

                            this.$message({
                                type: 'success',
                                message: `You no longer will see submissions from #${
                                    Store.page.channel.temp.name
                                } in your feed. `
                            });
                        });
                })
                .catch(() => {});
        },

        bookmark: _.debounce(
            function() {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                this.bookmarked = !this.bookmarked;

                axios
                    .post('/bookmark-channel', {
                        id: Store.page.channel.temp.id
                    })
                    .catch(() => {
                        this.bookmarked = !this.bookmarked;
                    });
            },
            200,
            { leading: true, trailing: false }
        )
    },

    computed: {
        sort() {
            if (this.$route.name != 'channel-submissions') return null;

            if (this.$route.query.sort == 'new') return 'new';

            if (this.$route.query.sort == 'rising') return 'rising';

            return 'hot';
        },

        bookmarked: {
            get() {
                return Store.state.bookmarks.channels.indexOf(
                    Store.page.channel.temp.id
                ) !== -1
                    ? true
                    : false;
            },

            set() {
                if (
                    Store.state.bookmarks.channels.indexOf(
                        Store.page.channel.temp.id
                    ) !== -1
                ) {
                    let index = Store.state.bookmarks.channels.indexOf(
                        Store.page.channel.temp.id
                    );
                    Store.state.bookmarks.channels.splice(index, 1);

                    let removeItem = Store.page.channel.temp.id;
                    Store.state.bookmarkedChannels = Store.state.bookmarkedChannels.filter(
                        (channel) => channel.id != removeItem
                    );

                    return;
                }

                Store.state.bookmarks.channels.push(Store.page.channel.temp.id);
                Store.state.bookmarkedChannels.push(Store.page.channel.temp);
            }
        },

        date() {
            return moment(Store.page.channel.temp.created_at)
                .utc(moment().format('MMM Do'))
                .format('MMM Do');
        },

        isModerator() {
            return (
                Store.state.moderatingAt.indexOf(Store.page.channel.temp.id) !=
                -1
            );
        },

        coverBackground() {
            if (Store.page.channel.temp.cover_color == 'Red') {
                return '#9a4e4e';
            } else if (Store.page.channel.temp.cover_color == 'Blue') {
                return '#5487d4';
            } else if (Store.page.channel.temp.cover_color == 'Dark Blue') {
                return '#2f3b49';
            } else if (Store.page.channel.temp.cover_color == 'Dark Green') {
                return '#507e75';
            } else if (Store.page.channel.temp.cover_color == 'Bright Green') {
                return 'rgb(117, 148, 127)';
            } else if (Store.page.channel.temp.cover_color == 'Purple') {
                return '#4d4261';
            } else if (Store.page.channel.temp.cover_color == 'Orange') {
                return '#ffaf40';
            } else if (Store.page.channel.temp.cover_color == 'Pink') {
                return '#ec7daa';
            } else {
                // userStore.cover_color == 'Black'
                return '#424242';
            }
        }
    }
};
</script>
