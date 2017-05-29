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
        </div>

        <h3 class="dotted-title">
            <span>
                Change Password
            </span>
        </h3>

        <div class="form-group">
            <label for="newpassword" class="form-label">New Password:</label>
            <input type="password" class="form-control" placeholder="New Password..." v-model="newpassword" id="newpassword">
        </div>
        <div class="form-group">
            <label for="confirmpassword" class="form-label">Confirm Password:</label>
            <input type="password" class="form-control" placeholder="Confirm Password..." v-model="confirmpassword" id="confirmpassword">
        </div>

        <div class="form-group">
            <label for="oldpassword" class="form-label">Old Password:</label>
            <input type="password" class="form-control" placeholder="Enter current password to confirm..." v-model="oldpassword" id="oldpassword">
        </div>

        <button class="v-button v-button--green" :disabled="sending" v-if="changedPassword">Save</button>
	</section>
</template>

<script>
    export default {
        components: {},

        mixins: [],

        data () {
            return {
                auth,
                sending: false,
                email: auth.email,
				oldpassword: '',
				newpassword: '',
				confirmpassword: ''
            }
        },

        computed: {
            changedEmail() {
            	return auth.email != email;
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
            //
        }
    };
</script>
