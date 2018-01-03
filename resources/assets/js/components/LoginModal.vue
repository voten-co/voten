<template>
	<el-dialog
		title="Login"
		:visible="visible"
		:width="isMobile ? '99%' : '600px'"
		@close="close"
		append-to-body
		class="user-select"
	>
		<div class="flex-center">
			<el-button href="/login/google" plain>
				<i class="v-icon v-google"></i>
				Connect with your 
				<span class="go-primary">G</span><span class="go-red">o</span><span class="go-yellow">o</span><span class="go-primary">g</span><span class="go-green">l</span><span class="go-red">e</span> 
				account 
			</el-button>
		</div>

		<div class="align-center margin-top-bottom-1">
			<strong>Or</strong>, Sign up with/<strong>without</strong> your email address:
		</div>

		<!-- <div class="tabs is-fullwidth">
			<ul>
				<li :class="{'is-active' : type == 'register'}" @click="switchType('register')"><a>Sign up</a></li>
				<li :class="{'is-active' : type == 'login'}" @click="switchType('login')"><a>Login</a></li>
			</ul>
		</div> -->

		<!-- login form  -->
		<el-form label-position="top" label-width="10px">
			<el-form-item>
				<el-alert
					v-if="errors.username"
					:title="errors.username[0]"
					type="error">
				</el-alert>
			</el-form-item>

			<el-form-item>
				<el-input
                    placeholder="Username or email address..."
                    name="username"
                    v-model="loginUsername"
					required
					size="medium"
                    autocorrect="off" autocapitalize="off" spellcheck="false"
                ></el-input>

				<el-alert v-for="e in errors.username" :title="e" type="error" :key="e"></el-alert>				
			</el-form-item>
			
			<el-form-item>
				<el-input
                    placeholder="Password..."
                    name="password"
                    v-model="loginPassword"
					required
					size="medium"
					:type="showPassword ? 'text' : 'password'"
                    autocorrect="off" autocapitalize="off" spellcheck="false"
                ></el-input>

				<el-alert v-for="e in errors.password" :title="e" type="error" :key="e"></el-alert>
			</el-form-item>

			<div class="flex-space">
				<div>
					<el-checkbox v-model="remember" size="mini">
						Remember me
					</el-checkbox>

					<el-checkbox v-model="showPassword" size="mini">
						Show password 
					</el-checkbox>
				</div>
			</div>
		</el-form>



		<!-- <div class="form-register">
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

		<footer>
			<button class="v-button v-button--green" @click="login" :disabled="!goodToLogin">Login</button>
		</footer>

		<footer>
			<span>By clicking "Sign up", you agree to our <router-link to="/tos" class="go-primary">terms</router-link>.</span>
			<button class="v-button v-button--green" @click="register" :disabled="!goodToRegister">Sign up</button>
		</footer> -->

		<span slot="footer" class="dialog-footer">
			<el-button type="text" href="/password/reset" size="small">
				Forgot my password
			</el-button>
			
			<el-button type="text" href="/password/reset" size="small">
				Sign up
			</el-button>

			<el-button
				type="success"
				@click="login"
				:loading="loading"
				size="small"
			>
				Login
			</el-button>
		</span>
	</el-dialog>
</template>

<script>
// @keyup.enter="register" 
import { mixin as clickaway } from 'vue-clickaway';
import Helpers from '../mixins/Helpers';

export default {
	mixins: [ clickaway, Helpers ],
	
	props: ['visible'],

    data () {
        return {
        	type: 'login',
        	errors: [],
        	loading: false,
        	loginUsername: '',
        	loginPassword: '',
        	successfulLogin: false,

        	registerUsername: '',
        	registerEmail: '',
        	registerPassword: '',
			registerConfirmPassword: '',
			
		   	remember: true,	
			showPassword: false 
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

    	
    	close() {
			this.$emit('update:visible', false);
		},
    },
}

</script>
