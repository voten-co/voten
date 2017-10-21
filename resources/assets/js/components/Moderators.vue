<template>
    <div class="v-modal-small">
        <div class="wrapper" v-on-clickaway="close">
            <header class="user-select">
                <h3>
                    Moderators
                </h3>

                <div class="close" @click="close">
                    <i class="v-icon v-cancel-small"></i>
                </div>
            </header>

            <div class="middle">
                <div class="flex1">
                    <loading v-show="loading"></loading>

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
                            <button type="button" class="v-button v-button-outline--green v-button-small" @click="sendMessage(user)" v-if="user.username !== auth.username">
                                Send a message
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <footer>
                <button type="button" class="v-button v-button--green v-button--block" @click="close">
                    Close
                </button>
            </footer>
        </div>
    </div>
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
import Loading from '../components/Loading.vue'
import { mixin as clickaway } from 'vue-clickaway';

export default {
    mixins: [ clickaway ],

    components: {
        Loading
    },

    data () {
        return {
            auth,
            list: [],
            loading: true,
        }
    },

    created: function () {
        this.getModerators();
    },

    methods: {
        getModerators() {
            axios.get( '/category-moderators', {
                params: {
                	name: this.$route.params.name
                }
            }).then((response) => {
                this.list = response.data;
                this.loading = false;
            });
        },

    	close() {
    		this.$eventHub.$emit('close');
    	},

        sendMessage(user) {
            this.$eventHub.$emit('start-conversation', user);
        }
    },
}
</script>