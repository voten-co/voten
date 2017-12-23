<template>
    <section>
        <h3 class="dotted-title">
            <span>
                Email Address
            </span>
        </h3>

        <el-form label-position="top" label-width="10px">
            <el-form-item label="Email Address">
                <el-input placeholder="Email Address..." v-model="email" type="email"></el-input>
                <el-alert v-for="e in errors.email" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <div class="flex-space warning warning--info" v-if="showVerificationWarning">
                <p v-if="verificationEmailResent">
                    Verification email sent. Please check your email inbox.
                </p>

                <p v-if="!verificationEmailResent">
                    {{ email }} is not verified yet.
                </p>

                <el-button type="primary" size="small" plain @click="resendVerificationEmail" v-if="!verificationEmailResent">
                    Resend Verification Email
                </el-button>
            </div>

            <el-button type="success" @click="saveEmail = true" :loading="sendingEmail" size="medium" v-if="changedEmail && !saveEmail">
                Save
            </el-button>

            <div v-if="saveEmail">
                <el-form-item label="To confirm this action please enter your password">
                    <el-input placeholder="Password..." v-model="password" type="password"></el-input>
                    <el-alert v-if="passwordError" :title="passwordError" type="error"></el-alert>
                </el-form-item>

                <el-button type="success" @click="updateEmail" :disabled="!password" :loading="sendingEmail" size="medium">
                    Confirm
                </el-button>
                <el-button type="text" @click="saveEmail = false" size="medium">
                    Cancel
                </el-button>
            </div>
        </el-form>


        <h3 class="dotted-title">
            <span>
                Change Password
            </span>
        </h3>

        <el-alert
                title="Your password has been successfully updated."
                type="success"
                v-if="passwordSaved"
                show-icon>
        </el-alert>

        <el-form label-position="top" label-width="10px">
            <el-form-item label="New Password">
                <el-input placeholder="New Password..." v-model="newpassword" type="password"></el-input>
                <el-alert v-for="e in errors.password" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <el-form-item label="Confirm Password">
                <el-input placeholder="Confirm Password..." v-model="confirmpassword" type="password"></el-input>
                <el-alert v-for="e in errors.password" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <el-form-item label="Old Password">
                <el-input placeholder="Enter current password to confirm..." v-model="oldpassword"
                          type="password"></el-input>
                <el-alert v-if="passwordError" :title="passwordError" type="error"></el-alert>
            </el-form-item>

            <el-form-item v-if="changedPassword">
                <el-button type="success" @click="updatePassword" :loading="sendingPassword" size="medium">
                    Save
                </el-button>
            </el-form-item>
        </el-form>
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
                sendingEmail: false,
                sendingPassword: false,
                passwordSaved: false,
                email: auth.email,
                oldpassword: '',
                newpassword: '',
                confirmpassword: '',
                verificationEmailResent: false
            }
        },

        computed: {
            showVerificationWarning() {
                return !auth.confirmedEmail && auth.email;
            },

            changedEmail() {
                return auth.email != this.email;
            },

            changedPassword() {
                return (this.newpassword == this.confirmpassword) && (this.newpassword) && (this.oldpassword);
            }
        },

        methods: {
            /**
             * saves email address into the database
             *
             * @return void
             */
            updateEmail() {
                this.sendingEmail = true;

                axios.post('/update-email', {
                    password: this.password,
                    email: this.email
                }).then(() => {
                    this.errors = [];
                    this.sendingEmail = false;
                    this.saveEmail = false;
                    auth.email = this.email;
                    auth.confirmedEmail = false;

                    this.passwordError = '';
                }).catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = [];
                        this.passwordError = error.response.data;
                        this.sending = false;
                        return;
                    }

                    this.passwordError = '';
                    this.errors = error.response.data.errors;
                    this.sendingEmail = false;
                });
            },

            resendVerificationEmail() {
                this.verificationEmailResent = true;

                axios.post('/email/verify/resend').catch(() => {
                    this.verificationEmailResent = false;
                });
            },

            /**
             * updates users' password. old-password is required
             *
             * @return void
             */
            updatePassword() {
                this.sendingPassword = true;
                this.passwordSaved = false;

                axios.post('/update-password', {
                    oldpassword: this.oldpassword,
                    password: this.newpassword,
                    password_confirmation: this.confirmpassword
                }).then(() => {
                    this.oldpassword = '';
                    this.newpassword = '';
                    this.confirmpassword = '';
                    this.sendingPassword = false;

                    this.passwordSaved = true;

                    this.errors = [];
                    this.passwordError = '';
                }).catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = [];
                        this.passwordError = error.response.data;
                        this.sendingPassword = false;
                        return;
                    }

                    this.passwordError = '';
                    this.errors = error.response.data.errors;
                    this.sending = false;
                });
            },
        }
    };
</script>
