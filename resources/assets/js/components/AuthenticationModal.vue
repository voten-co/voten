<template>
	<el-dialog :title="isLogin ? 'Login' : 'Sign up'"
	           :visible="visible"
	           :width="isMobile ? '99%' : '600px'"
	           @close="close"
	           append-to-body
	           class="user-select">
		<div class="flex-center">
			<google-login-button></google-login-button>
		</div>

		<div class="align-center margin-top-bottom-1">
			<strong>Or</strong>
		</div>

		<!-- login form  -->
		<el-form label-position="top"
		         v-if="isLogin"
		         label-width="10px">
			<el-form-item label="Username or email address:">
				<el-input placeholder="Username or email address..."
				          name="username"
				          v-model="loginForm.username"
				          required
				          size="medium"
				          autocorrect="off"
				          autocapitalize="off"
				          spellcheck="false"></el-input>

				<el-alert v-for="e in loginForm.errors.username"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Password:">
				<el-input placeholder="Password..."
				          name="password"
				          v-model="loginForm.password"
				          required
				          size="medium"
				          :type="showPassword ? 'text' : 'password'"
				          autocorrect="off"
				          autocapitalize="off"
				          spellcheck="false"></el-input>

				<el-alert v-for="e in loginForm.errors.password"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<div class="flex-space">
				<div>
					<el-checkbox v-model="loginForm.remember"
					             v-if="isLogin"
					             size="mini">
						Remember me
					</el-checkbox>

					<el-checkbox v-model="showPassword"
					             size="mini">
						Show password
					</el-checkbox>
				</div>
			</div>
		</el-form>

		<el-form label-position="top"
		         v-if="isRegister"
		         label-width="10px">
			<el-form-item label="Username:">
				<el-input placeholder="Username..."
				          name="username"
				          v-model="registerForm.username"
				          required
				          size="medium"
				          autocorrect="off"
				          autocapitalize="off"
				          spellcheck="false"></el-input>

				<el-alert v-for="e in registerForm.errors.username"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="(optional) Email Address:">
				<el-input placeholder="(optional) Email address..."
				          name="email"
				          v-model="registerForm.email"
				          size="medium"
				          autocorrect="off"
				          autocapitalize="off"
				          spellcheck="false"></el-input>

				<el-alert v-for="e in registerForm.errors.email"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Password:">
				<el-input placeholder="Password..."
				          name="password"
				          v-model="registerForm.password"
				          required
				          size="medium"
				          :type="showPassword ? 'text' : 'password'"
				          autocorrect="off"
				          autocapitalize="off"
				          spellcheck="false"></el-input>

				<el-alert v-for="e in registerForm.errors.password"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Confirm Password:">
				<el-input placeholder="Confirm Password..."
				          name="password"
				          v-model="registerForm.password_confirmation"
				          required
				          size="medium"
				          :type="showPassword ? 'text' : 'password'"
				          autocorrect="off"
				          autocapitalize="off"
				          spellcheck="false"></el-input>
			</el-form-item>

			<el-form-item class="margin-top-1">
				<el-alert v-if="registerForm.errors['g-recaptcha-response']"
				          title="You didn't pass the reCAPTCHA check"
						  class="margin-bottom-1"
				          type="error"></el-alert>

				<vue-recaptcha :sitekey="recaptchaKey"
				               @verify="reCaptchaVerified"
							   ref="recaptcha"
				               @expired="reCaptchaExpired"></vue-recaptcha>
			</el-form-item>

			<el-form-item>
				<span>
					By clicking "Sign up", you agree to our
					<router-link to="/tos"
					             class="go-primary">terms of service</router-link>
					and
					<router-link to="/privacy-policy">privacy policy</router-link>.
				</span>
			</el-form-item>
		</el-form>

		<span slot="footer"
		      class="dialog-footer">
			<a type="text"
			   href="/password/reset"
			   class="el-button el-button--text el-button--small"
			   v-if="isLogin"
			   size="small">
				Forgot my password
			</a>

			<el-button type="text"
			           href="/password/reset"
			           v-if="isLogin"
			           @click="switchType('register')"
			           size="small">
				Sign up
			</el-button>
			<el-button type="text"
			           v-if="isRegister"
			           href="/password/reset"
			           @click="switchType('login')"
			           size="small">
				Login
			</el-button>

			<el-button round type="success"
			           @click="login"
			           :loading="loading"
			           size="small"
			           v-if="isLogin">
				Login
			</el-button>
			<el-button round type="success"
			           @click="register"
			           :loading="loading"
			           v-if="isRegister"
			           size="small">
				Sign up
			</el-button>
		</span>
	</el-dialog>
</template>

<script>
// @keyup.enter="register"
import Helpers from '../mixins/Helpers';
import VueRecaptcha from 'vue-recaptcha';
import GoogleLoginButton from '../components/GoogleLoginButton';

export default {
    mixins: [Helpers],

    components: { GoogleLoginButton, VueRecaptcha },

    props: ['visible'],

    data() {
        return {
            type: 'register',
            loading: false,
            showPassword: false,
            recaptchaKey: window.Laravel.recaptchaKey,

            registerForm: {
                username: '',
                password: '',
                password_confirmation: '',
                email: '',
                errors: [],
                reCAPTCHA: ''
            },

            loginForm: {
                username: '',
                password: '',
                errors: [],
                remember: true
            }
        };
    },

    beforeDestroy() {
        if (window.location.hash == '#authintication') {
            history.go(-1);
        }
    },

    created() {
        window.location.hash = 'authintication';
    },

    computed: {
        isLogin() {
            return this.type == 'login';
        },

        isRegister() {
            return this.type == 'register';
        }
    },

    methods: {
        reCaptchaVerified(response) {
            this.registerForm.reCAPTCHA = response;
        },

        reCaptchaExpired() {
            this.registerForm.reCAPTCHA = '';
        },

        login() {
            this.loading = true;

            axios
                .post(`${Laravel.url}/login`, {
                    username: this.loginForm.username,
                    password: this.loginForm.password,
                    remember: this.loginForm.remember
                })
                .then((response) => {
                    this.loading = false;
                    this.loginForm.errors = [];
                    Vue.clearLS();
                    location.reload();
                })
                .catch((error) => {
                    this.loading = false;
                    this.loginForm.errors = error.response.data.errors;
                });
        },

        register() {
            this.loading = true;

            axios
                .post(`${Laravel.url}/register`, {
                    username: this.registerForm.username,
                    email: this.registerForm.email,
                    password: this.registerForm.password,
                    password_confirmation: this.registerForm
                        .password_confirmation,
                    'g-recaptcha-response': this.registerForm.reCAPTCHA
                })
                .then((response) => {
                    this.loading = false;
                    this.registerForm.errors = [];
                    window.location = '/discover-channels?newbie=1&sidebar=0';
                })
                .catch((error) => {
                    this.loading = false;
                    // this.$refs.recaptcha.reset();
                    this.registerForm.errors = error.response.data.errors;
                });
        },

        switchType(type) {
            this.loginForm.errors = [];
            this.registerForm.errors = [];
            this.type = type;
        },

        close() {
            this.$emit('update:visible', false);
        }
    }
};
</script>
