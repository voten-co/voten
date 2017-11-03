<template>
    <div class="v-modal-small">
        <div class="wrapper" v-on-clickaway="close">
            <header class="user-select">
                <h3>
                    Rules of #{{ $route.params.name }}
                </h3>

                <div class="close" @click="close">
                    <i class="v-icon v-cancel-small"></i>
                </div>
            </header>
            <div class="middle">
                <div class="flex1 margin-bottom-1">
                    <p>
                        Just like any other channel on Voten, all submissions must follow Voten's
                        <a href="/tos">
                            <b>general rules</b>
                        </a>.
                        Other than that, here are a few simple rules exclusively for submitting to #{{ $route.params.name }}:
                    </p>

                    <ol class="roman-counter-rounded">
                        <li v-for="rule in rules" :key="rule.id">
                            <markdown :text="rule.title"></markdown>
                        </li>
                    </ol>

                    <div class="align-center user-select" v-if="nothingFound">
                        <h3 class="v-bold" v-text="'No exlusive rules specified yet'"></h3>
                    </div>
                </div>
            </div>

            <footer>
                <el-button type="success" size="medium" @click="close">
                    Close
                </el-button>
            </footer>
        </div>
    </div>
</template>

<script>
import Markdown from '../components/Markdown.vue'
import { mixin as clickaway } from 'vue-clickaway';

export default {
    components: { Markdown },

    mixins: [ clickaway ],

    data() {
        return {
            nothingFound: false,
            rules: [],
        }
    },

    created() {
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

                if (! this.rules.length) {
                    this.nothingFound = true;
                }
            });
        },

    	close() {
    		this.$eventHub.$emit('close');
    	},
    },
}
</script>