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
                            {{ '@' + list.username }}
                        </router-link>
                    </h2>

                    <div class="flex-align-center">
                        <el-tooltip :content="bookmarked ? 'Unbookmark' : 'Bookmark'" placement="top"
                                    transition="false" :open-delay="500">
                            <i class="v-icon h-yellow pointer"
                               :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark go-gray'" @click="bookmark"></i>
                        </el-tooltip>
                        
                        <el-tooltip content="Start a private conversation" placement="top"
                                    transition="false" :open-delay="500">
                            <i class="v-icon go-green v-chat pointer" @click="sendMessage(list)"></i>
                        </el-tooltip>
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
            visible: true
        };
    },

    props: ['list'],

    computed: {
        bookmarked: {
            get() {
                return Store.state.bookmarks.users.indexOf(this.list.id) !== -1
                    ? true
                    : false;
            },

            set() {
                if (Store.state.bookmarks.users.indexOf(this.list.id) !== -1) {
                    let index = Store.state.bookmarks.users.indexOf(
                        this.list.id
                    );
                    Store.state.bookmarks.users.splice(index, 1);

                    return;
                }

                Store.state.bookmarks.users.push(this.list.id);
            }
        }
    },

    methods: {
        bookmark: _.debounce(
            function() {
                this.bookmarked = !this.bookmarked;

                axios
                    .post(`/users/${this.list.id}/bookmark`)
                    .catch(() => {
                        this.bookmarked = !this.bookmarked;
                    });
            },
            200,
            { leading: true, trailing: false }
        ),

        sendMessage(user) {
            this.$eventHub.$emit('start-conversation', user);
        }
    }
};
</script>
