<template>
    <div class="v-modal-small" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box" v-on-clickaway="close">
            <div class="flex1">
                <p>
                    Just like any other channel on Voten, all submissions must follow Voten's
                    <a href="/tos">
                    	<b>general rules</b>
                    </a>.
                    Other than that here are a few simple rules exclusively for submitting to #{{ $route.params.name }}:
                </p>

                <ol class="roman-counter-rounded">
                    <li v-for="rule in rules" :key="rule.id">
                        <markdown :text="rule.title"></markdown>
                    </li>
                </ol>

                <div class="align-center user-select" v-if="nothingFound">
                    <h3 class="v-bold" v-text="'No exlusive rules specified yet'"></h3>
                </div>

                <button type="button" class="v-button v-button--green v-button--block" @click="close">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Markdown from '../components/Markdown.vue'
import { mixin as clickaway } from 'vue-clickaway';

export default {
	props: ['sidebar'],

    components:{ Markdown },

    mixins: [ clickaway ],

    data () {
        return {
            nothingFound: false,
            rules: [],
        }
    },

    created: function () {
        this.getRules();
    },

    methods: {
        getRules:function() {
            axios.get('/rules', {
                params: {
                	name: this.$route.params.name
                }
            }).then((response) => {
                this.rules = response.data;

                if (!this.rules.length) {
                    this.nothingFound = true
                }
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
