<template>
    <transition name="fade">
        <section class="bookmarked-item user-select" v-show="visible">
            <div class="avatar">
                <router-link :to="'/@' + list.username">
                    <img :src="list.avatar" :alt="list.username">
                </router-link>
            </div>

            <div class="flex1">
                <div class="flex-space">
                    <h2>
                        <router-link :to="'/@' + list.username">
                            <i class="v-icon v-atsign" aria-hidden="true"></i>{{ list.username }}
                        </router-link>
                    </h2>

                    <div class="flex-align-center">
                        <el-tooltip :content="bookmarked ? 'Unbookmark' : 'Bookmark'" placement="left"
                                    transition="false" :open-delay="500">
                            <i class="v-icon h-yellow pointer"
                               :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'" @click="bookmark"></i>
                        </el-tooltip>


                        <el-button
                                class="margin-left-1"
                                size="mini"
                                type="success"
                                plain
                                @click="sendMessage(list)">
                            Message
                        </el-button>
                    </div>
                </div>

                <p>
                    {{ list.bio }}
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
                bookmarked: false,
                visible: true
            }
        },

        props: ['list'],

        created () {
            this.setBookmarked()
        },

        methods: {
            /**
             *  Whether or not user has bookmarked the submission
             *
             *  @return Boolean
             */
            setBookmarked () {
                if (Store.userBookmarks.indexOf(this.list.id) != -1) this.bookmarked = true
            },

            /**
             *  Toggles the user into bookmarks
             */
            bookmark (user) {
                this.bookmarked = !this.bookmarked

                axios.post('/bookmark-user', {
                    id: this.list.id
                }).then((response) => {
                    if (Store.userBookmarks.indexOf(this.list.id) != -1) {
                        var index = Store.userBookmarks.indexOf(this.list.id)
                        Store.userBookmarks.splice(index, 1)

                        return
                    }
                    Store.userBookmarks.push(this.list.id)
                })
            },

            sendMessage(user) {
                this.$eventHub.$emit('start-conversation', user);
            }
        },
    };
</script>
