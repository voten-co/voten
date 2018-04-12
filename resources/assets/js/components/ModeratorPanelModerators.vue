<template>
	<section>
		<h3 class="dotted-title">
			<span>
				Add New Moderator
			</span>
		</h3>

		<el-form label-position="top"
		         label-width="10px">
			<el-form-item label="Username">
				<el-select v-model="addModeratorForm.user_id"
				           value-key="username"
				           filterable
				           remote
				           placeholder="Search by username..."
				           :remote-method="search"
				           loading-text="Loading..."
				           :loading="addModeratorForm.loading">
					<el-option v-for="item in addModeratorForm.users"
					           :key="item.id"
					           :label="item.username"
					           :value="item.id">
					</el-option>
				</el-select>
			</el-form-item>

			<el-form-item label="Role">
				<el-radio-group v-model="addModeratorForm.role">
					<el-radio label="administrator"
					          border></el-radio>
					<el-radio label="moderator"
					          border></el-radio>
				</el-radio-group>
			</el-form-item>

			<el-form-item>
				<el-button round
				           type="success"
				           size="medium"
				           v-if="addModeratorForm.role && addModeratorForm.user_id"
				           @click="addModerator"
				           :loading="addModeratorForm.sending">
					Add
				</el-button>
			</el-form-item>
		</el-form>

		<h3 class="dotted-title">
			<span>
				All Moderators
			</span>
		</h3>

		<moderator v-for="(item, index) in mods"
		           :list="item.user"
		           :role="item.role"
		           :key="item.user.id"
		           @delete-moderator="mods.splice(index, 1)"></moderator>
	</section>
</template>

<script>
import Moderator from '../components/Moderator.vue';

export default {
    components: { Moderator },

    data() {
        return {
            addModeratorForm: {
                user_id: null,
                users: [],
                role: 'moderator',
                sending: false,
                loading: false,
            }, 
            
            mods: []
        };
    },

    created() {
        this.getMods();
    },

    methods: {
        getMods() {
            app.$Progress.finish();
            app.$Progress.start();
            this.addModeratorForm.users = [];

            axios
                .get(`/channels/${Store.page.channel.temp.id}/moderators`)
                .then(response => {
                    this.mods = response.data.data;
                    app.$Progress.finish();
                })
                .catch(error => {
                    app.$Progress.fail();
                });
        },

        search: _.debounce(function(query) {
            if (!query.trim()) return;
            this.addModeratorForm.loading = true;

            axios
                .get('/search', {
                    params: {
                        type: 'Users',
                        keyword: query
                    }
                })
                .then(response => {
                    this.addModeratorForm.users = response.data.data;
                    this.addModeratorForm.loading = false;
                })
                .catch(error => {
                    this.addModeratorForm.loading = false;
                });
        }, 600),

        addModerator() {
            this.addModeratorForm.sending = true;

            axios
                .post(`/channels/${Store.page.channel.temp.id}/moderators`, {
                    user_id: this.addModeratorForm.user_id,
                    role: this.addModeratorForm.role
                })
                .then(() => {
                    this.addModeratorForm.user_id = null;
                    this.addModeratorForm.role = 'moderator';

                    this.getMods();
                    this.addModeratorForm.sending = false;
                })
                .catch(() => {
                    this.addModeratorForm.sending = false;
                });
        }
    }
};
</script>
