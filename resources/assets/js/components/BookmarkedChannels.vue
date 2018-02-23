<template>
	<section class="bookmarked-items" :class="{'flex-center' : nothingFound}"
			 v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore"
	>
		<div class="index-channels">
			<bookmarked-channel v-for="channel in channels" :list="channel" :key="channel.id"></bookmarked-channel>
		</div>

	    <no-content v-if="nothingFound" :text="'No bookmarked channels yet'" icon="channel"></no-content>
		<loading v-if="loading && page > 1"></loading>
		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</section>
</template>

<script>
import BookmarkedChannel from '../components/BookmarkedChannel.vue';
import NoMoreItems from '../components/NoMoreItems.vue';
import NoContent from '../components/NoContent.vue';
import Loading from '../components/Loading.vue';
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    components: {
        NoContent,
        Loading,
        BookmarkedChannel,
        NoMoreItems
    },

    computed: {
        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        },

        NoMoreItems() {
            return Store.page.bookmarkedChannels.NoMoreItems;
        },

        nothingFound() {
            return Store.page.bookmarkedChannels.nothingFound;
        },

        channels() {
            return Store.page.bookmarkedChannels.channels;
        },

        loading() {
            return Store.page.bookmarkedChannels.loading;
        },

        page() {
            return Store.page.bookmarkedChannels.page;
        }
    },

    methods: {
        loadMore() {
            Store.page.bookmarkedChannels.getChannels();
        }
    },

    beforeRouteEnter(to, from, next) {
        if (!Store.page.bookmarkedChannels.page > 0) {
            if (typeof app != 'undefined') {
                app.$Progress.start();
            }

            Store.page.bookmarkedChannels.getChannels().then(() => {
                next((vm) => vm.$Progress.finish());
            });
        } else {
            next();
        }
    }
};
</script>
