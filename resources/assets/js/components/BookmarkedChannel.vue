<template>
    <transition name="fade">
        <section class="bookmarked-item user-select" v-show="visible">
            <div class="avatar">
                <router-link :to="'/c/' + list.name">
                    <img :src="list.avatar" :alt="list.name">
                </router-link>
            </div>

            <div class="flex1">
                <div class="flex-space">
                    <h2>
                        <router-link :to="'/c/' + list.name">
                            {{ list.name }}
                        </router-link>
                    </h2>

                    <div class="flex-align-center">
                        <el-tooltip :content="bookmarked ? 'Unbookmark' : 'Bookmark'" placement="top" transition="false"
                                    :open-delay="500">
                            <i class="v-icon h-yellow pointer" v-if="!isNewbie"
                               :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'" @click="bookmark"></i>
                        </el-tooltip>

                        <el-button
                                class="margin-left-1"
                                size="mini"
                                :type="subscribed ? 'danger' : 'success'"
                                plain
                                @click="subscribe"
                                v-text="subscribed ? 'Unsubscribe' : 'Subscribe'"
                        ></el-button>
                    </div>
                </div>

                <p>
                    {{ list.description }}
                </p>
            </div>
        </section>
    </transition>
</template>

<script>
    export default {
        data() {
            return {
                Store,
                visible: true
            }
        },

        props: ['list'],

        computed: {
            bookmarked: {
                get() {
                    return Store.state.bookmarks.channels.indexOf(this.list.id) !== -1 ? true : false;
                },

                set() {
                    if (Store.state.bookmarks.channels.indexOf(this.list.id) !== -1) {
                        let index = Store.state.bookmarks.channels.indexOf(this.list.id);
                        Store.state.bookmarks.channels.splice(index, 1);

                        let removeItem = this.list.id; 
                        Store.state.bookmarkedChannels = Store.state.bookmarkedChannels.filter(channel => channel.id != removeItem);

                        return;
                    }

                    Store.state.bookmarks.channels.push(this.list.id);
                    Store.state.bookmarkedChannels.push(this.list);
                }
            },

            subscribed: {
                get() {
                    return Store.state.subscribedAt.indexOf(this.list.id) !== -1 ? true : false;
                },

                set() {
                    if (Store.state.subscribedAt.indexOf(this.list.id) !== -1) {
                        let removeItem = this.list.id;
                        Store.state.subscribedChannels = Store.state.subscribedChannels.filter(channel => channel.id != removeItem);

                        let index = Store.state.subscribedAt.indexOf(this.list.id);
                        Store.state.subscribedAt.splice(index, 1);

                        return;
                    }

                    Store.state.subscribedChannels.push(this.list);
                    Store.state.subscribedAt.push(this.list.id);
                }
            },

            /**
             * Has the user just registered?
             *
             * @return boolean
             */
            isNewbie() {
                return this.$route.query.newbie == 1;
            },
        },

        methods: {
            bookmark: _.debounce(function () {
                this.bookmarked = !this.bookmarked;

                axios.post('/bookmark-channel', {
                    id: this.list.id
                }).catch(() => {
                    this.bookmarked = !this.bookmarked;
                });
            }, 200, { leading: true, trailing: false }),

            subscribe: _.debounce(function () {
                this.subscribed = !this.subscribed;

                axios.post('/subscribe', {
                    channel_id: this.list.id
                }).catch(() => {
                    this.subscribed = !this.subscribed;
                });

                this.$emit('subscribed');
            }, 200, { leading: true, trailing: false }),
        },
    };
</script>
