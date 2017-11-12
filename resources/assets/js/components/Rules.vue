<template>
    <el-dialog
            :title="'Rules of #' + $route.params.name"
            :visible="visible"
            :width="isMobile ? '99%' : '35%'"
            @close="close"
            append-to-body
    >
        <p>
            Just like any other channel on Voten, all submissions must follow Voten's
            <a href="/tos">
                <b>general rules</b>
            </a>.
            Other than that, here are a few simple rules exclusively for submitting to #{{ $route.params.name }}:
        </p>

        <ol>
            <li v-for="rule in rules" :key="rule.id">
                <markdown :text="rule.title"></markdown>
            </li>
        </ol>

        <div class="align-center user-select" v-if="nothingFound">
            <h3 class="v-bold" v-text="'No exlusive rules specified yet'"></h3>
        </div>

        <span slot="footer" class="dialog-footer">
            <el-button @click="close" size="medium">
                Close
            </el-button>
        </span>
    </el-dialog>
</template>

<script>
    import Markdown from '../components/Markdown.vue'
    import Helpers from '../mixins/Helpers';


    export default {
        components: { Markdown },

        mixins: [Helpers],

        props: ['visible'],

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
            getRules() {
                axios.get('/rules', {
                    params: {
                        name: this.$route.params.name
                    }
                }).then((response) => {
                    this.rules = response.data;

                    if (!this.rules.length) {
                        this.nothingFound = true;
                    }
                });
            },

            close() {
                this.$emit('update:visible', false);
            },
        },
    }
</script>