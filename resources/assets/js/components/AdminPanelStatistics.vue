<template>
	<div class="container margin-top-1">
        <div class="col-7 user-select">
        	<h1 class="v-bold">Statistics:</h1>

			<div class="flex-display">
				<div class="admin-statistics">
					<img src="/imgs/admin/users.svg"
					data-toggle="tooltip" data-placement="right" title="Registered Users">
					<div class="align-left">
						<h2>Today: {{ statistics.usersToday }}</h2>
						<h2>Total: {{ statistics.usersTotal }}</h2>
					</div>
				</div>

				<div class="admin-statistics">
					<img src="/imgs/admin/channels.svg"
					data-toggle="tooltip" data-placement="left" title="Created Channels">
					<div class="align-left">
						<h2>Today: {{ statistics.categoriesToday }}</h2>
						<h2>Total: {{ statistics.categoriesTotal }}</h2>
					</div>
				</div>
			</div>

			<div class="flex-display">
				<div class="admin-statistics">
					<img src="/imgs/admin/submissions.svg"
					data-toggle="tooltip" data-placement="right" title="Submitted Submissions">
					<div class="align-left">
						<h2>Today: {{ statistics.submissionsToday }}</h2>
						<h2>Total: {{ statistics.submissionsTotal }}</h2>
					</div>
				</div>

				<div class="admin-statistics">
					<img src="/imgs/admin/comments.svg"
					data-toggle="tooltip" data-placement="left" title="Left Comments">
					<div class="align-left">
						<h2>Today: {{ statistics.commentsToday }}</h2>
						<h2>Total: {{ statistics.commentsTotal }}</h2>
					</div>
				</div>
			</div>

			<div class="flex-display">
				<div class="admin-statistics">
					<img src="/imgs/admin/messages.svg"
					data-toggle="tooltip" data-placement="right" title="Sent Messages">
					<div class="align-left">
						<h2>Today: {{ statistics.messagesToday }}</h2>
						<h2>Total: {{ statistics.messagesTotal }}</h2>
					</div>
				</div>

				<div class="admin-statistics">
					<img src="/imgs/admin/votes.svg"
					data-toggle="tooltip" data-placement="left" title="Votes">
					<div class="align-left">
						<h2>Today: {{ statistics.submissionVotesToday }}</h2>
						<h2>Total: {{ statistics.submissionVotesTotal }}</h2>
					</div>
				</div>
			</div>

			<div class="flex-display">
				<div class="admin-statistics">
					<img src="/imgs/admin/reports.svg"
					data-toggle="tooltip" data-placement="right" title="Submitted Reports">
					<div class="align-left">
						<h2>Today: {{ statistics.reportsToday }}</h2>
						<h2>Total: {{ statistics.reportsTotal }}</h2>
					</div>
				</div>


				<!-- <div class="admin-statistics">
					<img src="/imgs/admin/votes.svg"
					data-toggle="tooltip" data-placement="left" title="Votes">
					<div class="align-left">
						<h2>Today: {{ statistics.submissionVotesToday }}</h2>
						<h2>Total: {{ statistics.submissionVotesTotal }}</h2>
					</div>
				</div> -->
			</div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                statistics: []
            }
        },

        created () {
            this.getStatistics()
        },

        mounted () {
			this.$nextTick(function () {
				this.$root.loadSemanticTooltip()
			})
        },

        methods: {
            getStatistics () {
            	axios.post('/big-daddy').then((response) => {
	                this.statistics = response.data
	            })
            },
        }
    };
</script>
