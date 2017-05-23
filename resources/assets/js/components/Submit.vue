<template>
    <section class="container margin-top-5 col-7 user-select" id="new-submission">
        <moon-loader :loading="loading" :size="'40px'" :color="'#38634f'" class="form-loader"></moon-loader>

        <div :class="loading ? 'opacity-fade' : ''">
            <h1 class="align-center">
                New Submission
            </h1>

            <div class="v-status v-status--error" v-if="customError">
                {{ customError }}
            </div>

            <form action="/submit" method="post">
                <div class="form-group relative">
                    <input type="text" class="form-control v-input-big" v-bind:class="{ 'btn-input-text': submissionType == 'link'}"
                    id="title" name="title" placeholder="Title ..." autocomplete="off" v-model="fTitle" :disabled="loading">

                    <button type="button" class="v-button v-button--primary btn-input" @click="getTitle(submitURL)"
                    v-if="submissionType == 'link' && submitURL && !loadingTitle"
                            data-toggle="tooltip" data-placement="bottom" title="Fetch title from entered URL" :disabled="loading"
                    >Suggest</button>

                    <moon-loader :loading="loadingTitle" :size="'30px'" :color="'#777'" class="btn-input"></moon-loader>

                    <small class="text-muted go-red" v-for="e in errors.title">{{ e }}</small>
                </div>

                <input type="hidden" name="type" v-bind:value="submissionType">

                <div v-show="submissionType == 'text'">
                    <textarea class="form-control v-input-big" rows="3" id="text" name="text" placeholder="Text(optional)..."
                    v-model="fText" :disabled="loading"></textarea>

                    <div class="flex-space">
                        <a class="comment-form-guide text-muted" @click="$eventHub.$emit('markdown-guide')">
        	            	Formatting Guide
        	            </a>

                        <a class="comment-form-guide text-muted" @click="preview = !preview" v-show="fText">
        	            	Preview
        	            </a>
                    </div>


                    <small class="text-muted go-red" v-for="e in errors.text">{{ e }}</small>
                </div>

                <div v-if="preview && fText" class="form-wrapper">
                    <markdown :text="fText"></markdown>
              	</div>

                <div class="form-group" v-if="submissionType == 'link'">
                    <input type="text" class="form-control v-input-big" id="url" name="url" placeholder="URL ..." autocomplete="off" v-model="submitURL" :disabled="loading">

                    <small class="text-muted go-red" v-for="e in errors.url">{{ e }}</small>
                </div>

                <div class="form-group" v-if="submissionType == 'gif'">
                    <input type="file" class="form-control v-input-big" id="gif" name="gif"
                    @change="gifSelected" accept="image/gif" :disabled="loading">

                    <small class="text-muted go-red" v-for="e in errors.gif">{{ e }}</small>
                </div>

                <div v-show="submissionType == 'img'">
                    <div class="form-group" v-show="submissionType == 'img'">
                        <form action="/upload-photo" class="dropzone" method="post" id="addPhotosForm">
                            <input type="hidden" name="_token" v-bind:value="csrf">
                            <div class="fallback">
                                <input name="photo" type="file" multiple />
                            </div>
                        </form>
                        <small class="text-muted go-red" v-for="e in errors.photos">{{ e }}</small>
                    </div>
                </div>

                <div class="form-group">
                    <multiselect :value="selectedCat" :options="suggestedCats" @input="updateSelected"
                    @search-change="getSuggestedCats" :placeholder="'#channel...'"
                    ></multiselect>

                    <small class="text-muted go-red" v-for="e in errors.name">{{ e }}</small>
                </div>

                <hr class="dashed-hr">

                <div class="flex-space">
                    <div>
                        <span class="fa-stack fa-lg fa-pull-left" @click="changeSubmissionType('img')">
                            <i class="v-icon v-photo" v-bind:class="{ 'go-primary': submissionType == 'img'}"
                            data-toggle="tooltip" data-placement="top" title="Image"
                            ></i>
                        </span>

                        <span class="fa-stack fa-lg fa-pull-left" @click="changeSubmissionType('link')">
                            <i class="v-icon v-link" v-bind:class="{ 'go-primary': submissionType == 'link'}"
                            data-toggle="tooltip" data-placement="top" title="Link"
                            ></i>
                        </span>

                        <span class="fa-stack fa-lg fa-pull-left" @click="changeSubmissionType('text')">
                            <i class="v-icon v-text" v-bind:class="{ 'go-primary': submissionType == 'text'}"
                            data-toggle="tooltip" data-placement="top" title="Text"
                            ></i>
                        </span>

                        <span class="fa-stack fa-lg fa-pull-left" @click="changeSubmissionType('gif')">
                            <i class="v-icon v-gif" v-bind:class="{ 'go-primary': submissionType == 'gif'}"
                            data-toggle="tooltip" data-placement="top" title="Animated GIF"
                            ></i>
                        </span>
                    </div>

                    <button type="submit" class="v-button v-button--green pull-right" @click="submit" :disabled="!goodToGo">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </section>
</template>

