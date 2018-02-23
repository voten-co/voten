<template>
    <div class="home-wrapper">
        <nav class="nav has-shadow user-select">
            <div class="container">
                <h1 class="title">
                    Subscribed Channels
                </h1>
            </div>
        </nav>

        <section class="bookmarked-items" :class="{'flex-center' : nothingFound}"
            v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore"
        >
            <div class="index-channels">
                <subscribed-channel v-for="channel in channels" :list="channel" :key="channel.id"></subscribed-channel>
            </div>

            <no-content v-if="nothingFound" :text="'You have not bookmarked any channels yet'"></no-content>
            <loading v-show="loading && page > 1"></loading>
            <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
        </section>
    </div>
</template>

<script>
import Loading from '../components/Loading.vue';
import SubscribedChannel from '../components/BookmarkedChannel.vue';
import NoMoreItems from '../components/NoMoreItems.vue';
import NoContent from '../components/NoContent.vue';
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    components: {
        NoContent,
        SubscribedChannel,
        Loading,
        NoMoreItems
    },

    computed: {
        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        },

        NoMoreItems() {
            return Store.page.subscribedChannels.NoMoreItems;
        },

        nothingFound() {
            return Store.page.subscribedChannels.nothingFound;
        },

        channels() {
            return Store.page.subscribedChannels.channels;
        },

        loading() {
            return Store.page.subscribedChannels.loading;
        },

        page() {
            return Store.page.subscribedChannels.page;
        }
    },

    beforeRouteEnter(to, from, next) {
        Store.page.subscribedChannels.clear();

        if (!Store.page.subscribedChannels.page > 0) {
            if (typeof app != 'undefined') {
                app.$Progress.start();
            }

            Store.page.subscribedChannels.getChannels().then(() => {
                next((vm) => vm.$Progress.finish());
            });
        } else {
            next();
        }
    },

    methods: {
        loadMore() {
            Store.page.subscribedChannels.getChannels();
        }
    }
};
</script>
