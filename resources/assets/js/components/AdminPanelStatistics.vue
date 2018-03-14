<template>
	<div id="submissions"
	     class="home-submissions">
		<div class="flex-space">
			<h2>
				Statistics:
			</h2>

			<div>
				<el-button type="info"
				           round
				           :loading="statistics.loading"
				           size="mini"
				           class="margin-left-half"
				           @click="getStatistics">
					Refresh
				</el-button>
			</div>
		</div>

		<div v-if="! statistics.loading">
			<div class="banned-user-wrapper">
				<div class="banned-user">
					<div class="v-bold left">
						Registers
					</div>

					<div class="actions">
						<el-tag size="small"
						        type="success">{{ statistics.list.users.today }}</el-tag>
						<el-tag size="small"
						        type="info">{{ statistics.list.users.week }}</el-tag>
						<el-tag size="small"
						        type="warning">{{ statistics.list.users.month }}</el-tag>
						<el-tag size="small"
						        type="primary">{{ statistics.list.users.total }}</el-tag>
					</div>
				</div>
			</div>

			<div class="banned-user-wrapper">
				<div class="banned-user">
					<div class="v-bold left">
						Active Users
					</div>

					<div class="actions">
						<el-tag size="small"
						        type="success">{{ statistics.list.active_users.today }}</el-tag>
						<el-tag size="small"
						        type="info">{{ statistics.list.active_users.week }}</el-tag>
						<el-tag size="small"
						        type="warning">{{ statistics.list.active_users.month }}</el-tag>
						<el-tag size="small"
						        type="primary">{{ statistics.list.active_users.total }}</el-tag>
					</div>
				</div>
			</div>

			<div class="banned-user-wrapper">
				<div class="banned-user">
					<div class="v-bold left">
						Subscriptions
					</div>

					<div class="actions">
						<el-tag size="small"
						        type="success">{{ statistics.list.subscriptions.today }}</el-tag>
						<el-tag size="small"
						        type="info">{{ statistics.list.subscriptions.week }}</el-tag>
						<el-tag size="small"
						        type="warning">{{ statistics.list.subscriptions.month }}</el-tag>
						<el-tag size="small"
						        type="primary">{{ statistics.list.subscriptions.total }}</el-tag>
					</div>
				</div>
			</div>

			<div class="banned-user-wrapper">
				<div class="banned-user">
					<div class="v-bold left">
						channels
					</div>

					<div class="actions">
						<el-tag size="small"
						        type="success">{{ statistics.list.channels.today }}</el-tag>
						<el-tag size="small"
						        type="info">{{ statistics.list.channels.week }}</el-tag>
						<el-tag size="small"
						        type="warning">{{ statistics.list.channels.month }}</el-tag>
						<el-tag size="small"
						        type="primary">{{ statistics.list.channels.total }}</el-tag>
					</div>
				</div>
			</div>

			<div class="banned-user-wrapper">
				<div class="banned-user">
					<div class="v-bold left">
						comments
					</div>

					<div class="actions">
						<el-tag size="small"
						        type="success">{{ statistics.list.comments.today }}</el-tag>
						<el-tag size="small"
						        type="info">{{ statistics.list.comments.week }}</el-tag>
						<el-tag size="small"
						        type="warning">{{ statistics.list.comments.month }}</el-tag>
						<el-tag size="small"
						        type="primary">{{ statistics.list.comments.total }}</el-tag>
					</div>
				</div>
			</div>

			<div class="banned-user-wrapper">
				<div class="banned-user">
					<div class="v-bold left">
						submissions
					</div>

					<div class="actions">
						<el-tag size="small"
						        type="success">{{ statistics.list.submissions.today }}</el-tag>
						<el-tag size="small"
						        type="info">{{ statistics.list.submissions.week }}</el-tag>
						<el-tag size="small"
						        type="warning">{{ statistics.list.submissions.month }}</el-tag>
						<el-tag size="small"
						        type="primary">{{ statistics.list.submissions.total }}</el-tag>
					</div>
				</div>
			</div>
		
			<div class="banned-user-wrapper">
				<div class="banned-user">
					<div class="v-bold left">
						messages
					</div>

					<div class="actions">
						<el-tag size="small"
						        type="success">{{ statistics.list.messages.today }}</el-tag>
						<el-tag size="small"
						        type="info">{{ statistics.list.messages.week }}</el-tag>
						<el-tag size="small"
						        type="warning">{{ statistics.list.messages.month }}</el-tag>
						<el-tag size="small"
						        type="primary">{{ statistics.list.messages.total }}</el-tag>
					</div>
				</div>
			</div>
			
			<div class="banned-user-wrapper">
				<div class="banned-user">
					<div class="v-bold left">
						reports
					</div>

					<div class="actions">
						<el-tag size="small"
						        type="success">{{ statistics.list.reports.today }}</el-tag>
						<el-tag size="small"
						        type="info">{{ statistics.list.reports.week }}</el-tag>
						<el-tag size="small"
						        type="warning">{{ statistics.list.reports.month }}</el-tag>
						<el-tag size="small"
						        type="primary">{{ statistics.list.reports.total }}</el-tag>
					</div>
				</div>
			</div>

		</div>
	</div>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    created() {
        this.getStatistics();
    },

    data() {
        return {
            statistics: {
                list: [],
                loading: false
            }
        };
    },

    methods: {
        getStatistics() {
            this.statistics.list = [];
            this.statistics.loading = true;

            axios
                .get('/admin/statistics')
                .then(response => {
                    this.statistics.loading = false;

                    this.statistics.list = response.data.data;
                })
                .catch(error => {
                    this.statistics.loading = false;
                });
        }
    }
};
</script>
