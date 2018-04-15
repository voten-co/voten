<template>
	<section id="reported-items">
		<h3 class="dotted-title">
			<span>
				Reported Submissions
			</span>
		</h3>

		<p>
			All reports submitted by users are displayed here for you to moderate. As a moderator you will get a notification when a report is submitted unless you prefer otherwise which you can set in your settings.
		</p>

		<div class="tabs is-fullwidth">
			<ul>
				<router-link tag="li"
				             active-class="is-active"
				             :to="{ path: '' }"
				             exact>
					<a>
						Unsolved
					</a>
				</router-link>

				<router-link tag="li"
				             active-class="is-active"
				             :to="{ path: '?type=solved' }"
				             exact>
					<a>
						Solved
					</a>
				</router-link>
			</ul>
		</div>

		<div class="flex-center"
		     v-show="loading">
			<loading></loading>
		</div>

		<div class="no-more-to-load user-select"
		     v-if="nothingFound">
			<h3 v-text="'No records were found'"></h3>
		</div>

		<reported-submission v-for="item in items"
		                     :list="item"
		                     :key="item.id"
		                     v-if="item.submission"
		                     @disapprove-submission="disapproveSubmission"
		                     @approve-submission="approveSubmission"></reported-submission>
	</section>
</template>

<script>
import Loading from '../components/SimpleLoading.vue';
import ReportedSubmission from '../components/ReportedSubmission.vue';

export default {
    components: {
        Loading,
        ReportedSubmission
    },

    data() {
        return {
            NoMoreItems: false,
            loading: true,
            nothingFound: false,
            items: [],
            page: 0,
            Store
        };
    },

    computed: {
        type() {
            if (this.$route.query.type == 'solved') {
                return 'solved';
            }

            if (this.$route.query.type == 'deleted') {
                return 'deleted';
            }

            return 'unsolved';
        }
    },

    created: function() {
        this.getItems();
        this.$eventHub.$on('scrolled-to-bottom', this.loadMore);
    },

    watch: {
        type: function() {
            this.clearContent();
            this.getItems();
        }
    },

    methods: {
        disapproveSubmission(submission_id) {
            axios.post(`/submissions/${submission_id}/disapprove`).then(response => {
                this.items = this.items.filter(item => item.submission.id != submission_id);

                if (!this.items.length) {
                    this.nothingFound = true;
                }
            });
        },

        approveSubmission(submission_id) {
            axios.post(`/submissions/${submission_id}/approve`).then(response => {
                this.items = this.items.filter(item => item.submission.id != submission_id);

                if (!this.items.length) {
                    this.nothingFound = true;
                }
            });
        },

        loadMore() {
            if (!this.loading && !this.NoMoreItems) {
                this.getItems();
            }
        },

        /**
         * Resets all the basic data
         *
         * @return void
         */
        clearContent() {
            this.nothingFound = false;
            this.items = [];
            this.loading = true;
            this.page = 0;
        },

        getItems() {
            this.page++;
            this.loading = true;
            app.$Progress.finish();
            app.$Progress.start();

            axios
                .get(`/channels/${Store.page.channel.temp.id}/submissions/reported`, {
                    params: {
                        type: this.type,
                        page: this.page,
                        with_reporter: 1,
                        with_submission: 1
                    }
                })
                .then(response => {
                    this.items = [...this.items, ...response.data.data];

                    if (!this.items.length) {
                        this.nothingFound = true;
                    }

                    if (response.data.links.next == null) {
                        this.NoMoreItems = true;
                    }

                    this.loading = false;
                    app.$Progress.finish();
                })
                .catch(error => {
                    app.$Progress.fail();
                });
        }
    }
};
</script>

<style>
#reported-items .fond {
    padding-top: 7%;
}
</style>
