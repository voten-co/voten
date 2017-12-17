<template>
    <section>
        <h3 class="dotted-title">
            <span>
                Account
            </span>
        </h3>

        <el-alert
                v-if="customError"
                :title="customError"
                type="error"
        ></el-alert>

        <el-form label-position="top" label-width="10px" :model="form">
            <el-form-item label="Username">
                <el-input placeholder="Username..." v-model="form.username"></el-input>
                <el-alert v-for="e in errors.username" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <el-form-item label="Font">
                <el-select v-model="form.font" placeholder="Font..." filterable>
                    <el-option
                            v-for="item in fonts"
                            :key="item"
                            :label="item"
                            :value="item">
                    </el-option>
                </el-select>
            </el-form-item>

            <h3 class="dotted-title">
                <span>
                    Notify me when
                </span>
            </h3>

            <div class="form-toggle">
                My submissions get comments:
                <el-switch v-model="form.notify_submissions_replied"></el-switch>
            </div>

            <div class="form-toggle">
                My comments get replies:
                <el-switch v-model="form.notify_comments_replied"></el-switch>
            </div>

            <div class="form-toggle no-border">
                My username gets mentioned:
                <el-switch v-model="form.notify_mentions"></el-switch>
            </div>

            <!-- submit -->
            <el-form-item v-if="changed">
                <el-button type="success" @click="save" :loading="sending" size="medium">Save</el-button>
            </el-form-item>
        </el-form>
    </section>
</template>

<script>
    import Helpers from '../mixins/Helpers';

    export default {
        mixins: [Helpers],

        data() {
            return {
                sending: false,
            	errors: [],
            	customError: '',
                auth,
				fonts: [
					'Josefin Sans', 'Lato', 'Source Sans Pro', 'Ubuntu', 'Open Sans', 'Dosis', 'Reem Kufi', 'Athiti' ,
					'Molengo', 'Catamaran', 'Roboto', 'Eczar', 'Titillium Web', 'Varela Round', 'Bree Serif', 'Alegreya Sans',
					'Sorts Mill Goudy', 'Patrick Hand', 'Dancing Script', 'Satisfy', 'Montserrat', 'Gloria Hallelujah', 'Courgette',
					'Indie Flower', 'Handlee', 'Arvo'
				],

                form: {
                    username: auth.username,
                    font: auth.font,
                    notify_submissions_replied: auth.notify_submissions_replied,
                    notify_comments_replied: auth.notify_comments_replied,
                    notify_mentions: auth.notify_mentions,
                }
            }
        },

        created() {
        	document.title = 'My Account | Settings'
        },

	    computed: {
	    	changed () {
	    		if (
	                auth.font != this.form.font ||
	                auth.notify_submissions_replied != this.form.notify_submissions_replied ||
	                auth.notify_mentions != this.form.notify_mentions ||
	                auth.username != this.form.username ||
	                auth.notify_comments_replied != this.form.notify_comments_replied
	                ) {
		    			return true; 
		    		}

	    		return false; 
	    	},
	    },

        methods: {
            /**
             * Stores the changes in the database. (using the recently changed values)
             *
             * @return void
             */
            save () {
                this.sending = true;

                let changedFont = (auth.font !== this.form.font);
                let changedUsername = (auth.username !== this.form.username);

            	axios.post( '/update-account', {
                    username: this.form.username,
                    font: this.form.font,
                    notify_submissions_replied: this.form.notify_submissions_replied,
                    notify_comments_replied: this.form.notify_comments_replied,
                    notify_mentions: this.form.notify_mentions,
                }).then(() => {
	                this.errors = []; 
	                this.customError = ''; 

	                auth.font = this.form.font;
	                auth.username = this.form.username;
	                auth.notify_submissions_replied = this.form.notify_submissions_replied;
	                auth.notify_comments_replied = this.form.notify_comments_replied;
                    auth.notify_mentions = this.form.notify_mentions;
                    
                    if (changedFont) {
                        this.loadWebFont();
                    }

                    if (changedUsername) {
                        window.location = `/@${this.form.username}/settings/account`;
                    }

                    this.sending = false; 
	            }).catch((error) => {
	                if(error.response.status == 500) {
	                	this.sending = false;
	                    this.customError = error.response.data;
	                    this.errors = [];
	                    return;
	                }

                    this.sending = false;

	                this.errors = error.response.data.errors;
	            })
            },
        }
    };
</script>
