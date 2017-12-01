<template>
    <section class="bookmarked-items" v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore"
             :class="{'flex-center' : nothingFound}">
        <submission v-for="submission in submissions" :list="submission" :key="submission.id"></submission>

        <no-content v-if="nothingFound" :text="'No bookmarked submissions yet'"></no-content>

        <loading v-show="loading"></loading>

        <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
    </section>
</template>

<script>
    import Loading from '../components/Loading.vue';
    import Submission from '../components/Submission.vue';
    import NoContent from '../components/NoContent.vue';
    import NoMoreItems from '../components/NoMoreItems.vue';
    import Helpers from '../mixins/Helpers';


    export default {
        mixins: [Helpers],

        components: {
            Loading,
            Submission,
            NoContent,
            NoMoreItems
        },

        data() {
            return {
                NoMoreItems: false,
                loading: true,
                nothingFound: false,
                submissions: [],
                page: 0
            }
        },

        created () {
            this.getSubmissions();
        },

        watch: {
            '$route': function () {
                this.clearContent();
                this.getSubmissions();
            }
        },

        computed: {
            cantLoadMore() {
                return this.loading || this.NoMoreItems || this.nothingFound;
            },
        },

        methods: {
            loadMore() {
                this.getSubmissions();
            },

            clearContent() {
                this.nothingFound = false;
                this.submissions = [];
                this.loading = true;
            },

            getSubmissions () {
                this.page++;
                this.loading = true;

                axios.get('/bookmarked-submissions', {
                    params: {
                        page: this.page
                    }
                }).then((response) => {
                    this.submissions = [...this.submissions, ...response.data.data];

                    if (response.data.next_page_url == null) {
                        this.NoMoreItems = true;
                    }

                    if (this.submissions.length == 0) {
                        this.nothingFound = true;
                    }

                    this.loading = false;
                })
            }
        }
    };
</script>
