<template>
    <el-dialog
            :title="'Rules of #' + $route.params.name"
            :visible="visible"
            :width="isMobile ? '99%' : '550px'"
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

        <div class="flex-center" v-show="loading">
            <loading></loading>
        </div>

        <ol>
            <li v-for="rule in rules" :key="rule.id">
                <markdown :text="rule.body"></markdown>
            </li>
        </ol>

        <div class="align-center user-select" v-if="nothingFound">
            <h3 class="v-bold" v-text="'No exlusive rules specified yet'"></h3>
        </div>
    </el-dialog>
</template>

<script>
import Markdown from '../components/Markdown.vue';
import Helpers from '../mixins/Helpers';
import Loading from '../components/SimpleLoading.vue';

export default {
    components: { Markdown, Loading },

    mixins: [Helpers],

    props: ['visible'],

    data() {
        return {
            nothingFound: false,
            loading: true,
            rules: []
        };
    },

    created() {
        this.getRules();
    },

    methods: {
        getRules() {
            this.loading = true;

            axios
                .get(`/channels/${Store.page.channel.temp.id}/rules`)
                .then((response) => {
                    this.rules = response.data.data;

                    if (!this.rules.length) {
                        this.nothingFound = true;
                    }

                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        },

        close() {
            this.$emit('update:visible', false);
        }
    }
};
</script>
