<template>
    <div class="padding-bottom-10 flex1" id="submissions" :class="{'flex-center' : nothingFound}"
         v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore"
    >
        <submission :list="submission" v-for="submission in uniqueList" v-bind:key="submission.id"></submission>

        <no-content v-if="nothingFound" :text="'No submissions here yet'"></no-content>

        <div class="flex-center padding-top-bottom-1" v-if="loading && page > 1">
            <i class="el-icon-loading"></i>
        </div>

        <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound && !loading"></no-more-items>
    </div>
</template>

<script>
    import Submission from '../components/Submission.vue';
    import NoContent from '../components/NoContent.vue';
    import NoMoreItems from '../components/NoMoreItems.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        mixins: [Helpers],

        components: {
            Submission,
            NoContent,
            NoMoreItems
        },

        data() {
            return {
                preload,
            }
        },

        beforeRouteEnter (to, from, next) {
            if (typeof app != "undefined") {
                app.$Progress.start();
            }

            Store.page.category.clear();

            Promise.all([Store.page.category.getSubmissions(to.query.sort, to.params.name), Store.page.category.getCategory(to.params.name)]).then(() => {
                next(vm => {
                    vm.$Progress.finish();
                });
            });
        },

        beforeRouteUpdate (to, from, next) {
            Store.page.category.clear();

            this.$Progress.start();

            Promise.all([Store.page.category.getSubmissions(to.query.sort, to.params.name), Store.page.category.getCategory(to.params.name, false)]).then(values => {
                Store.page.category.setCategory(values[1]);
                this.setPageTitle('#' + to.params.name);
                this.$Progress.finish();
                next();
            });
        },

        created () {
            this.$eventHub.$on('refresh-category-submissions', this.refresh);
            this.setPageTitle('#' + this.$route.params.name);
        },

        computed: {
            NoMoreItems() {
                return Store.page.category.NoMoreItems;
            },

            nothingFound() {
                return Store.page.category.nothingFound;
            },

            submissions() {
                return Store.page.category.submissions;
            },

            loading() {
                return Store.page.category.loading;
            },

            page() {
                return Store.page.category.page;
            },

            cantLoadMore() {
                return this.loading || this.NoMoreItems || this.nothingFound;
            },

            sort() {
                return this.$route.query.sort ? this.$route.query.sort : 'hot';
            },

            uniqueList() {
                return _.uniq(Store.page.category.submissions);
            }
        },

        methods: {
            loadMore() {
                Store.page.category.getSubmissions(this.sort, this.$route.params.name);
            },

            refresh() {
                Store.page.category.clear(); 
                Store.page.category.getSubmissions(this.sort, this.$route.params.name);
            }
        }
    };
</script>
