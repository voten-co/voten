<template>
    <div class="v-modal-small" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box v-modal-small-box--light" v-on-clickaway="close">
            <div class="flex1">
	            <div class="tabs is-fullwidth">
					<ul>
						<li :class="{'is-active' : type == 'register'}" @click="switchType('register')"><a>Sign up</a></li>
						<li :class="{'is-active' : type == 'login'}" @click="switchType('login')"><a>Login</a></li>
					</ul>
				</div>

				<!-- login form  -->
				<div v-show="type == 'login'">
					<div class="form-group">
						<input type="text" class="form-control" id="username" v-model="loginUsername" name="username" placeholder="Username..." required>

						<small class="text-muted go-red" v-for="e in errors.username">{{ e }}</small>
					</div>

					<div class="form-group">
						<input id="password" type="password" class="form-control" name="password" v-model="loginPassword" placeholder="Password" required>

						<small class="text-muted go-red" v-for="e in errors.password">{{ e }}</small>
					</div>

	                <div class="form-group ui form">
		                <div class="inline field">
		                    <div class="ui toggle checkbox">
		                        <input type="checkbox" class="hidden" name="remember" v-model="remember">
		                        <label>Remember Me</label>
		                    </div>
		                </div>
		            </div>

					<div class="flex-space">
						<button class="v-button v-button--green">Login</button>
						<a class="v-button" href="/password/reset">Forgot my password</a>
					</div>
				</div>

				<!-- register form -->
				<div v-show="type == 'register'">
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

						<small class="text-muted go-red" v-for="e in errors.confirm_password">{{ e }}</small>
					</div>

					<div class="flex-space">
						<span class="form-notice">By clicking Sign Up, you agree to our <a href="/tos" class="go-primary">terms</a>.</span>
						<button class="v-button v-button--green">Sign up</button>
					</div>
				</div>
            </div>
        </div>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway';
import Helpers from '../mixins/Helpers';

export default {
	props: ['sidebar'],

    mixins: [ clickaway, Helpers ],

    data () {
        return {
        	type: 'login',
        	errors: [],
        	loginUsername: '',
        	loginPassword: '',
        	remember: true,

        	registerUsername: '',
        	registerEmail: '',
        	registerPassword: '',
        	registerConfirmPassword: '',
        }
    },

	mounted: function () {
		this.$nextTick(function () {
			this.$root.loadCheckBox();
		})
	},

    methods: {
    	/**
    	 * switches the type
    	 *
    	 * @return void
    	 */
    	switchType(type) {
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
