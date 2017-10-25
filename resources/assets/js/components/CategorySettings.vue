<template>
	<section>
		<h3 class="dotted-title">
            <span>
                Avatar
            </span>
        </h3>

        <div class="form-group">
            <div class="flex-space">
                <div>
                    <button class="v-button v-button--upload" type="button">
                        <i class="v-icon v-upload" aria-hidden="true"></i> Click To Browse 

                        <input class="v-button" type="file" @change="passToCropModal" />
                    </button>
                    
                    <p class="go-gray go-small">
                        You can upload any size image file. After uploading is done, you'll get to position and size your image. 
                    </p>
                </div>

                <div class="edit-avatar-preview">
                    <img v-bind:alt="Store.category.name" v-bind:src="Store.category.avatar" class="circle" />
                </div>
            </div>
        </div>

		<h3 class="dotted-title">
			<span>
				Settings
			</span>
		</h3>

		<div class="form-group">
			<label for="description" class="form-label">Description</label>

			<textarea class="form-control" rows="3" name="description" v-model="description" id="description" :placeholder="'How would you describe #' + Store.category.name + '?'"></textarea>

			<small class="text-muted go-red" v-for="e in errors.description">{{ e }}</small>
		</div>

		<div class="form-group">
			<label for="color" class="form-label">Cover Color:</label>

			<multiselect :value="color" :options="colors" @input="changeColor"
				:placeholder="'Cover Color...'"
			></multiselect>
		</div>

		<div class="form-toggle">
			This channel contains mostly NSFW content:
			<toggle-button v-model="nsfw"/>
		</div>

		<button class="v-button v-button--green" @click="save" v-if="changed" :disabled="sending">Save</button>
	</section>
</template>

<script>
	import Multiselect from 'vue-multiselect'

    export default {
		components: {
			Multiselect
	    },

        data() {
            return {
            	errors: [],
            	customError: '',
				sending: false,
                Store,
                auth,
            	description: Store.category.description,
                nsfw: Store.category.nsfw,
                color: Store.category.color,
				colors: [
					'Blue', 'Dark Blue', 'Red', 'Dark', 'Dark Green', 'Bright Green', 'Purple', 'Orange', 'Pink'
				], 
				fileUploadFormData: new FormData(),
            }
        },

		watch: {
			'Store.category': function () {
				this.description = Store.category.description
				this.nsfw = Store.category.nsfw
				this.color = Store.category.color
			}
		},

	    computed: {
	    	changed () {
	    		if (
	                Store.category.color != this.color ||
	                Store.category.nsfw != this.nsfw ||
	                Store.category.description != this.description
	                )
    			{
	    			return true
	    		}

	    		return false
	    	},
	    },

        mounted () {
			this.$nextTick(function () {
				this.$root.autoResize();
			})
        },

        methods: {
			/**
			 * Passes the photo to the cropModal to take care of the rest
			 *
			 * @return void
			 */
			passToCropModal (e)
			{
				this.fileUploadFormData.append('photo', e.target.files[0]);

				axios.post('/upload-temp-avatar', this.fileUploadFormData).then((response) => {
					this.$eventHub.$emit('crop-photo-uploaded', response.data);
				});

				this.$eventHub.$emit('crop-category-photo');
			},
			
        	save () {
				this.sending = true

        		axios.post('/category-patch', {
        			name: Store.category.name,
        			description: this.description,
        			nsfw: this.nsfw,
        			color: this.color
        		}).then((response) => {
	                this.errors = []
	                this.customError = ''

        			Store.category.nsfw = this.nsfw
        			Store.category.color = this.color
        			Store.category.description = this.description
					this.sending = false
        		}).catch((error) => {
	                if(error.response.status == 500) {
	                	this.sending = false
	                    this.customError = error.response.data
	                    this.errors = []
	                    return
	                }

	                this.errors = error.response.data.errors;
					this.sending = false
	            });
        	},

            // used for multi select
			 changeColor(newSelected) {
                 this.color = newSelected
             },
        },


		beforeRouteEnter(to, from, next){
            if (Store.category.name == to.params.name) {
                // loaded
                if (Store.administratorAt.indexOf(Store.category.id) != -1) {
                    next()
                }
            } else {
                // not loaded but let's continue (the server-side is still protecting us!)
                next()
            }
        },
    };
</script>
