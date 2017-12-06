<template>
	<section class="bookmarked-items" :class="{'flex-center' : nothingFound}"
			 v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore"
	>
		<bookmarked-category v-for="category in categories" :list="category" :key="category.id"></bookmarked-category>

	    <no-content v-if="nothingFound" :text="'No bookmarked channels yet'"></no-content>

		<div class="flex-center padding-top-bottom-1" v-if="loading && page > 1">
			<i class="el-icon-loading"></i>
		</div>

		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</section>
</template>

<script>
    import BookmarkedCategory from '../components/BookmarkedCategory.vue';
	import NoMoreItems from '../components/NoMoreItems.vue';
    import NoContent from '../components/NoContent.vue';
	import Helpers from '../mixins/Helpers'; 

    export default {
		mixins: [Helpers], 

        components: {
        	NoContent,
        	BookmarkedCategory,
			NoMoreItems
        },

        computed: {
            cantLoadMore() {
                return this.loading || this.NoMoreItems || this.nothingFound;
			},
			
			NoMoreItems() {
				return Store.page.bookmarkedCategories.NoMoreItems;
			},

			nothingFound() {
				return Store.page.bookmarkedCategories.nothingFound;
			},

			categories() {
				return Store.page.bookmarkedCategories.categories;
			},

			loading() {
				return Store.page.bookmarkedCategories.loading;
			},

			page() {
				return Store.page.bookmarkedCategories.page;
			}
        },

        methods: {
			loadMore () {
                Store.page.bookmarkedCategories.getCategories(); 
            },
		}, 

		beforeRouteEnter (to, from, next) {
			if (! Store.page.bookmarkedCategories.page > 0) {
				if (typeof app != "undefined") {
					app.$Progress.start();
				}

				Store.page.bookmarkedCategories.getCategories().then(() => {
					next(vm => vm.$Progress.finish());
				});
			} else {
				next();
			}
		},
	}
</script>
