<template>
	<div id="submissions" class="home-submissions" 
		v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore" @scroll.passive="scrolled"
	>
        <submission :list="submission" v-for="submission in uniqueList" v-bind:key="submission.id"></submission>

		<loading v-show="loading"></loading>

		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
    </div>
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
        Submission,
        Loading,
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
        };
    },

    created() {
        this.getSubmissions();
        this.$eventHub.$on('scrolled-to-bottom', this.loadMore);
    },

    watch: {
        $route() {
            this.clearContent();
            this.getSubmissions();
        }
    },

    computed: {
        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        },

        uniqueList() {
            let unique = [];
            let temp = [];

            this.submissions.forEach(function(element, index, self) {
                if (temp.indexOf(element.id) === -1) {
                    unique.push(element);
                    temp.push(element.id);
                }
            });

            return unique;
        }
    },

    methods: {
        loadMore() {
            if (!this.loading && !this.NoMoreItems) {
                this.getSubmissions();
            }
        },

        clearContent() {
            this.nothingFound = false;
            this.NoMoreItems = false;
            this.submissions = [];
            this.loading = true;
        },

        getSubmissions: function() {
            this.page++;
            this.loading = true;

            axios
                .get('/admin/submissions', {
                    params: {
                        page: this.page
                    }
                })
                .then((response) => {
                    this.submissions = [
                        ...this.submissions,
                        ...response.data.data
                    ];

                    if (response.data.links.next == null) {
                        this.NoMoreItems = true;
                    }

                    if (this.submissions.length == 0) {
                        this.nothingFound = true;
                    }

                    this.loading = false;
                });
        }
    }
};
</script>
