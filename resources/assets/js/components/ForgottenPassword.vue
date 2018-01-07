<template>
	<section class="home-wrapper user-select">
		<nav class="nav has-shadow user-select">
			<div class="container">
				<h1 class="title">
					Request a password reset
				</h1>

				<div class="flex-center">
					<router-link to="/register"
					             class="margin-right-1">
						<el-button size="small"
						           type="text">Sign up</el-button>
					</router-link>

					<router-link to="/login"
					             class="margin-right-1">
						<el-button size="small"
						           type="text">Login</el-button>
					</router-link>
				</div>
			</div>
		</nav>

		<div id="page"
		     class="home-submissions"
		     @keyup.enter="submit">
			<p>
				Enter your registered email address below and we'll email you a link to reset your password.
			</p>

			<el-form label-position="top"
			         v-if="!emailSent"
			         label-width="10px"
			         @submit.native.prevent>
				<el-form-item label="Emaill Address">
					<el-input v-model="form.email"
					          placeholder="Email Address..."></el-input>

					<el-alert v-for="e in form.errors.email"
					          :title="e"
					          type="error"
					          show-icon
					          :closable="false"
					          :key="e"></el-alert>
				</el-form-item>

				<el-button type="success"
				           :loading="form.loading"
				           size="medium"
				           @click="submit"
				           :disabled="!form.email.trim()">
					Send Password Reset Link
				</el-button>
			</el-form>

			<div v-if="emailSent">
				<el-alert type="success"
				          show-icon
				          title="Password reset link has been sent. Please check your email inbox for further instructions."></el-alert>
			</div>
		</div>
	</section>
</template>

<script>
export default {
    data() {
        return {
            form: {
                email: "",
                sent: false,
                loading: false,
                errors: []
            }
        };
    },

    computed: {
        emailSent() {
            return this.form.sent;
        }
    },

    methods: {
        submit() {
            this.form.loading = true;
            this.form.errors = [];

            axios
                .post("/password/email", {
                    email: this.form.email
                })
                .then(response => {
                    this.form.sent = true;
                    this.form.loading = false;
                })
                .catch(error => {
                    this.form.errors = error.response.data.errors;
                    this.form.loading = false;
                });
        }
    }
};
</script>
