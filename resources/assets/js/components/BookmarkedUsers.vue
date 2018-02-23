<template>
	<section 
		class="bookmarked-items"
		:class="{'flex-center' : nothingFound}"
		v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore"
	>
		<div class="index-channels">
			<bookmarked-user v-for="user in users" :list="user" :key="user.id"></bookmarked-user>			
		</div>

	    <no-content v-if="nothingFound" :text="'No bookmarked users yet'" icon="user"></no-content>
		<loading v-if="loading && page > 1"></loading>
		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</section>
</template>

<script>
import BookmarkedUser from '../components/BookmarkedUser.vue';
import NoMoreItems from '../components/NoMoreItems.vue';
import NoContent from '../components/NoContent.vue';
import Loading from '../components/Loading.vue';
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    components: {
        NoContent,
        Loading,
        BookmarkedUser,
        NoMoreItems
    },

    computed: {
        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        },

        NoMoreItems() {
            return Store.page.bookmarkedUsers.NoMoreItems;
        },

        nothingFound() {
            return Store.page.bookmarkedUsers.nothingFound;
        },

        users() {
            return Store.page.bookmarkedUsers.users;
        },

        loading() {
            return Store.page.bookmarkedUsers.loading;
        },

        page() {
            return Store.page.bookmarkedUsers.page;
        }
    },

    methods: {
        loadMore() {
            Store.page.bookmarkedUsers.getUsers();
        }
    },

    beforeRouteEnter(to, from, next) {
        if (!Store.page.bookmarkedUsers.page > 0) {
            if (typeof app != 'undefined') {
                app.$Progress.start();
            }

            Store.page.bookmarkedUsers.getUsers().then(() => {
                next((vm) => vm.$Progress.finish());
            });
        } else {
            next();
        }
    }
};
</script>
