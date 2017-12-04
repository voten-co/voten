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

    function getSubmissions(sort = 'hot', categoryName) {
        return new Promise((resolve, reject) => {
            Store.page.category.page++;
            Store.page.category.loading = true;

            // if a guest has landed on a category page
            if (preload.submissions && Store.page.category.page == 1) {
                Store.page.category.submissions = preload.submissions.data;
                if (! Store.page.category.submissions.length) Store.page.category.nothingFound = true;
                if (preload.submissions.next_page_url == null) Store.page.category.NoMoreItems = true;
                Store.page.category.loading = false;
                delete preload.submissions;
                return;
            }

            axios.get(auth.isGuest == true ? '/auth/category-submissions' : '/category-submissions', {
                params: {
                    sort: sort,
                    page: Store.page.category.page,
                    category: categoryName
                }
            }).then((response) => {
                Store.page.category.submissions = [...Store.page.category.submissions, ...response.data.data];

                if (! Store.page.category.submissions.length) Store.page.category.nothingFound = true;
                if (response.data.next_page_url == null) Store.page.category.NoMoreItems = true;

                Store.page.category.loading = false;

                resolve(response);
            }).catch((error) => {
                reject(error);
            });
        });
    }

    function clear() {
        Store.page.category.submissions = [];
        Store.page.category.loading = true;
        Store.page.category.nothingFound = false;
        Store.page.category.NoMoreItems = false;
        Store.page.category.page = 0;
    }

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

            clear();

            Promise.all([getSubmissions(to.query.sort, to.params.name), Store.getCategory(to.params.name)]).then(() => {
                next(vm => {
                    vm.$Progress.finish();
                });
            });
        },

        beforeRouteUpdate (to, from, next) {
            this.clear();

            this.$Progress.start();

            Promise.all([getSubmissions(to.query.sort, to.params.name), Store.getCategory(to.params.name, false)]).then(values => {
                Store.setCategory(values[1]);
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
                getSubmissions(this.sort, this.$route.params.name);
            },

            clear() {
                Store.page.category.submissions = [];
                Store.page.category.loading = true;
                Store.page.category.nothingFound = false;
                Store.page.category.NoMoreItems = false;
                Store.page.category.page = 0;
            },

            refresh() {
                this.clear(); 
                getSubmissions(this.sort, this.$route.params.name);
            }
        }
    };
</script>
