<template>
    <el-dialog
            title="Moderators"
            :visible="visible"
            :width="isMobile ? '99%' : '35%'"
            @close="close"
            append-to-body
            class="user-select"
    >
        <div class="flex-center" v-show="loading">
            <loading></loading>
        </div>

        <div class="small-modal-user" v-for="user in list" :key="user.id">
            <div>
                <router-link :to="'/@' + user.username">
                    <img :src="user.avatar" :alt="user.username">
                </router-link>

                <router-link :to="'/@' + user.username">
                    {{ '@' + user.username }}
                </router-link>
            </div>

            <div>
                <el-button type="success" plain size="mini" @click="sendMessage(user)"
                           v-if="user.username !== auth.username">
                    Send a message
                </el-button>
            </div>
        </div>

        <span slot="footer" class="dialog-footer">
            <el-button @click="close" size="medium">
                Close
            </el-button>
        </span>
    </el-dialog>
</template>

<style>
    .small-modal-user {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .small-modal-user img {
        width: 4em;
        height: auto;
        margin: 1em;
        margin-left: 0;
        border-radius: 50%;
        border: 1px solid #635d5d;
    }
</style>

<script>
    import Loading from '../components/SimpleLoading.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        mixins: [Helpers],

        components: {
            Loading
        },

        props: ['visible'],

        data () {
            return {
                list: [],
                loading: true,
            }
        },

        created() {
            this.getModerators();
        },

        methods: {
            getModerators() {
                axios.get('/category-moderators', {
                    params: {
                        name: this.$route.params.name
                    }
                }).then((response) => {
                    this.list = response.data;
                    this.loading = false;
                });
            },

            close() {
                this.$emit('update:visible', false);
            },

            sendMessage(user) {
                this.$eventHub.$emit('start-conversation', user);
                this.close();
            }
        },
    }
</script>