<template>
	<section>
		<div v-for="(value, index) in uniqueList" v-bind:key="value.id">
    		<suggested-category v-if="index == 5"></suggested-category>

			<submission :list="value"></submission>
		</div>

	    <no-content v-if="nothingFound" :text="'Oooops, I hate to say it but there are no submissions to show you here'"></no-content>

		<loading v-if="loading"></loading>

		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</section>
</template>

<script>
	import Submission from '../components/Submission.vue';
	import SuggestedCategory from '../components/SuggestedCategory.vue';
	import Loading from '../components/Loading.vue';
	import NoContent from '../components/NoContent.vue';
	import NoMoreItems from '../components/NoMoreItems.vue';
	import LocalStorage from '../mixins/LocalStorage';
	import Helpers from '../mixins/Helpers';

    export default {
    	mixins: [LocalStorage, Helpers],

	    components: {
	        Submission,
	        Loading,
	        SuggestedCategory,
	        NoContent,
			NoMoreItems
	    },

        data: function () {
            return {
				NoMoreItems: false,
				nothingFound: false,
	            submissions: [],
	            loading: true,
				page: 0,
				Store
            }
        },


	    created: function() {
	        this.getSubmissions();
			this.$eventHub.$on('scrolled-to-bottom', this.loadMore);
			this.$eventHub.$on('refresh-home', this.refresh);
	    },

	    watch: {
			'$route': function () {
				this.clearContent()
				this.getSubmissions()
			}
		},

		computed: {
			/**
	    	 * the sort of the page
	    	 *
	    	 * @return string
	    	 */
	    	sort() {
	    	    if (this.$route.query.sort == 'new')
	    	    	return 'new';

	    	    if (this.$route.query.sort == 'rising')
	    	    	return 'rising';

	    	    return 'hot';
	    	},

			/**
			 * Due to the issue with duplicate notifiactions (cuz the present ones have diffrent
			 * timestamps) we need a different approch to make sure the list is always unique.
			 * This ugly coded methods does it! Maybe move this to the Helpers.js mixin?!
			 *
			 * @return array
			 */
			uniqueList() {
				let unique = []
				let temp = []

				this.submissions.forEach(function(element, index, self) {
					if (temp.indexOf(element.id) === -1) {
						unique.push(element)
						temp.push(element.id)
					}
				})

				return unique
			}
		},

	    methods: {
			loadMore() {
				if (Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems && this.$route.name == 'home') {
					this.getSubmissions()
				}
			},

	        getSubmissions() {
				this.page ++;
	            this.loading = true;

	            // if landed on the home page as guest
	        	if (preload.submissions && this.$route.name == 'home') {
	        		this.submissions = preload.submissions.data;

					if (!this.submissions.length) {
						this.nothingFound = true
					}

					if (preload.submissions.next_page_url == null) {
						this.NoMoreItems = true
					}

					this.loading = false;

					delete preload.submissions;

					return;
	        	}

	        	// make sure feedFitler is set
	        	if (this.isSetLS('feed-filter')) {
	   				Store.feedFilter = this.getLS('feed-filter');
	   			} else {
	   				Store.feedFilter = 'subscribed-channels';
	   			}

	            axios.get(this.authUrl('home'), {
	            	params: {
		                sort: this.sort,
		                page: this.page,
		                filter: Store.feedFilter
				    }
	            }).then((response) => {
					this.submissions = [...this.submissions, ...response.data.data]

					if (!this.submissions.length) {
						this.nothingFound = true
					}

					if (response.data.next_page_url == null) {
						this.NoMoreItems = true
					}

					this.loading = false
	            })
	        },

	        /**
	         * Resets all the basic data
	         *
	         * @return void
	         */
	        clearContent() {
				this.nothingFound = false;
				this.NoMoreItems = false;
				this.submissions = [];
				this.loading = true;
				this.page = 0;
	    	},

	    	refresh() {
	    	    this.clearContent();
	    	    this.getSubmissions();
	    	},
	    }
    };
</script>
