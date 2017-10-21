<template>
	<div class="padding-bottom-10 flex1" id="submissions" @scroll="scrolled" :class="{'flex-center' : nothingFound}">
		<submission :list="submission" v-for="submission in uniqueList" v-bind:key="submission.id"></submission>

		<no-content v-if="nothingFound" :text="'No submissions here yet'"></no-content>

		<loading v-if="loading"></loading>

		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound && !loading"></no-more-items>
	</div>
</template>

<script>
import Submission from '../components/Submission.vue';
import Loading from '../components/Loading.vue';
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

    data: function () {
        return {
			isActive: null, 
			NoMoreItems: false,
			nothingFound: false,
        	Store,
        	preload,
            submissions: [],
            loading: true,
			page: 0
        }
	},

   	created () {
		this.clear(); 
		this.$eventHub.$on('scrolled-to-bottom', this.loadMore); 
		this.$eventHub.$on('refresh-category-submissions', this.clear); 
	},

	watch: {
		'$route': function() {
			if (this.$route.name !== 'category-submissions' || this.isActive === false) {
				return;
			}

			this.clear();
			this.$eventHub.$on('scrolled-to-bottom', this.loadMore);
			this.$eventHub.$on('refresh-category-submissions', this.clear); 
		}
	},

	computed: {
    	sort() {
    	    if (this.$route.query.sort == 'new')
    	    	return 'new';

    	    if (this.$route.query.sort == 'rising')
    	    	return 'rising';

    	    return 'hot';
    	},

		uniqueList() {
			return _.uniq(this.submissions);
		}
	},

    methods: {
		loadMore () {
			if (Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems && this.$route.name == 'category-submissions') {
				this.getSubmissions();
			}
		},
        
    	clear () {
            this.submissions = []; 
            this.loading = true; 
            this.nothingFound = false; 
            this.NoMoreItems = false; 
			this.page = 0;

			this.updateCategoryStore(); 
			this.getSubmissions(); 
    	},
    	
    	updateCategoryStore () {
			this.$root.getCategoryStore(this.$route.params.name); 
    	},

    	getSubmissions () {
			this.page ++; 
            this.loading = true; 

            // if landed on a category page
        	if (preload.submissions && this.page == 1) {
        		this.submissions = preload.submissions.data;

				if (!this.submissions.length) {
					this.nothingFound = true; 
				}

				if (preload.submissions.next_page_url == null) {
					this.NoMoreItems = true; 
				}

				this.loading = false;

				// clear the preload
				delete preload.submissions;

				return;
        	}

            axios.get(this.authUrl('category-submissions'), {
            	params: {
			    	sort: this.sort,
	                page: this.page,
	                category: this.$route.params.name
			    }
            }).then((response) => {
				this.submissions = [...this.submissions, ...response.data.data]; 

				if (!this.submissions.length) {
					this.nothingFound = true; 
				}

				if (response.data.next_page_url == null) {
					this.NoMoreItems = true; 
				}

				this.loading = false; 
            });
        }
    }
};
</script>
