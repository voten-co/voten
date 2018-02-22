<template>
	<div class="padding-bottom-10 flex1" :class="{'flex-center' : nothingFound}" id="submissions" 
		v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore" @scroll.passive="scrolled"
	>
		<submission :list="submission" v-for="submission in submissions" v-bind:key="submission.id"></submission>
	
		<no-content v-if="nothingFound" :text="'This user has not submitted anything yet'"></no-content>
	
		<loading v-if="loading && page > 1"></loading>
	
		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</div>
</template>

<script>
import Submission from '../components/Submission.vue';
import NoMoreItems from '../components/NoMoreItems.vue';
import NoContent from '../components/NoContent.vue';
import UserHeader from '../components/UserHeader.vue';
import Helpers from '../mixins/Helpers';
import Loading from '../components/Loading.vue';

export default {
    mixins: [Helpers],

    components: {
        Submission,
        NoContent,
        UserHeader,
        NoMoreItems,
        Loading
    },

    computed: {
        NoMoreItems() {
            return Store.page.user.submissions.NoMoreItems;
        },

        submissions() {
            return Store.page.user.submissions.submissions;
        },

        loading() {
            return Store.page.user.submissions.loading;
        },

        page() {
            return Store.page.user.submissions.page;
        },

        nothingFound() {
            return Store.page.user.submissions.nothingFound;
        },

        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        }
    },

    created() {
        this.setPageTitle('Submissions | @' + this.$route.params.username);
    },

    beforeRouteEnter(to, from, next) {
        if (
            typeof Store.page.user.temp.username != 'undefined' &&
            Store.page.user.temp.username != to.params.username
        ) {
            Store.page.user.submissions.clear();
        }

        if (Store.page.user.submissions.page === 0) {
            if (typeof app != 'undefined') {
                app.$Progress.start();
            }

            Promise.all([
                Store.page.user.submissions.getSubmissions(to.params.username),
                Store.page.user.getUser(to.params.username)
            ]).then(() => {
                next((vm) => {
                    vm.$Progress.finish();
                });
            });
        } else {
            next((vm) => {
                vm.$Progress.finish();
            });
        }
    },

    beforeRouteUpdate(to, from, next) {
        if (to.hash !== from.hash) return;

        Store.page.user.submissions.clear();

        this.$Progress.start();

        Promise.all([
            Store.page.user.submissions.getSubmissions(to.params.username),
            Store.page.user.getUser(to.params.username, false)
        ]).then((values) => {
            Store.page.user.setUser(values[1]);
            this.setPageTitle('Submissions | @' + to.params.username);
            this.$Progress.finish();
            next();
        });
    },

    methods: {
        loadMore() {
            Store.page.user.submissions.getSubmissions(
                this.$route.params.username
            );
        }
    }
};
</script>
