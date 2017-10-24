<template>
    <section>
        <h3 class="dotted-title go-red">
            <span>
                Delete Account
            </span>
        </h3>

        <p>
        	Deleting an account is a permanent action and <b>cannot be undone</b>. It is also going to make us miss you.
        </p>

        <p>
            Click below button to begin. You'll be asked for your password to confirm the action. 
        </p>

        <button class="v-button v-button--red" @click="deleteMyAccount = true" v-if="!deleteMyAccount">
        	Delete my account
        </button>

		<div v-if="deleteMyAccount">
			<div class="form-group">
	            <input type="password" class="form-control" placeholder="Password..." v-model="password" id="password" autocomplete="off">

	            <small class="text-muted go-red" v-if="passwordError">{{ passwordError }}</small>
	        </div>

	        <button class="v-button v-button--green" @click="destroyAccount" :disabled="!password">
	        	Confirm
	        </button>
	        <button class="v-button v-button--red" @click="deleteMyAccount = false">
	        	Cancel
	        </button>
		</div>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                sending: false,
                deleteMyAccount: false,
                password: '',
                passwordError: '',
            	errors: [],
            	customError: '',
                auth,
                username: auth.username
            }
        },

        created() {
        	document.title = 'Delete Account'; 
        },

        methods: {
            /**
             * Destroys account, logs out
             *
             * @return void
             */
            destroyAccount() {
                axios.post('/delete-my-account', {
                	password: this.password
                })
                .then((response) => {
                	window.location = "/logout";
                }).catch((error) => {
                	if (error.response.status == 422) {
                		this.passwordError = error.response.data;
                	}
                });
            },
        }
    };
</script>
