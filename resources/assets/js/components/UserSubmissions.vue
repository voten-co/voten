<template>
    <div class="container margin-top-1 col-7">
	    <div v-for="submission in submissions" v-bind:key="submission.id">
	        <submission :list="submission"></submission>
	    </div>

	    <no-content v-if="nothingFound" :text="'This user has not submitted anything yet'"></no-content>

        <loading v-if="loading"></loading>

        <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
    </div>
</template>

<script>
    import Submission from '../components/Submission.vue';
    import Loading from '../components/Loading.vue';
    import NoMoreItems from '../components/NoMoreItems.vue';
    import NoContent from '../components/NoContent.vue';
    import UserHeader from '../components/UserHeader.vue';
    import Helpers from '../mixins/Helpers';

    export default {
    	mixins: [Helpers],

        components: {
            Submission,
            Loading,
            NoContent,
            UserHeader,
            NoMoreItems,
        },

        data: function () {
            return {
                NoMoreItems: false,
                submissions: [],
                loading: true,
                page: 0,
                nothingFound: false,
            }
        },

       created: function() {
           	this.$eventHub.$on('scrolled-to-bottom', this.loadMore);
        	this.getSubmissions();
        	this.setPageTitle('@' + this.$route.params.username);
       },

       	watch: {
	    	'$route': function () {
				if (this.$route.name !== 'user-submissions') {
					return;
				}
				
	    		this.clearContent()
	    		this.getSubmissions()
	    	}
	    },


        methods: {
            loadMore () {
				if ( Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems && this.$route.name == 'user-submissions' ) {
					this.getSubmissions()
				}
			},

        	clearContent () {
                this.submissions = []
                this.loading = true
                this.nothingFound = false
        	},

        	/**
        	 * Fetches the submissions via ajax call
        	 *
        	 * @return void
        	 */
        	getSubmissions: function () {
                this.page ++
           		this.loading = true

           		// if landed on the user page as guest
	        	if (preload.submissions && this.$route.name == 'user-submissions' && this.page == 1) {
	        		this.submissions = preload.submissions.data;

					if (!this.submissions.length) {
						this.nothingFound = true
					}

					if (preload.submissions.next_page_url == null) {
						this.NoMoreItems = true
					}

					this.loading = false;

					// clear the preload
					delete preload.submissions;

					return;
	        	}

        		axios.get('/user-submissions', {
        			params: {
        				page: this.page,
        				username: this.$route.params.username
        			}
        		}).then((response) => {
                    this.submissions = [...this.submissions, ...response.data.data]

                    if (response.data.next_page_url == null) {
						this.NoMoreItems = true
					}

	                if(this.submissions.length < 1) {
	                	this.nothingFound = true
	                }

                    this.loading = false
	            })
        	},
        },

    }

</script>
