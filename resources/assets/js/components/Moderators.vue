<template>
    <el-dialog
            title="Moderators"
            :visible="visible"
            :width="isMobile ? '99%' : '550px'"
            @close="close"
            append-to-body
            class="user-select"
    >
        <div class="flex-center" v-show="loading">
            <loading></loading>
        </div>

        <div class="small-modal-user" v-for="item in list" :key="item.user.id">
            <div>
                <router-link :to="'/@' + item.user.username">
                    <img :src="item.user.avatar" :alt="item.user.username">
                </router-link>

                <router-link :to="'/@' + item.user.username">
                    {{ '@' + item.user.username }}
                </router-link>
            </div>

            <div>
                <el-button round type="success" plain size="mini" @click="sendMessage(item.user)"
                           v-if="item.user.username !== auth.username">
                    Send a message
                </el-button>
            </div>
        </div>
    </el-dialog>
</template>

<script>
import Loading from '../components/SimpleLoading.vue';
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    components: {
        Loading
    },

    props: ['visible'],

    data() {
        return {
            list: [],
            loading: true
        };
    },

    created() {
        this.getModerators();
    },

    methods: {
        getModerators() {
            axios
                .get(`/channels/${Store.page.channel.temp.id}/moderators`)
                .then((response) => {
                    this.list = response.data.data;
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        },

        close() {
            this.$emit('update:visible', false);
        },

        sendMessage(user) {
            if (this.isGuest) {
                this.mustBeLogin();
                return;
            }

            this.$eventHub.$emit('start-conversation', user);
            this.close();
        }
    }
};
</script>

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
