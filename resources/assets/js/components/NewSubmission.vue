<template>
	<el-dialog title="New Submission"
	           :visible="visible"
	           :width="isMobile ? '99%' : '600px'"
	           @close="close"
	           append-to-body
	           class="user-select submit-form">

		<el-form label-position="top"
		         label-width="10px">
			<!-- Title -->
			<el-form-item label="Title:">
				<el-input placeholder="Title ..."
				          class="input-with-select"
				          name="title"
				          :maxlength="150"
				          :minlength="7"
				          v-model="title">
					<el-button round
					           slot="append"
					           type="primary"
					           v-if="submitURL && submissionType === 'link'"
					           @click="getTitle(submitURL)"
					           :loading="loadingTitle">
						Suggest
					</el-button>
				</el-input>

				<el-alert v-for="e in errors.title"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<!-- Text -->
			<el-form-item v-if="submissionType === 'text'"
			              label="Text(optional):">
				<el-input type="textarea"
				          placeholder="Text(optional)..."
				          name="text"
				          resize="none"
				          :maxlength="15000"
				          :autosize="{ minRows: 4, maxRows: 10}"
				          v-model="text">
				</el-input>

				<el-alert v-for="e in errors.text"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>

				<div class="flex-space">
					<el-button type="text"
					           size="mini"
					           @click="$eventHub.$emit('markdown-guide')">
						Formatting Guide
					</el-button>

					<el-button size="mini"
					           @click="preview = !preview"
					           v-show="text"
					           type="text"
					           :icon="preview ? 'el-icon-close' : 'el-icon-view'">
						Preview
					</el-button>
				</div>

				<div v-if="preview && text"
				     class="preview margin-top-1 enable-user-select">
					<markdown :text="text.trim()"></markdown>
				</div>
			</el-form-item>

			<!-- Link -->
			<el-form-item v-if="submissionType === 'link'"
			              label="URL:">
				<el-input placeholder="URL ..."
				          name="url"
				          v-model="submitURL"></el-input>
				<el-alert v-for="e in errors.url"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<!-- GIF -->
			<!-- <el-form-item v-if="submissionType === 'gif'"
			              label="GIF file:">
				<el-upload class="upload-demo"
				           drag
				           :limit="1"
				           action="/api/gifs"
				           :file-list="gifTempArray"
				           :on-preview="gifPreview"
				           :on-remove="removeGif"
				           :on-success="successfulGifUpload"
				           :on-exceed="exceededGifFileCount"
				           :on-error="failedGifUpload"
				           :before-upload="beforeGifUploadCheckings"
				           with-credentials
				           accept=".gif"
				           :headers="{ 'X-CSRF-TOKEN': csrf}">
					<i class="el-icon-upload"></i>
					<div class="el-upload__text">Drop your GIF here or
						<em>click to upload</em>
					</div>
					<div class="el-upload__tip"
					     slot="tip">
						Only animated GIFs with a valid .gif format with a size less than {{ gifSizeLimit }}mb are supported.
					</div>
				</el-upload>

				<el-dialog :visible.sync="previewGifModal"
				           :title="previewGifFileName"
				           append-to-body>
					<img width="100%"
					     :src="previewGifImage"
					     alt="preview">
				</el-dialog>
			</el-form-item> -->

			<!-- Photo(s) -->
			<el-form-item v-if="submissionType === 'img'"
			              label="Photo(s):">
				<el-upload class="upload-demo"
				           drag
				           :limit="20"
				           action="/api/photos"
				           :file-list="photos"
				           :on-preview="photoPreview"
				           :on-remove="removePhoto"
				           :on-success="successfulPhotoUpload"
				           :on-exceed="exceededPhotoFileCount"
				           :on-error="failedPhotoUpload"
				           :before-upload="beforePhotoUploadCheckings"
				           with-credentials
				           accept=" .jpg, .jpeg, .png"
				           :headers="{ 'X-CSRF-TOKEN': csrf}">
					<i class="el-icon-upload"></i>
					<div class="el-upload__text">Drop photo here or
						<em>click to upload</em>
					</div>
					<div class="el-upload__tip"
					     slot="tip">Up to {{ photosNumberLimit }} jpg/png files with a size less than {{ photosSizeLimit }}mb
					</div>
				</el-upload>

				<el-dialog :visible.sync="previewPhotoModal"
				           :title="previewPhotoFileName"
				           append-to-body>
					<img width="100%"
					     :src="previewPhotoImage"
					     alt="preview">
				</el-dialog>
			</el-form-item>

			<!-- Select Channel -->
			<el-form-item label="Channel:">
				<el-select v-model="selectedCat"
				           filterable
				           remote
				           placeholder="#channel..."
				           :remote-method="getSuggestedChannels"
				           loading-text="Loading..."
				           :loading="loadingChannels">
					<el-option v-for="item in suggestedCats"
					           :key="item"
					           :label="item"
					           :value="item">
					</el-option>
				</el-select>
				<el-alert v-for="e in errors.channel_name"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<!-- NSFW Toggle -->
			<el-form-item>
				<el-checkbox v-model="sfw">
					Safe for work
				</el-checkbox>
			</el-form-item>

			<hr class="dashed-hr">

			<div class="flex-space">
				<div class="submit-type">
					<el-tooltip content="Photo(s)"
					            placement="top"
					            transition="false"
					            :open-delay="500">
						<i class="v-icon v-photo"
						   :class="{ 'go-primary': submissionType == 'img'}"
						   @click="changeSubmissionType('img')"></i>
					</el-tooltip>

					<el-tooltip content="Link"
					            placement="top"
					            transition="false"
					            :open-delay="500">
						<i class="v-icon v-link"
						   :class="{ 'go-primary': submissionType == 'link'}"
						   @click="changeSubmissionType('link')"></i>
					</el-tooltip>

					<el-tooltip content="Text"
					            placement="top"
					            transition="false"
					            :open-delay="500">
						<i class="v-icon v-text"
						   :class="{ 'go-primary': submissionType == 'text'}"
						   @click="changeSubmissionType('text')"></i>
					</el-tooltip>

					<!-- <el-tooltip content="Animated GIF"
					            placement="top"
					            transition="false"
					            :open-delay="500">
						<i class="v-icon v-gif"
						   :class="{ 'go-primary': submissionType == 'gif'}"
						   @click="changeSubmissionType('gif')"></i>
					</el-tooltip> -->
				</div>

				<el-button round
				           type="success"
				           size="mini"
				           @click="submit"
				           :disabled="!goodToGo"
				           :loading="loading">
					Submit
				</el-button>
			</div>
		</el-form>
	</el-dialog>
