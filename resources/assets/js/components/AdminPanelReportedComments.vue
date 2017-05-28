<template>
<section id="reported-items" class="container margin-top-1">
	<div class="col-7 v-box">
		<h1 class="dotted-title">
			<span>
				Reported Comments
			</span>
		</h1>

	    <p>
	        All reports submitted by users are displayed here for you to moderate. As a moderator you will get a notification when a report is submitted unless you prefer otherwise which you can set in your settings.
	    </p>

	    <div class="tabs is-fullwidth">
	        <ul>
	            <router-link tag="li" active-class="is-active" :to="{ path: '' }" exact>
	                <a>
						Unsolved
					</a>
	            </router-link>

	            <router-link tag="li" active-class="is-active" :to="{ path: '?type=solved' }" exact>
	                <a>
						Solved
					</a>
	            </router-link>
	        </ul>
	    </div>

	    <loading v-if="loading"></loading>

	    <div class="no-more-to-load user-select" v-if="nothingFound">
	        <h3 v-text="'No records were found'"></h3>
	    </div>

	    <reported-comment v-for="item in items" :list="item" :key="item.id" v-if="item.comment"
	    @disapprove-comment="disapproveComment" @approve-comment="approveComment"></reported-comment>
	</div>
</section>
</template>

<script>
import Loading from '../components/Loading.vue'
import ReportedComment from '../components/ReportedComment.vue'
import NoContent from '../components/NoContent.vue'

export default {
    components: {
        Loading,
        NoContent,
        ReportedComment
    },

    mixins: [],

    data: function() {
        return {
            NoMoreItems: false,
            loading: true,
            nothingFound: false,
            items: [],
            page: 0,
            Store
        }
    },


    computed: {
        type() {
            if (this.$route.query.type == 'solved') {
                return 'solved'
            }

            if (this.$route.query.type == 'deleted') {
                return 'deleted'
            }

            return 'unsolved'
        }
    },


    created: function() {
        this.getItems()
        this.$eventHub.$on('scrolled-to-bottom', this.loadMore)
    },

    watch: {
        'type': function () {
            this.clearContent()
            this.getItems()
        }
    },

    mounted() {
        //
    },

    methods: {
        disapproveComment(comment_id){
            axios.post('/disapprove-comment', { comment_id }).then((response) => {
                this.items = this.items.filter(function (item) {
				  	return item.comment.id != comment_id
				})

                if (!this.items.length) {
                    this.nothingFound = true
                }
            })
        },

        approveComment(comment_id){
            axios.post('/approve-comment', { comment_id }).then((response) => {
                this.items = this.items.filter(function (item) {
				  	return item.comment.id != comment_id
				})

                if (!this.items.length) {
                    this.nothingFound = true
                }
            })
        },

        loadMore() {
            if (Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems) {
                this.getItems()
            }
        },

        /**
         * Resets all the basic data
         *
         * @return void
         */
        clearContent() {
            this.nothingFound = false
            this.items = []
            this.loading = true
            this.page = 0
        },

        getItems() {
            this.page++;
            this.loading = true

            axios.post('/admin/reported-comments', {
                type: this.type,
                category: this.$route.params.name,
                page: this.page
            }).then((response) => {
                this.items = [...this.items, ...response.data.data]

                if (!this.items.length) {
                    this.nothingFound = true
                }

                if (response.data.next_page_url == null) {
                    this.NoMoreItems = true
                }

                this.loading = false
            })

        }
    },

};
</script>

<style>
    #reported-items .fond {
        padding-top: 7%;
    }
</style>
