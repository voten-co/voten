<template>
    <div id="user-header">
        <div :style="{ background: coverBackground }" class="profile-cover" v-show="showFirstHeader">
            <div class="container user-select full-width">
                <div class="cols-flex">
                    <div class="user-header-left">
                        <div class="profile-avatar">
                            <router-link :to="'/@' + userStore.username">
                                <img :src="userStore.avatar" :alt="userStore.name" class="circle" />
                            </router-link>
                        </div>
                    </div>
    
                    <div class="user-header-middle">
                        <h1>
                            {{ userStore.name }}
                        </h1>
    
                        <router-link :to="'/@' + userStore.username">
                            <h2>
                                {{ '@' + userStore.username }}
                            </h2>
                        </router-link>
    
                        <p v-text="userStore.bio"></p>
    
                        <span class="inline-block">
    							<i class="v-icon v-submissions" aria-hidden="true"></i>{{ userStore.stats.submissions_count
                                }}
    						</span>
    
                        <span class="inline-block">
    							<i class="v-icon v-chat" aria-hidden="true"></i>{{ userStore.stats.comments_count }}
    						</span>
    
                        <span class="inline-block">
    							<i class="v-icon v-calendar" aria-hidden="true"></i>Joined: {{ date }}
    						</span>
    
                        <a :href="userStore.info.website" rel="nofollow" target="_blank" v-if="userStore.info.website" class="inline-block">
                            <i class="v-icon v-link" aria-hidden="true"></i>{{ userStore.info.website }}
                        </a>
    
                        <span v-if="userStore.location" class="inline-block">
    							<i class="v-icon v-location" aria-hidden="true"></i>{{ userStore.location }}
    						</span>
    
                        <span v-if="userStore.info.twitter " class="inline-block">
    							<a :href="'https://twitter.com/' + userStore.info.twitter" rel="nofollow" target="_blank">
    								<i class="v-icon v-twitter" aria-hidden="true"></i>{{ userStore.info.twitter }}
    							</a>
    						</span>
                    </div>
    
                    <div class="user-header-right">
                        <div class="xp">
                            <div class="xp-number">
                                {{ userStore.stats.submission_xp }}
                            </div>
    
                            <div class="xp-text">
                                Post XP
                            </div>
                        </div>
    
                        <div class="xp margin-top-1">
                            <div class="xp-number">
                                {{ userStore.stats.comment_xp }}
                            </div>
    
                            <div class="xp-text">
                                Comment XP
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <nav class="nav has-shadow user-select">
            <div class="container">
                <div class="nav-left">
                    <router-link :to="'/@' + $route.params.username" class="nav-item is-tab" active-class="is-active" exact>
                        Submissions
                    </router-link>
    
                    <router-link :to="'/@' + $route.params.username + '/comments'" class="nav-item is-tab" active-class="is-active" exact>
                        Comments
                    </router-link>

                    <router-link :to="'/@' + $route.params.username + '/upvoted-submissions'" class="nav-item is-tab" active-class="is-active" v-if="isAuth">
                        Upvoted
                    </router-link>
    
                    <router-link :to="'/@' + $route.params.username + '/downvoted-submissions'" class="nav-item is-tab" active-class="is-active" v-if="isAuth">
                        Downvoted
                    </router-link>
                </div>
    
                <div class="channel-admin-btn">
                    <el-tooltip :content="bookmarked ? 'Unbookmark user' : 'Bookmark user'" placement="bottom" transition="false" :open-delay="500">
                        <i class="v-icon h-yellow pointer" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark go-gray'" @click="bookmark" v-if="$route.params.username != auth.username"></i>
                    </el-tooltip>
    
                    <message-button :id="userStore.id" v-if="$route.params.username != auth.username && !isGuest"></message-button>
    
                    <el-button round type="success" 
                        @click="Store.modals.preferences.show = true" 
                        v-if="$route.params.username == auth.username"  
                        size="mini">
                        Edit Profile
                    </el-button>
                </div>
            </div>
        </nav>
    </div>
</template>

<script>
import MessageButton from '../components/MessageButton.vue';
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    components: {
        MessageButton
    },

    data() {
        return {
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
        bookmark: _.debounce(
            function() {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                this.bookmarked = !this.bookmarked;

                axios
                    .post('/bookmark-user', {
                        id: Store.page.user.temp.id
                    })
                    .catch(() => {
                        this.bookmarked = !this.bookmarked;
                    });
            },
            200,
            {
                leading: true,
                trailing: false
            }
        )
    },

    computed: {
        bookmarked: {
            get() {
                return Store.state.bookmarks.users.indexOf(
                    Store.page.user.temp.id
                ) !== -1
                    ? true
                    : false;
            },

            set() {
                if (
                    Store.state.bookmarks.users.indexOf(
                        Store.page.user.temp.id
                    ) !== -1
                ) {
                    let index = Store.state.bookmarks.users.indexOf(
                        Store.page.user.temp.id
                    );
                    Store.state.bookmarks.users.splice(index, 1);

                    return;
                }

                Store.state.bookmarks.users.push(Store.page.user.temp.id);
            }
        },

        isAuth() {
            return auth.username == this.$route.params.username;
        },

        userStore() {
            return Store.page.user.temp;
        },

        date() {
            return moment(this.userStore.created_at)
                .utc(moment().format('MMM Do'))
                .format('MMM Do');
        },

        coverBackground() {
            if (this.userStore.cover_color == 'Red') {
                return '#9a4e4e';
            } else if (this.userStore.cover_color == 'Blue') {
                return '#5487d4';
            } else if (this.userStore.cover_color == 'Dark Blue') {
                return '#2f3b49';
            } else if (this.userStore.cover_color == 'Dark Green') {
                return '#507e75';
            } else if (this.userStore.cover_color == 'Bright Green') {
                return 'rgb(117, 148, 127)';
            } else if (this.userStore.cover_color == 'Purple') {
                return '#4d4261';
            } else if (this.userStore.cover_color == 'Orange') {
                return '#ffaf40';
            } else if (this.userStore.cover_color == 'Pink') {
                return '#ec7daa';
            } else {
                // userStore.cover_color == 'Black'
                return '#424242';
            }
        }
    }
};
</script>
