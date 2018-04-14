<template>
	<el-dialog :title="'Ban @' + username"
	           :visible="visible"
	           :width="isMobile ? '99%' : '600px'"
	           @close="close"
	           append-to-body
	           class="user-select">
		<section class="user-select"
		         id="feedback-modal">
			<el-form label-position="top"
			         label-width="10px">
				<el-form-item label="For how many days (leave 0 for permanent)">
					<el-input-number v-model="duration"
					                 :step="5"
					                 :min="0"></el-input-number>
				</el-form-item>

                <el-form-item>
                    <el-checkbox v-model="deletePosts">Delete everything posted by user</el-checkbox>
                </el-form-item>

				<el-form-item label="Desciption">
					<el-input type="textarea"
					          v-model="description"
					          placeholder="Desciption..."
					          name="description"
					          :maxlength="2000"
					          ref="description"
					          :autosize="{ minRows: 4, maxRows: 10}"></el-input>
				</el-form-item>
			</el-form>
		</section>

		<!-- submit -->
		<span slot="footer"
		      class="dialog-footer">
			<el-button type="text"
			           @click="close"
			           size="medium"
			           class="margin-right-1">
				Cancel
			</el-button>

			<el-button round
			           type="danger"
			           @click="submit"
			           :loading="loading"
			           size="medium">
				Ban
			</el-button>
		</span>
	</el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    props: ['visible'],

    data() {
        return {
            loading: false,
            description: '',
            duration: 7, 
            deletePosts: true, 
        };
    },

    computed: {
        user_id() {
            return Store.modals.banUser.user.id;
        }, 

        username() {
            return Store.modals.banUser.user.username;
        }
    },

    beforeDestroy() {
        if (window.location.hash == '#banUser') {
            history.go(-1);
        }
    },

    created() {
        window.location.hash = 'banUser';
    },

    methods: {
        close() {
            this.$emit('update:visible', false);
        },

        submit() {
            this.loading = true;

            axios
                .post(`/admin/banned-users`, {
                    user_id: this.user_id,
                    duration: this.duration,
                    description: this.description, 
                    delete_posts: this.deletePosts ? 1 : 0, 
                })
                .then(response => {
                    this.loading = false;
                    this.close(); 
                    this.$router.push('/');
                })
                .catch(error => {
                    this.loading = false;
                });
        }
    }
};
</script>
