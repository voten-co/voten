<template>
	<section>
		<bookmarked-category v-for="category in categories" :list="category" :key="category.id"></bookmarked-category>

	    <no-content v-if="nothingFound" :text="'You have not bookmarked any channels yet'"></no-content>

		<loading v-show="loading"></loading>

		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</section>
</template>

<script>
    import Loading from '../components/Loading.vue';
    import BookmarkedCategory from '../components/BookmarkedCategory.vue';
	import NoMoreItems from '../components/NoMoreItems.vue';
    import NoContent from '../components/NoContent.vue';

    export default {
        components: {
        	NoContent,
        	BookmarkedCategory,
        	Loading,
			NoMoreItems
        },

        data: function () {
            return {
				NoMoreItems: false,
				loading: true,
                nothingFound: false,
				page: 0,
                categories: []
            }
        },

        created () {
			this.$eventHub.$on('scrolled-to-bottom', this.loadMore)
            this.getCategories()
        },

	    watch: {
			'$route': function () {
				this.clearContent()
				this.getCategories()
			}
		},


        methods: {
			loadMore () {
				if ( Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems ) {
					this.getCategories()
				}
			},

        	clearContent () {
				this.nothingFound = false
				this.users = []
				this.loading = true
        	},

            getCategories () {
				this.page ++
           		this.loading = true

            	axios.get('/bookmarked-categories', {
            		params: {
                        page: this.page
					}
            	}).then((response) => {
					this.categories = [...this.categories, ...response.data.data]

					if (response.data.next_page_url == null) {
						this.NoMoreItems = true
					}

					if(this.categories.length == 0){
						this.nothingFound = true
					}

					this.loading = false
                })
            }
        }
    };
</script>
