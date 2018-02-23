<template>
    <section class="bookmarked-items" v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore"
             :class="{'flex-center' : nothingFound}">
        <submission v-for="submission in submissions" :list="submission" :key="submission.id"></submission>

        <no-content v-if="nothingFound" :text="'No bookmarked submissions yet'" icon="submission"></no-content>

        <loading v-if="loading && page > 1"></loading>

        <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
    </section>
</template>

<script>
import Submission from '../components/Submission.vue';
import NoContent from '../components/NoContent.vue';
import Loading from '../components/Loading.vue';
import NoMoreItems from '../components/NoMoreItems.vue';
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    components: {
        Submission,
        NoContent,
        Loading,
        NoMoreItems
    },

    computed: {
        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        },

        NoMoreItems() {
            return Store.page.bookmarkedSubmissions.NoMoreItems;
        },

        nothingFound() {
            return Store.page.bookmarkedSubmissions.nothingFound;
        },

        submissions() {
            return Store.page.bookmarkedSubmissions.submissions;
        },

        loading() {
            return Store.page.bookmarkedSubmissions.loading;
        },

        page() {
            return Store.page.bookmarkedSubmissions.page;
        }
    },

    beforeRouteEnter(to, from, next) {
        if (!Store.page.bookmarkedSubmissions.page > 0) {
            if (typeof app != 'undefined') {
                app.$Progress.start();
            }

            Store.page.bookmarkedSubmissions.getSubmissions().then(() => {
                next((vm) => vm.$Progress.finish());
            });
        } else {
            next();
        }
    },

    methods: {
        loadMore() {
            Store.page.bookmarkedSubmissions.getSubmissions();
        }
    }
};
</script>