</template>

<script>
import Markdown from '../components/Markdown.vue';
import Helpers from '../mixins/Helpers';
import SubmitFormUpload from '../mixins/SubmitFormUpload';

export default {
    props: ['visible'],
    mixins: [Helpers, SubmitFormUpload],
    components: { Markdown },

    data() {
        return {
            errors: [],

            loading: false,
            loadingChannels: false,
            selectedCat: null,
            suggestedCats: [],
            submissionType: 'link',
            title: '',
            sfw: true,

            // Link
            submitURL: '',
            loadingTitle: false,

            // Text
            preview: false,
            text: '',

            // Photo
            photos: [],
            photosNumberLimit: 20,
            photosSizeLimit: 10,
            previewPhotoImage: '',
            previewPhotoFileName: '',
            previewPhotoModal: false

            // GIF
            // gifTempArray: [],
            // gif_id: null,
            // gifNumberLimit: 1,
            // gifSizeLimit: 50,
            // previewGifImage: '',
            // previewGifFileName: '',
            // previewGifModal: false
        };
    },

    computed: {
        goodToGo() {
            if (this.submissionType == 'link') {
                return this.title.trim().length > 0 && this.selectedCat && this.submitURL && !this.loading;
            }

            if (this.submissionType == 'img') {
                return this.title.trim().length > 0 && this.selectedCat && this.photos.length && !this.loading;
            }

            return this.title.trim().length > 0 && this.selectedCat && !this.loading;
        }
    },

    created() {
        this.setDefaultChannels();
        this.submitApi();
    },

    watch: {
        visible() {
            if (this.visible) {
                this.setDefaultChannels();
                this.submitApi();
                window.location.hash = 'newSubmission';
            } else {
                if (window.location.hash == '#newSubmission') {
                    history.go(-1);
                }
            }
        }
    },

    methods: {
        close() {
            this.$emit('update:visible', false);
        },

        /**
         * Used for setting the values using API. This will get extended in the future to support
         * voten sharing buttons! But for now we are just going to use it for setting the default
         * channel so when clicked on submit in the channels, users won't have to set channel.
         *
         * @return
         */
        submitApi() {
            if (this.$route.params.name) {
                this.selectedCat = this.$route.params.name;
            } else {
                this.selectedCat = null;
            }

            this.sfw = !Store.page.channel.temp.nsfw;
        },

        /**
         * Sets the default value for suggestCats (uses user's already subscriber channels)
         *
         * @return void
         */
        setDefaultChannels() {
            let array = [];

            Store.state.subscribedChannels.forEach((element, index) => {
                array.push(element.name);
            });

            this.suggestedCats = array;
        },

        /**
         * Submits the form.
         *
         * @return void
         */
        submit() {
            this.loading = true;

            let formData = this.prepareFormData();

            axios
                .post('/submissions', formData)
                .then(response => {
                    this.loading = false;

                    Store.state.submissions.likes.push(response.data.data.id);
                    this.$router.push('/c/' + this.selectedCat + '/' + response.data.data.slug);

                    this.close();
                    this.reset();
                })
                .catch(error => {
                    this.loading = false;
                    this.errors = error.response.data.errors;
                });
        },

        prepareFormData() {
            let formData = new FormData();

            formData.append('title', this.title);
            formData.append('channel_name', this.selectedCat);
            formData.append('nsfw', !this.sfw);
            formData.append('type', this.submissionType);

            switch (this.submissionType) {
                case 'text':
                    formData.append('text', this.text);
                    break;

                case 'link':
                    formData.append('url', this.submitURL);
                    break;

                case 'gif':
                    formData.append('gif_id', this.gif_id);
                    break;

                case 'img':
                    let arr = _.map(this.photos, 'id');
                    for (let i = 0; i < arr.length; i++) {
                        formData.append('photos_id[]', arr[i]);
                    }
                    break;
            }

            return formData;
        },

        /**
         * Fetches the title from the external URL (through Voten's proxy server which we contact via API)
         *
         * @param string typed
         * @return void
         */
        getTitle(typed) {
            if (!typed.trim()) return;

            this.loadingTitle = true;

            axios
                .get('/links/title', {
                    params: {
                        url: typed
                    }
                })
                .then(response => {
                    this.title = response.data.data.title;
                    this.loadingTitle = false;
                    this.errors.url = [];
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                    this.loadingTitle = false;
                });
        },

        /**
         * Searches through channels.
         *
         * @param string typed
         * @return void
         */
        getSuggestedChannels: _.debounce(function(typed) {
            if (!typed) return;

            this.loadingChannels = true;

            axios
                .get('/search', {
                    params: {
                        type: 'Channels',
                        keyword: typed
                    }
                })
                .then(response => {
                    this.suggestedCats = _.map(response.data.data, 'name');
                    this.loadingChannels = false;
                })
                .catch(() => {
                    this.loadingChannels = false;
                });
        }, 600),

        changeSubmissionType(newType) {
            this.submissionType = newType;
        },

        reset() {
            this.errors = [];

            this.loading = false;
            this.loadingChannels = false;
            this.selectedCat = null;
            this.suggestedCats = [];
            this.title = '';
            this.sfw = true;

            // Link
            this.submitURL = '';
            this.loadingTitle = false;

            // Text
            this.preview = false;
            this.text = '';

            // Photo
            this.photos = [];
            this.previewPhotoImage = '';
            this.previewPhotoFileName = '';
            this.previewPhotoModal = false;

            // GIF
            // this.gifTempArray = [];
            // this.gif_id = null;
            // this.previewGifImage = '';
            // this.previewGifFileName = '';
            // this.previewGifModal = fals;
        }
    }
};
</script>
