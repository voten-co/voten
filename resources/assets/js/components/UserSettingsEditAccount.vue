<template>
    <section>
        <h3 class="dotted-title">
            <span>
                My Account
            </span>
        </h3>

        <div class="form-group">
            <label for="username" class="form-label">Username:</label>

            <input type="text" class="form-control" placeholder="Username..." v-model="username" id="username">

            <small class="text-muted go-red" v-for="e in errors.username">{{ e }}</small>
        </div>

        <div class="form-group">
            <label for="font" class="form-label">Font:</label>

            <multiselect :value="font" :options="fonts" @input="changeFont"
                :placeholder="'Font...'"
            ></multiselect>
        </div>

        <div class="form-group">
            <label for="sidebar_color" class="form-label">Sidebar Color:</label>

            <multiselect :value="sidebar_color" :options="sideColors" @input="changeSidebarColor"
                :placeholder="'Sidebar Color...'"
            ></multiselect>
        </div>

        <h3 class="v-ultra-bold">Notify me when:</h3>

        <div class="form-group ui form">
            <div class="inline field">
                <div class="ui toggle checkbox">
                    <input type="checkbox" tabindex="0" class="hidden" v-model="notify_submissions_replied">
                    <label>My submissions get comments</label>
                </div>
            </div>
        </div>

        <div class="form-group ui form">
            <div class="inline field">
                <div class="ui toggle checkbox">
                    <input type="checkbox" tabindex="0" class="hidden" v-model="notify_comments_replied">
                    <label>My comments get replies</label>
                </div>
            </div>
        </div>

        <button class="v-button v-button--green" @click="save" :disabled="sending" v-if="changed">Save</button>
    </section>
</template>

<script>
	import Multiselect from 'vue-multiselect'

    export default {

	    components: {
			Multiselect
	    },


        data: function () {
            return {
                sending: false,
            	errors: [],
            	customError: '',
                auth,
                font: auth.font,
				fonts: [
					'Josefin Sans', 'Lato', 'Source Sans Pro', 'Ubuntu', 'Open Sans', 'Dosis', 'Reem Kufi', 'Athiti' ,
					'Molengo', 'Catamaran', 'Roboto', 'Eczar', 'Titillium Web', 'Varela Round', 'Bree Serif', 'Alegreya Sans',
					'Sorts Mill Goudy', 'Patrick Hand', 'Dancing Script', 'Satisfy', 'Montserrat', 'Gloria Hallelujah', 'Courgette',
					'Indie Flower', 'Handlee', 'Arvo'
				],
                sidebar_color: auth.sidebar_color,
				sideColors: [
					'Blue', 'Dark Blue', 'Red', 'Dark', 'Gray', 'Green', 'Purple'
				],
                notify_submissions_replied: auth.notify_submissions_replied,
                notify_comments_replied: auth.notify_comments_replied,
                username: auth.username
            }
        },

        created () {
        	document.title = 'My Account | Settings'
        },

        mounted () {
			this.$nextTick(function () {
				this.$root.loadCheckBox()
				this.$root.autoResize()
			})
        },

	    computed: {
	    	changed () {
	    		if (
	                auth.sidebar_color != this.sidebar_color ||
	                auth.font != this.font ||
	                auth.notify_submissions_replied != this.notify_submissions_replied ||
	                auth.username != this.username ||
	                auth.notify_comments_replied != this.notify_comments_replied
	                ) {
		    			return true
		    		}

	    		return false
	    	},
	    },

        methods: {
			// used for multi select
            changeFont(newSelected) {
                this.font = newSelected
            },

			// used for multi select
            changeSidebarColor(newSelected) {
                this.sidebar_color = newSelected
            },

            /**
             * Stores the changes in the database. (using the recently changed values)
             *
             * @return void
             */
            save () {
            	// whether or not a page-refresh is needed
            	var refresh = false
            	if ( auth.font != this.font || auth.sidebar_color != this.sidebar_color) {
            		refresh = true
            	}

                this.sending = true

            	axios.post( '/update-account', {
                    sidebar_color: this.sidebar_color,
                    username: this.username,
                    font: this.font,
                    notify_submissions_replied: this.notify_submissions_replied,
                    notify_comments_replied: this.notify_comments_replied,
                }).then((response) => {
	                this.errors = []
	                this.customError = ''

                    auth.sidebar_color = this.sidebar_color
	                auth.font = this.font
	                auth.username = this.username
	                auth.notify_submissions_replied = this.notify_submissions_replied
	                auth.notify_comments_replied = this.notify_comments_replied

	                if (refresh){
	                	location.reload()
	                }

                    this.sending = false

	            }).catch((error) => {
	                if(error.response.status == 500){
	                    this.customError = error.response.data.error.message
	                    this.errors = []
	                    return
	                }

                    this.sending = false

	                this.errors = error.response.data
	            })
            },

        }
    };
</script>
