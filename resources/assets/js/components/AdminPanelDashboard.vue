<template>
	<div id="submissions"
	     class="home-submissions">
		<div class="flex-space">
			<h1>
				Latest Activities:
			</h1>

			<div>
				<el-button type="info"
				           round
				           :icon="activities.loading ? 'el-icon-loading' : 'el-icon-refresh'"
				           size="mini"
				           class="margin-left-half"
				           @click="getActivities">
					Refresh
				</el-button>
			</div>
		</div>

		<el-table :data="activities.items"
		          stripe
		          size="mini">
			<el-table-column label="Name">
				<template slot-scope="scope">
					<el-tooltip :content="scope.row.activity_type"
					            placement="top">
						<span v-text="str_limit(scope.row.activity_type, 15)"></span>
					</el-tooltip>
				</template>
			</el-table-column>

			<el-table-column prop="user.username"
			                 label="By">
			</el-table-column>

			<el-table-column label="Country">
				<template slot-scope="scope">
					<el-tooltip :content="scope.row.country_short_name"
					            placement="top">
						<img v-if="scope.row.country_short_name != 'unknown'"
                        :src="scope.row.country_flag"
						     :alt="scope.row.country_short_name"
						     height="20">
					</el-tooltip>
				</template>
			</el-table-column>

			<el-table-column label="OS">
				<template slot-scope="scope">
					<el-tooltip :content="scope.row.os"
					            placement="top">
						<span v-text="str_limit(scope.row.os, 10)"></span>
					</el-tooltip>
				</template>
			</el-table-column>

			<el-table-column prop="browser_name"
			                 label="Browser">
			</el-table-column>

			<el-table-column prop="ip_address"
			                 label="IP">
			</el-table-column>

			<el-table-column label="Date">
				<template slot-scope="scope">
					{{ date(scope.row.created_at) }}
				</template>
			</el-table-column>
		</el-table>
	</div>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    created() {
        this.getActivities();
    },

    data() {
        return {
            activities: {
                items: [],
                loading: false
            }
        };
    },

    methods: {
        getActivities() {
            this.activities.items = [];
            this.activities.loading = true;

            axios
                .get('/admin/activities')
                .then(response => {
                    this.activities.items = response.data.data;
                    this.activities.loading = false;
                })
                .catch(() => {
                    this.activities.loading = false;
                });
        },

        date(date) {
            return moment(date)
                .utc(moment().format('Z'))
                .fromNow();
        }
    }
};
</script>
