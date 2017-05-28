<template>
    <section>
        <h3 class="dotted-title">
            <span>
                My Public Profile
            </span>
        </h3>

        <div class="v-status v-status--error" v-if="customError">
            {{ customError }}
        </div>

        <div class="form-group">
            <label for="color" class="form-label">Cover Color:</label>

            <multiselect :value="color" :options="coverColors" @input="changeColor"
                :placeholder="'Cover Color...'"
            ></multiselect>
        </div>

        <div class="form-group">
            <label for="name" class="form-label">Full Name:</label>
            <input type="text" class="form-control" placeholder="Your full name..." v-model="name" id="name">
        </div>

        <div class="form-group">
            <label for="bio" class="form-label">Bio:</label>

            <textarea class="form-control" rows="3" v-model="bio" id="bio" placeholder="How would you describe you?"></textarea>

            <small class="text-muted go-red" v-for="e in errors.bio">{{ e }}</small>
        </div>

        <div class="form-group">
            <label for="website" class="form-label">Website:</label>

            <input type="url" class="form-control" v-model="website" id="website" placeholder="Website"  v-bind:value="auth.website">

            <small class="text-muted go-red" v-for="e in errors.website">{{ e }}</small>
        </div>

        <div class="form-group">
            <label for="location" class="form-label">Location:</label>

            <input type="text" class="form-control" v-model="location" id="location" placeholder="Location">

            <small class="text-muted go-red" v-for="e in errors.location">{{ e }}</small>
        </div>

        <div class="form-group">
            <label for="twitter" class="form-label">Twitter Username:</label>

            <input type="text" class="form-control" v-model="twitter" id="twitter" placeholder="Twitter Username...">

            <small class="text-muted go-red" v-for="e in errors.twitter">{{ e }}</small>
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
                Store,
                name: auth.name,
                bio: auth.bio,
                website: auth.info.website,
                color: auth.color,
				coverColors: [
					'Blue', 'Dark Blue', 'Red', 'Dark', 'Dark Green', 'Bright Green', 'Purple', 'Pink', 'Orange'
				],
                location: auth.location,
                twitter: auth.info.twitter,
            }
        },

        created () {
        	document.title = 'My Profile | Settings'
        },

        mounted () {
			this.$nextTick(function () {
				// this.$root.loadCheckBox()
				this.$root.autoResize()
			})
        },

	    computed: {
	    	changed () {
	    		if (
	    			auth.name != this.name ||
	                auth.bio != this.bio ||
	                auth.info.website != this.website ||
	                auth.location != this.location ||
	                auth.color != this.color ||
                    auth.info.twitter != this.twitter
	                ) {
		    			return true
		    		}

	    		return false
	    	},
	    },

        methods: {
			// used for multi select
            changeColor(newSelected) {
                this.color = newSelected
            },

            /**
             * Stores the changes in the database. (using the recently changed values)
             *
             * @return void
             */
            save () {
                this.sending = true

            	axios.post( '/update-profile', {
                    name: this.name,
                    bio: this.bio,
                    website: this.website,
                    location: this.location,
                    color: this.color,
                    twitter: this.twitter,
                }).then((response) => {
	                this.errors = []
	                this.customError = ''

	                auth.name = this.name
	                auth.bio = this.bio
	                auth.location = this.location
	                auth.color = this.color
	                auth.info.website = this.website
	                auth.info.twitter  = this.twitter

                    this.sending = false
	            }).catch((error) => {
	                if(error.response.status == 500){
	                	this.sending = false
	                    this.customError = error.response.data
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