<script>
    import Markdown from '../components/Markdown.vue'
    import Multiselect from 'vue-multiselect'
    import MoonLoader from '../components/MoonLoader.vue'

    window.Dropzone = require('../libs/dropzone')
    Dropzone.autoDiscover = false

    export default {

       components: {
            Multiselect,
            MoonLoader,
            Markdown
        },

        data () {
            return {
                loadingTitle: false,
                fTitle: '',
                preview: false,
                fText: '',
                submitURL: '',
                photo: '',
                errors: [],
                customError: '',
                csrf: window.Laravel.csrfToken,
                loading: false,
                selectedCat: null,
                suggestedCats :[],
                submissionType: 'link',
                photos: [],
                Store,
                gifUploadFormData: new FormData(),
            }
        },

        computed: {
        	goodToGo() {
                if (this.submissionType == "link") {
                    return (this.fTitle.trim().length > 7 && this.selectedCat && this.submitURL && !this.loading)
                }

                if (this.submissionType == "img") {
                    return (this.fTitle.trim().length > 7 && this.selectedCat && this.photos.length && !this.loading)
                }

                return (this.fTitle.trim().length > 7 && this.selectedCat && !this.loading)
        	}
        },

        created () {
            this.dropzone()
            this.setDefaultCategories()
            this.submitApi()
        },

		mounted: function () {
			this.$nextTick(function () {
				this.$root.loadSemanticTooltip()
				this.loadDropzone()
				this.$root.autoResize()
			})
		},

        watch: {
            'Store.subscribedCategories': function () {
                this.setDefaultCategories()
            }
        },

        methods: {
            /**
             * Used for setting the values using API. This will get extended in the future to support
             * voten sharing buttons! But for now we are just going to use it for setting the default
             * channel so when clicked on submit in the channels, users won't have to set channel.
             *
             * @return
             */
            submitApi() {
                if (this.$route.query.channel) {
                    this.selectedCat = this.$route.query.channel
                }
            },

            /**
             * Sets the default value for suggestCats (uses user's already subscriber channels)
             *
             * @return void
             */
            setDefaultCategories(){
                let array = []

                Store.subscribedCategories.forEach(function(element, index) {
                    array.push(element.name)
                })

                this.suggestedCats = array
            },

            // used for multi select
            updateSelected(newSelected) {
                this.selectedCat = newSelected
            },

        	dropzone() {
            	var that = this
	            Dropzone.options.addPhotosForm  = {
	                paramName: 'photo',
	                maxFileSize: 10,
                    // addRemoveLinks: true,
	                acceptedFiles: '.jpg, .jpeg, .png, .gif',
	                success: function(file, data) {
	                    that.photos.push(data)
	                }
	            }
        	},

        	loadDropzone() {
                $(".dropzone").dropzone()
            },

            gifSelected(e) {
                this.gifUploadFormData = new FormData();

                this.gifUploadFormData.append('gif', e.target.files[0]);
            },

        	submit(e) {
                e.preventDefault()

        		this.loading = true;

                if (this.submissionType == 'gif') {
                    this.gifUploadFormData.append('title', this.fTitle);
                    this.gifUploadFormData.append('name', this.selectedCat);
                    this.gifUploadFormData.append('type', this.submissionType);

                    axios.post('/submit', this.gifUploadFormData).then((response) => {
                        // success
                        this.errors = []

            			Store.submissionUpVotes.push(response.data.id)

                        this.$router.push('/c/' + this.selectedCat + '/' + response.data.slug)

    					this.loading = false
                    }, (response) => {
                        // error
                        if(response.status == 500){
                            this.customError = response.data
                            this.errors = []
                            this.loading = false
                            return
                        }
                        this.errors = response.data
                        this.loading = false
                    })

                    return
                }


                // rest of the types
                axios.post( '/submit', {
                    title: this.fTitle,
                    url: this.submitURL,
                    text: this.fText,
                    name: this.selectedCat,
                    type: this.submissionType,
                    photos: this.photos,
                }).then((response) => {
                    // success
                    this.errors = []

        			Store.submissionUpVotes.push(response.data.id)

                    this.$router.push('/c/' + this.selectedCat + '/' + response.data.slug)

					this.loading = false
                }, (error) => {
                    // error
                    if(error.response.status == 500){
                        this.customError = error.response.data
                        this.errors = []
                        this.loading = false
                        return
                    }

                    this.errors = error.response.data
                    this.loading = false
                })
            },

            getTitle(typed){
                if(!typed) return

                this.loadingTitle = true

                axios.get('/fetch-url-title', {
                	params: {
                		url: typed
                	}
                }).then((response) => {
                    this.fTitle = response.data;
                    this.loadingTitle = false
                    this.errors.url = []
                }, (error) => {
                    if (error.response.status == 500) {
                        this.customError = error.response.data
                        this.errors = []
                        this.loadingTitle = false
                        return
                    }

                    this.errors = error.response.data
                    this.loadingTitle = false
                });
            },

            getSuggestedCats: _.debounce(function (typed) {
                if(!typed) return

                axios.post( '/get-categories', { name: typed } ).then((response) => {
                    this.suggestedCats = response.data
                })
            }, 600),

            changeSubmissionType(newType){
                this.submissionType = newType
            }
        },
    }
</script>


<style media="screen">
    #new-submission {
        position: relative;
    }

    .opacity-fade {
        opacity: 0.6;
    }

    .form-loader {
        position: absolute;
        top: 50%;
        left: 50%;
        z-index: 100;
    }

    .form-loader .v-moon1 {
        height: 60px !important;
        width: 60px !important;
    }

    .form-loader .v-moon2 {
        height: 5.28571px !important;
        width: 5.28571px !important;
        border-radius: 100% !important;
        top: 25.8571px !important;
        opacity: 1 !important;
        background-color: #333 !important;
    }

    .form-loader .v-moon3 {
        height: 60px !important;
        width: 60px !important;
        opacity: 0.2 !important;
        border: 5.71429px solid #000 !important;
    }
</style>
