<template>
    <div class="v-modal-small" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box" v-on-clickaway="close">
            <div class="flex1">
                <h2 class="align-center">
                    Moderators
                </h2>

                <loading v-show="loading"></loading>

                <div class="small-modal-user" v-for="user in list">
                    <router-link :to="'/@' + user.username">
                        <img :src="user.avatar" :alt="user.username">
                    </router-link>

                    <router-link :to="'/@' + user.username">
                        {{ '@' + user.username }}
                    </router-link>
                </div>

                <button type="button" class="v-button v-button--green v-button--block" @click="close">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<style>
    .small-modal-user {
        /*display: flex;
        justify-content: space-between;
        align-items: center;*/
    }

    .small-modal-user img {
        width: 4em;
        height: auto;
        margin: 1em;
        border-radius: 50%;
        border: 1px solid #635d5d;
    }
</style>

<script>
import Loading from '../components/Loading.vue'
import { mixin as clickaway } from 'vue-clickaway';

export default {
	props: ['sidebar'],

    mixins: [ clickaway ],

    components: {
        Loading
    },

    data () {
        return {
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
                this.loading = false
            });
        },

    	/**
    	 * Fires the 'close' event which causes all the modals to be closed.
    	 *
    	 * @return void
    	 */
    	close () {
    		this.$eventHub.$emit('close')
    	},
    },

}

</script>
