<template>
	<section>
		<h3 class="dotted-title">
			<span>
				Ban Users
			</span>
		</h3>

		<p>
			Please use this tool as the last solution for dealing with spammers.
		</p>

		<el-form label-position="top"
		         label-width="10px">
			<el-form-item label="Username">
				<el-select v-model="user_id"
				           filterable
				           remote
				           placeholder="Search by username..."
				           :remote-method="search"
				           loading-text="Loading..."
				           :loading="loading">
					<el-option v-for="item in users"
					           :key="item.id"
					           :label="item.username"
					           :value="item.id">
					</el-option>
				</el-select>
			</el-form-item>

			<el-form-item label="Reason(optional)">
				<el-input type="textarea"
				          placeholder="What did the user wrong? (markdown syntax is supported)"
				          v-model="description"
				          :rows="4">
				</el-input>
			</el-form-item>

			<el-form-item label="For how many days (leave 0 for permanent)">
				<el-input-number v-model="duration"
				                 :step="5"
				                 :min="0"></el-input-number>
			</el-form-item>

			<el-form-item>
				<el-button round
				           size="medium"
				           type="danger"
				           v-if="user_id"
				           @click="store"
				           :loading="sending">Ban</el-button>
			</el-form-item>
		</el-form>

		<h3 class="dotted-title"
		    v-if="bannedUsers.length">
			<span>
				All Banned Users
			</span>
		</h3>

		<banned-user v-for="banned in bannedUsers"
		             :list="banned"
		             :key="banned.id"
		             @unban="destroy"></banned-user>
	</section>
</template>

<script>
import BannedUser from '../components/BannedUser.vue';

export default {
    components: {
        BannedUser
    },

    data() {
        return {
            loading: false,
            sending: false,
            user_id: null,
            description: '',
            duration: 1,
            users: [],
            bannedUsers: [],
            Store
        };
    },

    created() {
        this.index();
    },

    methods: {
        search: _.debounce(function(query) {
            if (!query.trim()) return;
            this.loading = true;

            axios
                .get('/search', {
                    params: {
                        type: 'Users',
                        keyword: query
                    }
                })
                .then(response => {
                    this.users = response.data.data; 
                    this.loading = false;
                })
                .catch(error => {
                    this.loading = false;
                });
        }, 600),

        store() {
            this.sending = true;

            axios
                .post(`/channels/${Store.page.channel.temp.id}/banned-users`, {
                    user_id: this.user_id,
                    description: this.description,
                    duration: this.duration
                })
                .then(response => {
                    // add the banned user to the this.bannedUsers array
                    this.user_id = null;
                    this.description = '';
                    this.duration = 0;

                    this.bannedUsers.unshift(response.data.data);

                    this.sending = false;
                })
                .catch(() => {
                    this.sending = false;
                });
        },

        /**
         * Unbans the user (destroy the ban record).
         *
         * @return void
         */
        destroy(user_id) {
            axios
                .delete(`/channels/${Store.page.channel.temp.id}/banned-users/${user_id}`)
                .then(() => {
                    this.bannedUsers = this.bannedUsers.filter(item => item.user_id != user_id);
                });
        },

        /**
         * Fetches the list of already banned users on this channel.
         *
         * @return void
         */
        index() {
            app.$Progress.finish();
            app.$Progress.start();

            axios
                .get(`/channels/${Store.page.channel.temp.id}/banned-users`)
                .then(response => {
                    this.bannedUsers = response.data.data;
                    app.$Progress.finish();
                })
                .catch(error => {
                    app.$Progress.fail();
                });
        }
    }
};
</script>
