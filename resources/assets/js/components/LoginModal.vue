<template>
    <div class="vo-modal-small">
		<div class="wrapper" v-on-clickaway="close">
			<header class="user-select">
				<h3>
					Login/Register
				</h3>

				<div class="close" @click="close">
					<i class="v-icon v-cancel-small"></i>
				</div>
			</header>

			<div class="middle">
				<div class="flex1 margin-bottom-1">
					<div class="tabs is-fullwidth">
						<ul>
							<li :class="{'is-active' : type == 'register'}" @click="switchType('register')"><a>Sign up</a></li>
							<li :class="{'is-active' : type == 'login'}" @click="switchType('login')"><a>Login</a></li>
						</ul>
					</div>

					<!-- login form  -->
					<div class="form-login" v-show="type == 'login'"  @keyup.enter="login">
						<a href="/login/google" class="v-button v-button--red v-button--block">
							<i class="v-icon v-google"></i>
							Connect With Google
						</a>

						<div class="align-center margin-bottom-1">
							Or
						</div>

						<div class="v-status v-status--error" v-if="errors.username">
							{{ errors.username[0] }}
						</div>

						<div class="v-status v-status--success" v-if="successfulLogin">
							Welcome back {{ '@' + loginUsername }}
						</div>

						<div class="form-group">
							<input type="text" class="form-control" id="username" v-model="loginUsername" name="username" placeholder="Username..." required>
						</div>

						<div class="form-group">
							<input id="password" type="password" class="form-control" name="password" v-model="loginPassword" placeholder="Password" required>

							<small class="text-muted go-red" v-for="e in errors.password">{{ e }}</small>
						</div>

						<div class="flex-right">
							<toggle-button v-model="remember" :labels="{checked: 'Remember Me', unchecked: 'Remember Me'}"
										   :width="120"
										   :height="25"
										   :color="'#edb431'"
							/>
						</div>
					</div>

					<!-- register form -->
					<div class="form-register" v-show="type == 'register'" @keyup.enter="register" >
						<a href="/login/google" class="v-button v-button--red v-button--block">
							<i class="v-icon v-google"></i>
							Connect With Google
						</a>

						<div class="align-center margin-bottom-1">
							Or
						</div>

						<div class="form-group">
							<input type="text" class="form-control" id="username" v-model="registerUsername" name="username" placeholder="Username..." required>

							<small class="text-muted go-red" v-for="e in errors.username">{{ e }}</small>
						</div>

						<div class="form-group">
							<input type="text" class="form-control" id="email" v-model="registerEmail" name="email" placeholder="Email Address (optional)" required>

							<small class="text-muted go-red" v-for="e in errors.email">{{ e }}</small>
						</div>

						<div class="form-group">
							<input id="password" type="password" class="form-control" name="password" v-model="registerPassword" placeholder="Password" required>

							<small class="text-muted go-red" v-for="e in errors.password">{{ e }}</small>
						</div>

						<div class="form-group">
							<input id="password" type="password" class="form-control" name="confirm_password" v-model="registerConfirmPassword" placeholder="Confirm Password" required>
						</div>
					</div>
				</div>
			</div>

			<footer v-if="type == 'login'">
				<button class="v-button v-button--green" @click="login" :disabled="!goodToLogin">Login</button>
				<a class="v-button v-button--link" href="/password/reset">Forgot my password</a>
			</footer>

			<footer v-if="type == 'register'">
				<span>By clicking "Sign up", you agree to our <a href="/tos" target="_blank" class="go-primary">terms</a>.</span>
				<button class="v-button v-button--green" @click="register" :disabled="!goodToRegister">Sign up</button>
			</footer>
		</div>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway';
import Helpers from '../mixins/Helpers';

export default {
    mixins: [ clickaway, Helpers ],

    data () {
        return {
        	type: 'login',
        	errors: [],
        	loading: false,
        	loginUsername: '',
        	loginPassword: '',
        	remember: true,
        	successfulLogin: false,

        	registerUsername: '',
        	registerEmail: '',
        	registerPassword: '',
        	registerConfirmPassword: '',
        }
    },

	computed: {
		goodToLogin() {
			return this.loginUsername.length > 2 && this.loginPassword.length > 5 && !this.loading;
		},

		goodToRegister() {
			return this.registerUsername.length > 2 && this.registerPassword.length > 5 && this.registerConfirmPassword.length > 5 && !this.loading;
		}
	},

    methods: {
    	/**
    	 * Fakes the login form
    	 *
    	 * @return void
    	 */
    	login() {
    	    if(!this.goodToLogin) return;

    	    this.loading = true;

    	    axios.post('/login', {
    	    	username: this.loginUsername,
    	    	password: this.loginPassword,
    	    	remember: this.remember
    	    }).then((response) => {
    	    	this.loading = false;
    	    	this.errors = [];
    	    	this.successfulLogin = true;
    	    	location.reload();
    	    }).catch((error) => {
    	    	this.loading = false;
    	    	this.errors = error.response.data.errors;
    	    });
    	},

    	/**
    	 * Fakes the register form
    	 *
    	 * @return void
    	 */
    	register() {
            if(!this.goodToRegister) return;

            this.loading = true;

    	    axios.post('/register', {
    	    	username: this.registerUsername,
    	    	email: this.registerEmail,
    	    	password: this.registerPassword,
    	    	password_confirmation: this.registerConfirmPassword,
    	    	remember: this.remember
    	    }).then((response) => {
    	    	this.loading = false;
    	    	this.errors = [];
    	    	window.location = "/discover-channels?newbie=1&sidebar=0";
    	    }).catch((error) => {
    	    	this.loading = false;
    	    	this.errors = error.response.data.errors;
    	    });
    	},

    	/**
    	 * switches the type
    	 *
    	 * @return void
    	 */
    	switchType(type) {
    		this.errors = [];

    	    this.type = type;
    	},

    	/**
    	 * Fires the 'close' event which causes all the modals to be closed.
    	 *
    	 * @return void
    	 */
    	close() {
    		this.$eventHub.$emit('close')
    	},
    },
}

</script>
