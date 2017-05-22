<template>
	<div class="container margin-top-1">
        <div class="col-7">
		    <submission :list="submission" v-for="submission in uniqueList" v-bind:key="submission.id"></submission>

		    <loading v-show="loading"></loading>

		    <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	    </div>
    </div>
</template>

<script>
    import Loading from '../components/Loading.vue'
    import Submission from '../components/Submission.vue'
    import NoContent from '../components/NoContent.vue'
	import NoMoreItems from '../components/NoMoreItems.vue'

    export default {
        components: {
            Submission,
            Loading,
            NoContent,
            NoMoreItems
        },

        data: function () {
            return {
	            NoMoreItems: false,
				loading: true,
                nothingFound: false,
                submissions: [],
				page: 0
            }
        },

        created: function() {
            this.getSubmissions()
			this.$eventHub.$on('scrolled-to-bottom', this.loadMore)
        },

	    watch: {
			'$route': function () {
				this.clearContent()
				this.getSubmissions()
			}
		},

		computed: {
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
			loadMore () {
				if ( Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems ) {
					this.getSubmissions()
				}
			},

        	clearContent () {
				this.nothingFound = false
				this.NoMoreItems = false
				this.submissions = []
				this.loading = true
        	},

            getSubmissions: function(){
				this.page ++
           		this.loading = true

                axios.post('/admin/submissions', {
                	page: this.page
                }).then((response) => {
                    this.submissions = [...this.submissions, ...response.data.data]

					if (response.data.next_page_url == null) {
						this.NoMoreItems = true
					}

					if(this.submissions.length == 0){
						this.nothingFound = true
					}

					this.loading = false
                })
            }
        }
    };
</script>
