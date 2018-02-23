<template>
    <section class="padding-bottom-10 flex1" id="submissions" :class="{'flex-center' : nothingFound}" 
        v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore" @scroll.passive="scrolled"
    >
        <submission :list="submission" v-for="submission in uniqueList" v-bind:key="submission.id"></submission>
    
        <no-content v-if="nothingFound" :text="'No submissions here'"></no-content>
    
        <loading v-if="loading && page > 1"></loading>
    
        <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound && !loading"></no-more-items>
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

    beforeRouteEnter(to, from, next) {
        if (
            typeof Store.page.channel.temp.name != 'undefined' &&
            Store.page.channel.temp.name != to.params.name
        ) {
            Store.page.channel.clear();
        }

        if (Store.page.channel.page === 0) {
            if (typeof app != 'undefined') {
                app.$Progress.start();
            }

            Promise.all([
                Store.page.channel.getSubmissions(
                    to.query.sort,
                    to.params.name
                ),
                Store.page.channel.getChannel(to.params.name)
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

        Store.page.channel.clear();

        this.$Progress.start();

        Promise.all([
            Store.page.channel.getSubmissions(to.query.sort, to.params.name),
            Store.page.channel.getChannel(to.params.name, false)
        ]).then((values) => {
            Store.page.channel.setChannel(values[1]);
            this.setPageTitle('#' + to.params.name);
            this.$Progress.finish();
            next();
        });
    },

    created() {
        this.$eventHub.$on('refresh-channel-submissions', this.refresh);
        this.setPageTitle('#' + this.$route.params.name);
    },

    computed: {
        NoMoreItems() {
            return Store.page.channel.NoMoreItems;
        },

        nothingFound() {
            return Store.page.channel.nothingFound;
        },

        submissions() {
            return Store.page.channel.submissions;
        },

        loading() {
            return Store.page.channel.loading;
        },

        page() {
            return Store.page.channel.page;
        },

        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        },

        sort() {
            return this.$route.query.sort ? this.$route.query.sort : 'hot';
        },

        uniqueList() {
            return _.uniqBy(Store.page.channel.submissions, 'id');
        }
    },

    methods: {
        loadMore() {
            Store.page.channel.getSubmissions(
                this.sort,
                this.$route.params.name
            );
        },

        refresh() {
            Store.page.channel.clear();
            Store.page.channel.getSubmissions(
                this.sort,
                this.$route.params.name
            );
        }
    }
};
</script>
