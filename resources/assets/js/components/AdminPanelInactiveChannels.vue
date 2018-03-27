<template>
	<div id="submissions"
	     class="home-submissions">
		<h2 class="v-bold">
			Inactive Channels({{ items.length }}):
		</h2>

		<el-table :data="items"
		          stripe
		          size="medium">
			<el-table-column label="Name">
				<template slot-scope="scope">
					<img :src="scope.row.avatar"
					     :alt="scope.row.avatar"
					     height="30"
					     class="circle margin-right-half">

					<router-link :to="'/c/' + scope.row.name">{{ str_limit(scope.row.name, 15) }}</router-link>
				</template>
			</el-table-column>

			<el-table-column label="Description">
				<template slot-scope="scope">
					{{ scope.row.description }}
				</template>
			</el-table-column>

			<el-table-column label="Created At">
				<template slot-scope="scope">
					<el-tag type="info"
					        size="mini">
						{{ date(scope.row.created_at) }}
					</el-tag>
				</template>
			</el-table-column>

			<el-table-column label="subscribers - comments - submissions"
			                 align="center">
				<template slot-scope="scope">
					<el-tag type="info"
					        size="mini">
						{{ scope.row.subscribers_count + ' - ' + scope.row.comments_count + ' - ' + scope.row.submissions_count }}
					</el-tag>
				</template>
			</el-table-column>

			<el-table-column fixed="right"
			                 label="Operations"
			                 width="180">
				<template slot-scope="scope">
					<el-button type="text"
					           @click="destroy(scope.row.id)"
					           size="small">Delete</el-button>

					<!-- <el-button type="text"
					           size="small">Moderators</el-button> -->
				</template>
			</el-table-column>
		</el-table>
	</div>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    data() {
        return {
            items: []
        };
    },

    created() {
        this.getItems();
    },

    methods: {
        getItems() {
            this.$Progress.start();

            axios
                .get('/admin/channels/inactive')
                .then(response => {
                    this.items = response.data.data;
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },

        date(date) {
            return moment(date)
                .utc(moment().format('Z'))
                .fromNow();
        },

        destroy(channel_id) {
            this.$prompt('Please confirm this action by entering your password', 'Confirm Required', {
                confirmButtonText: 'Delete Channel',
                cancelButtonText: 'Cancel', 
                inputType: 'password'
            })
                .then(value => {
                    axios
                        .post(`/channels/${channel_id}`, {
                            password: value.value
                        })
                        .then(response => {
                            this.items = this.items.filter(channel => channel.id !== channel_id);
                        })
                        .catch(error => {
                            app.$message({
                                'type': 'error', 
                                message: error.response.data.errors.password[0]
                            });
                        });
                });
        }
    }
};
</script>
