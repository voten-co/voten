<style lang="scss">
.echo-info-wrapper {
    display: flex;
    justify-content: space-evenly;

    .echo-info-box {
        width: 200px;
        height: 5em;
        background: #4e4e84;
        color: #fff;
        border-radius: 4px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 2em;
        padding: 1em;
        flex-direction: column;

        .info {
            font-size: 15px;
            opacity: 0.9;
        }
    }
}
</style>


<template>
	<div id="submissions"
	     class="home-submissions">
		<div class="echo-info-wrapper" v-if="!echo.loading && isUsingEcho">
			<div class="echo-info-box">
				<div class="info">
					Online Users:
				</div>

				<div>{{ echo.items.online_connections }}</div>
			</div>

			<div class="echo-info-box">
				<div class="info">
					Echo Uptime:
				</div>

				{{ echo.items.uptime }}
			</div>

			<div class="echo-info-box">
				<div class="info">
					Echo Memory Usage:
				</div>

				{{ echo.items.memory_usage }}
			</div>
		</div>

		<el-alert type="warning" title="You're using Pusher for broadcasting." v-if="!isUsingEcho"></el-alert>

		<div class="flex-space">
			<h2>
				Latest Activities:
			</h2>

			<div>
				<el-button type="info"
				           round
				           :loading="activities.loading"
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

			<el-table-column label="IP">
				<template slot-scope="scope">
					{{ str_limit(scope.row.ip_address, 15) }}
				</template>
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
        this.getEcho();
    },

    data() {
        return {
            activities: {
                items: [],
                loading: false
            },

            echo: {
                items: [],
                loading: false
            }
        };
	},
	
	computed: {
		isUsingEcho() {
			return Laravel.broadcasting.service === 'echo';
		}
	}, 

    methods: {
        getActivities() {
            this.activities.items = [];
            this.activities.loading = true;

            axios
                .get('/admin/activities')
                .then((response) => {
                    this.activities.items = response.data.data;
                    this.activities.loading = false;
                })
                .catch(() => {
                    this.activities.loading = false;
                });
        },

        getEcho() {
			if (! this.isUsingEcho) return; 

            this.echo.loading = true;

            axios
                .get('/admin/echo')
                .then((response) => {
                    console.log(response.data.data);
                    this.echo.items = response.data.data;
                    this.echo.loading = false;
                })
                .catch(() => {
                    this.echo.loading = false;
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
