<template>
	<section>
		<h3 class="dotted-title">
            <span>
                Change Email Address
            </span>
        </h3>

        <div class="form-group">
            <label for="email" class="form-label">Email Address:</label>

            <input type="email" class="form-control" placeholder="Email Address..." v-model="email" id="email">

            <small class="text-muted go-red" v-for="e in errors.email">{{ e }}</small>
        </div>

        <button class="v-button v-button--green" :disabled="sending" v-if="changedEmail && !saveEmail" @click="saveEmail = true">
        	Save
        </button>

        <div v-if="saveEmail">
			<div class="form-group">
	    		<label for="password" class="form-label">To confirm this action please enter your password:</label>

	            <input type="password" class="form-control" placeholder="Password..." v-model="password" id="password">

	            <small class="text-muted go-red" v-if="passwordError">{{ passwordError }}</small>
	        </div>

	        <button class="v-button v-button--green" @click="updateEmail" :disabled="!password">
	        	Confirm
	        </button>
	        <button class="v-button v-button--red" @click="saveEmail = false">
	        	Cancel
	        </button>
		</div>

        <h3 class="dotted-title">
            <span>
                Change Password
            </span>
        </h3>

        <div v-if="passwordSaved" class="v-status v-status--success">
        	Your password has been successfully updated.
        </div>

        <div class="form-group">
            <label for="newpassword" class="form-label">New Password:</label>
            <input type="password" class="form-control" placeholder="New Password..." v-model="newpassword" id="newpassword">
            <small class="text-muted go-red" v-for="e in errors.password">{{ e }}</small>
        </div>
        <div class="form-group">
            <label for="confirmpassword" class="form-label">Confirm Password:</label>
            <input type="password" class="form-control" placeholder="Confirm Password..." v-model="confirmpassword" id="confirmpassword">
        </div>

        <div class="form-group">
            <label for="oldpassword" class="form-label">Old Password:</label>

            <input type="password" class="form-control" placeholder="Enter current password to confirm..." v-model="oldpassword" id="oldpassword">

            <small class="text-muted go-red" v-if="passwordError">{{ passwordError }}</small>
        </div>

        <button class="v-button v-button--green" :disabled="sending" v-if="changedPassword" @click="updatePassword">
        	Save
        </button>
	</section>
</template>

<script>
    export default {
        components: {},

        mixins: [],

        data () {
            return {
                auth,
                errors: [],
                saveEmail: false,
                password: '',
                passwordError: '',
                sending: false,
                passwordSaved: false,
                email: auth.email,
				oldpassword: '',
				newpassword: '',
				confirmpassword: ''
            }
        },

        computed: {
            changedEmail() {
            	return auth.email != this.email;
            },

            changedPassword() {
            	return (this.newpassword == this.confirmpassword) && (this.newpassword) && (this.oldpassword);
            }
        },

        created () {
            document.title = 'Email & Password | Settings';
        },

        mounted () {
            //
        },

        methods: {
            /**
             * saves email address into the database
             *
             * @return void
             */
            updateEmail() {
            	this.sending = true;

                axios.post('/update-email', {
                	password: this.password,
                	email: this.email
                })
                .then((response) => {
                	this.errors = [];
                	this.sending = false;
                	this.saveEmail = false;
                	auth.email = this.email;

                	this.passwordError = '';
                }).catch((error) => {
                	if (error.response.status == 422) {
                		this.errors = [];
                		this.passwordError = error.response.data;
                		this.sending = false;
                		return;
                	}

                	this.passwordError = '';
                	this.errors = error.response.data;
                	this.sending = false;
                });
            },

            /**
             * updates users' password. old-password is required
             *
             * @return void
             */
            updatePassword() {
            	this.sending = true;
            	this.passwordSaved = false;

                axios.post('/update-password', {
                	oldpassword: this.oldpassword,
                	password: this.newpassword,
                	password_confirmation: this.confirmpassword
                })
                .then((response) => {
                	this.oldpassword = '';
					this.newpassword = '';
					this.confirmpassword = '';
					this.sending = false;

					this.passwordSaved = true;
                }).catch((error) => {
                	if (error.response.status == 422) {
                		this.errors = [];
                		this.passwordError = error.response.data;
                		this.sending = false;
                		return;
                	}

                	this.passwordError = '';
                	this.errors = error.response.data;
                	this.sending = false;
                });
            },
        }
    };
</script>
