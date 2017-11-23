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

        <el-form label-position="top" label-width="10px">
            <el-form-item>
                <el-button type="danger" plain size="medium" @click="deleteMyAccount = true" v-if="!deleteMyAccount">
                    Delete my account
                </el-button>
            </el-form-item>

            <div v-if="deleteMyAccount" class="margin-bottom-1">
                <el-form-item label="To confirm this action please enter your password">
                    <el-input placeholder="Password..." v-model="password" type="password"></el-input>
                    <el-alert v-if="passwordError" :title="passwordError" type="error"></el-alert>
                </el-form-item>

                <el-form-item>
                    <el-button type="success" size="medium" @click="destroyAccount" :disabled="!password" :loading="sending">
                        Confirm
                    </el-button>
                    <el-button type="text" size="medium" @click="deleteMyAccount = false">
                        Cancel
                    </el-button>
                </el-form-item>
            </div>
        </el-form>
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
                this.sending = true;

                axios.post('/delete-my-account', {
                	password: this.password
                }).then(() => {
                    this.sending = false;
                    window.location = "/logout";
                }).catch((error) => {
                    this.sending = false;
                    if (error.response.status == 422) {
                		this.passwordError = error.response.data;
                	}
                });
            },
        }
    };
</script>
