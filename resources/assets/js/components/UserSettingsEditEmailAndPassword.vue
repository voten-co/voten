<template>
	<section>
		<h3 class="dotted-title">
			<span>
				Email Address
			</span>
		</h3>

		<el-form label-position="top"
		         label-width="10px">
			<el-form-item label="Email Address">
				<el-input placeholder="Email Address..."
				          v-model="emailForm.email"
				          type="email"></el-input>
				<el-alert v-for="e in emailForm.errors.email"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<div class="flex-space warning warning--info"
			     v-if="showVerificationWarning">
				<p v-if="emailForm.verificationEmailResent">
					Verification email sent. Please check your email inbox.
				</p>

				<p v-if="!emailForm.verificationEmailResent">
					{{ emailForm.email }} is not verified yet.
				</p>

				<el-button round type="primary"
				           size="small"
				           plain
				           @click="resendVerificationEmail"
				           v-if="!emailForm.verificationEmailResent">
					Resend Verification Email
				</el-button>
			</div>

			<el-form-item v-if="changedEmail && !emailForm.showConfirmPassword">
				<el-button round type="success"
						@click="emailForm.showConfirmPassword = true"
						:loading="emailForm.loading"
						size="medium">
					Save
				</el-button>
			</el-form-item>

			<div v-if="emailForm.showConfirmPassword">
				<el-form-item label="To confirm this action please enter your password">
					<el-input placeholder="Password..."
					          v-model="emailForm.password"
					          type="password"></el-input>

					<el-alert v-for="e in emailForm.errors.password"
					          :title="e"
					          type="error"
					          :key="e"></el-alert>
				</el-form-item>

				<el-button round type="success"
				           @click="updateEmail"
				           :disabled="!emailForm.password"
				           :loading="emailForm.loading"
				           size="medium">
					Confirm
				</el-button>
				<el-button type="text"
				           @click="emailForm.showConfirmPassword = false"
				           size="medium">
					Cancel
				</el-button>
			</div>
		</el-form>

		<h3 class="dotted-title">
			<span>
				Change Password
			</span>
		</h3>

		<el-form label-position="top"
		         label-width="10px">
			<el-form-item label="New Password">
				<el-input placeholder="New Password..."
				          v-model="passwordForm.new_password"
				          type="password"></el-input>

				<el-alert v-for="e in passwordForm.errors.new_password"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Confirm Password">
				<el-input placeholder="Confirm Password..."
				          v-model="passwordForm.new_password_confirmation"
				          type="password"></el-input>

				<el-alert v-for="e in passwordForm.errors.new_password_confirmation"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Old Password">
				<el-input placeholder="Enter current password to confirm..."
				          v-model="passwordForm.password"
				          type="password"></el-input>

				<el-alert v-for="e in passwordForm.errors.password"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item v-if="changedPassword">
				<el-button round type="success"
				           @click="updatePassword"
				           :loading="passwordForm.loading"
				           size="medium">
					Save
				</el-button>
			</el-form-item>
		</el-form>
	</section>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    data() {
        return {
            emailForm: {
                errors: [],
                email: auth.email,
                loading: false,
                showConfirmPassword: false,
                verificationEmailResent: false
            },

            passwordForm: {
                errors: [],
                loading: false,
                password: '',
                new_password: '',
                new_password_confirmation: ''
            }
        };
    },

    computed: {
        showVerificationWarning() {
            return (
                !auth.verified_email &&
                auth.email &&
                !this.emailForm.showConfirmPassword
            );
        },

        changedEmail() {
            return auth.email != this.emailForm.email;
        },

        changedPassword() {
            return (
                this.passwordForm.new_password ==
                    this.passwordForm.new_password_confirmation &&
                this.passwordForm.new_password &&
                this.passwordForm.password
            );
        }
    },

    methods: {
        /**
         * saves email address into the database
         *
         * @return void
         */
        updateEmail() {
            this.emailForm.loading = true;

            axios
                .patch('/users/email', {
                    password: this.emailForm.password,
                    email: this.emailForm.email
                })
                .then(() => {
                    this.emailForm.errors = [];
                    this.emailForm.loading = false;
                    this.emailForm.showConfirmPassword = false;
                    auth.email = this.emailForm.email;
                    auth.verified_email = false;
                })
                .catch((error) => {
                    this.emailForm.errors = error.response.data.errors;
                    this.emailForm.loading = false;
                });
        },

        resendVerificationEmail() {
            this.emailForm.verificationEmailResent = true;

            axios.post('/email/verify/resend').catch(() => {
                this.emailForm.verificationEmailResent = false;
            });
        },

        /**
         * updates users' password. old-password is required
         *
         * @return void
         */
        updatePassword() {
            this.passwordForm.loading = true;

            axios
                .patch('/users/password', {
                    password: this.passwordForm.password,
                    new_password: this.passwordForm.new_password,
                    new_password_confirmation: this.passwordForm
                        .new_password_confirmation
                })
                .then(() => {
                    this.passwordForm.password = '';
                    this.passwordForm.new_password = '';
                    this.passwordForm.new_password_confirmation = '';
                    this.passwordForm.loading = false;

                    this.passwordForm.errors = [];

                    this.$message({
                        type: 'success',
                        message: 'Password updated successfully.'
                    });
                })
                .catch((error) => {
                    this.passwordForm.errors = error.response.data.errors;
                    this.passwordForm.loading = false;
                });
        }
    }
};
</script>
