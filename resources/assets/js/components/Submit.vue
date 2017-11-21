<template>
    <el-dialog
            title="New Submission"
            :visible="visible"
            :width="isMobile ? '99%' : '45%'"
            @close="close"
            append-to-body
            class="user-select submit-form"
    >
        <el-alert
                v-if="customError"
                :title="customError"
                type="error"
        ></el-alert>

        <el-form label-position="top" label-width="10px">
            <!-- Title -->
            <el-form-item>
                <el-input
                        placeholder="Title ..."
                        class="input-with-select"
                        name="title"
                        :maxlength="150"
                        :minlength="7"
                        v-model="title"
                >
                    <el-button slot="append" type="primary" v-if="submitURL && submissionType === 'link'"
                               @click="getTitle(submitURL)" :loading="loadingTitle">
                        Suggest
                    </el-button>
                </el-input>

                <el-alert v-for="e in errors.title" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <!-- Text -->
            <el-form-item v-if="submissionType === 'text'">
                <el-input
                        type="textarea"
                        placeholder="Text(optional)..."
                        name="text"
                        resize="none"
                        :maxlength="15000"
                        :autosize="{ minRows: 4, maxRows: 10}"
                        v-model="text">
                </el-input>

                <el-alert v-for="e in errors.text" :title="e" type="error" :key="e"></el-alert>

                <div class="flex-space">
                    <el-button type="text" size="mini" @click="$eventHub.$emit('markdown-guide')">
                        Formatting Guide
                    </el-button>

                    <el-button size="mini" @click="preview = !preview" v-show="text" type="text"
                               :icon="preview ? 'el-icon-close' : 'el-icon-view'">
                        Preview
                    </el-button>
                </div>

                <div v-if="preview && text" class="preview margin-top-1">
                    <markdown :text="text"></markdown>
                </div>
            </el-form-item>

            <!-- Link -->
            <el-form-item v-if="submissionType === 'link'">
                <el-input placeholder="URL ..." name="url" v-model="submitURL"></el-input>
                <el-alert v-for="e in errors.url" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <!-- GIF -->
            <!--<el-form-item v-if="submissionType === 'gif'">-->
            <!--<div class="align-center">-->
            <!--<el-upload-->
            <!--class="upload-demo"-->
            <!--action="https://jsonplaceholder.typicode.com/posts/"-->
            <!--:on-preview="handlePreview"-->
            <!--:on-remove="handleRemove"-->
            <!--:file-list="fileList"-->
            <!--drag-->
            <!--:limit="1"-->
            <!--:accept="'image/gif'"-->
            <!--&gt;-->
            <!--<i class="el-icon-upload"></i>-->
            <!--<div class="el-upload__text">Drop animated GIF here or <em>click to upload</em></div>-->
            <!--&lt;!&ndash;<div class="el-upload__tip" slot="tip">jpg/png files with a size less than 500kb</div>&ndash;&gt;-->
            <!--</el-upload>-->
            <!--</div>-->
            <!--</el-form-item>-->

            <div class="form-group" v-if="submissionType === 'asdf'">
                <input type="file" class="form-control v-input-big" id="gif" name="gif"
                       @change="gifSelected" accept="image/gif" :disabled="loading">

                <small class="text-muted go-red" v-for="e in errors.gif">{{ e }}</small>
            </div>


            <!-- Photo(s) -->
            <el-form-item v-if="submissionType === 'img'">
                <el-upload
                        class="upload-demo"
                        drag
                        :limit="20"
                        action="/photo"
                        :file-list="photos"
                        :on-preview="photoPreview"
                        :on-remove="removePhoto"
                        :on-success="successfulPhotoUpload"
                        :on-exceed="exceededFileCount"
                        :on-error="failedPhotoUpload"
                        :before-upload="beforePhotoUploadCheckings"
                        with-credentials
                        accept=" .jpg, .jpeg, .png"
                        :headers="{ 'X-CSRF-TOKEN': csrf}"
                >
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">Drop photo here or <em>click to upload</em></div>
                    <div class="el-upload__tip" slot="tip">Up to {{ photosNumberLimit }} jpg/png files with a size less than {{ photosSizeLimit }}mb</div>
                </el-upload>

                <!-- Preview Photo -->
                <el-dialog
                        :visible.sync="previewPhotoModal"
                        :title="previewPhotoFileName"
                        append-to-body
                >
                    <img width="100%" :src="previewPhotoImage" alt="preview">
                </el-dialog>
            </el-form-item>

            <div v-if="submissionType === 'iasdfmg'">
                <div class="form-group">
                    <form action="/upload-photo" class="dropzone" method="post" id="addPhotosForm">
                        <input type="hidden" name="_token" v-bind:value="csrf">
                        <div class="fallback">
                            <input name="photo" type="file" multiple/>
                        </div>
                    </form>
                    <small class="text-muted go-red" v-for="e in errors.photos">{{ e }}</small>
                </div>
            </div>

            <!-- Select Channel -->
            <el-form-item>
                <el-select
                        v-model="selectedCat"
                        filterable
                        remote
                        placeholder="#channel..."
                        :remote-method="getSuggestedCats"
                        loading-text="Loading..."
                        :loading="loadingCategories">
                    <el-option
                            v-for="item in suggestedCats"
                            :key="item"
                            :label="item"
                            :value="item">
                    </el-option>
                </el-select>
                <el-alert v-for="e in errors.name" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <hr class="dashed-hr">

            <div class="flex-space">
                <div class="submit-type">
                    <el-tooltip content="Photo(s)" placement="top" transition="false" :open-delay="500">
                        <i class="v-icon v-photo" :class="{ 'go-primary': submissionType == 'img'}"
                           @click="changeSubmissionType('img')"></i>
                    </el-tooltip>

                    <el-tooltip content="Link" placement="top" transition="false" :open-delay="500">
                        <i class="v-icon v-link" :class="{ 'go-primary': submissionType == 'link'}"
                           @click="changeSubmissionType('link')"></i>
                    </el-tooltip>

                    <el-tooltip content="Text" placement="top" transition="false" :open-delay="500">
                        <i class="v-icon v-text" :class="{ 'go-primary': submissionType == 'text'}"
                           @click="changeSubmissionType('text')"></i>
                    </el-tooltip>

                    <el-tooltip content="Animated GIF" placement="top" transition="false" :open-delay="500">
                        <i class="v-icon v-gif" :class="{ 'go-primary': submissionType == 'gif'}"
                           @click="changeSubmissionType('gif')"></i>
                    </el-tooltip>
                </div>

                <el-button type="success" size="mini" @click="submit" :disabled="!goodToGo" :loading="loading">
                    Submit
                </el-button>
            </div>
        </el-form>
    </el-dialog>
</template>

<script>
    import Markdown from '../components/Markdown.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        props: ['visible'],
        mixins: [Helpers],
        components: { Markdown },

        data() {
            return {
                loadingTitle: false,
                title: '',
                preview: false,
                text: '',
                submitURL: '',
                photo: '',
                errors: [],
                customError: '',
                loading: false,
                loadingCategories: false,
                selectedCat: null,
                suggestedCats: [],
                submissionType: 'img',

                photos: [],
                photosNumberLimit: 20,
                photosSizeLimit: 10,

                gifUploadFormData: new FormData(),

                previewPhotoImage: '',
                previewPhotoFileName: '',
                previewPhotoModal: false
            }
        },

        computed: {
            goodToGo() {
                if (this.submissionType == "link") {
                    return (this.title.trim().length > 0 && this.selectedCat && this.submitURL && !this.loading)
                }

                if (this.submissionType == "img") {
                    return (this.title.trim().length > 0 && this.selectedCat && this.photos.length && !this.loading)
                }

                return (this.title.trim().length > 0 && this.selectedCat && !this.loading)
            },
        },

        created() {
            this.setDefaultCategories();
            this.submitApi();
        },

        watch: {
            'visible'() {
                if (this.visible) this.setDefaultCategories();
            }
        },

        methods: {
            beforePhotoUploadCheckings(file) {
                const isInCorrectFormat = (file.type === 'image/jpeg' || file.type === 'image/jpg' || file.type === 'image/png');
                const doesNotExceedFileSize = file.size / 1024 / 1024 < this.photosSizeLimit;

                if (!isInCorrectFormat) {
                    this.$message.error('Only files with jpg/png formats are allowed! ');
                }

                if (!doesNotExceedFileSize) {
                    this.$message.error(`Uplaoded photo size can not exceed ${this.photosSizeLimit}mb!`);
                }

                return isInCorrectFormat && doesNotExceedFileSize;
            },

            exceededFileCount(files, fileList) {
                this.$message.error(`The limit is ${this.photosNumberLimit}, you selected ${files.length} files this time. You may add up to ${this.photosNumberLimit - fileList.length} more files for this post. `);
            },

            failedPhotoUpload(err, file, fileList) {
                this.$message.error(err.message);
                this.photos = fileList;
            },

            removePhoto(file, fileList) {
                this.photos = fileList;
            },

            photoPreview(file) {
                this.previewPhotoImage = file.url;
                this.previewPhotoFileName = file.name;
                this.previewPhotoModal = true;
            },

            successfulPhotoUpload(response, file, fileList) {
                file.id = response;
                this.photos.push(file);
            },

            /**
             * Closes the submit modal.
             *
             * @return void
             */
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
                if (this.$route.query.channel) {
                    this.selectedCat = this.$route.query.channel
                }
            },

            /**
             * Sets the default value for suggestCats (uses user's already subscriber channels)
             *
             * @return void
             */
            setDefaultCategories() {
                let array = [];

                Store.subscribedCategories.forEach(function (element, index) {
                    array.push(element.name)
                });

                this.suggestedCats = array;
            },

//            dropzone() {
//                var that = this
//                Dropzone.options.addPhotosForm = {
//                    paramName: 'photo',
//                    maxFileSize: 10,
//                    // addRemoveLinks: true,
//                    acceptedFiles: '.jpg, .jpeg, .png, .gif',
//                    success: function (file, data) {
//                        that.photos.push(data)
//                    }
//                }
//            },

            gifSelected(e) {
                this.gifUploadFormData = new FormData();

                this.gifUploadFormData.append('gif', e.target.files[0]);
            },

            submit() {
                this.loading = true;

                if (this.submissionType === 'gif') {
                    this.gifUploadFormData.append('title', this.title);
                    this.gifUploadFormData.append('name', this.selectedCat);
                    this.gifUploadFormData.append('type', this.submissionType);

                    axios.post('/submission', this.gifUploadFormData).then((response) => {
                        // success
                        this.errors = []

                        Store.submissionUpVotes.push(response.data.id)

                        this.$router.push('/c/' + this.selectedCat + '/' + response.data.slug)

                        this.loading = false
                    }).catch((error) => {
                        // error
                        if (error.response.status == 500) {
                            this.customError = error.response.data
                            this.errors = []
                            this.loading = false
                            return
                        }

                        this.errors = error.response.data.errors
                        this.loading = false
                    });

                    return;
                }


                // rest of the types
                axios.post('/submission', {
                    title: this.title,
                    url: this.submitURL,
                    text: this.text,
                    name: this.selectedCat,
                    type: this.submissionType,
                    photos: _.map(this.photos, 'id'),
                }).then((response) => {
                    // success
                    this.errors = []

                    Store.submissionUpVotes.push(response.data.id)

                    this.$router.push('/c/' + this.selectedCat + '/' + response.data.slug)

                    this.loading = false
                }).catch((error) => {
                    // error
                    if (error.response.status == 500) {
                        this.customError = error.response.data
                        this.errors = []
                        this.loading = false
                        return
                    }

                    this.errors = error.response.data.errors
                    this.loading = false
                });
            },

            getTitle(typed) {
                if (!typed) return

                this.loadingTitle = true

                axios.get('/fetch-url-title', {
                    params: {
                        url: typed
                    }
                }).then((response) => {
                    this.title = response.data;
                    this.loadingTitle = false
                    this.errors.url = []
                }).catch((error) => {
                    if (error.response.status == 500) {
                        this.customError = error.response.data
                        this.errors = []
                        this.loadingTitle = false
                        return
                    }

                    this.errors = error.response.data.errors
                    this.loadingTitle = false
                });
            },

            getSuggestedCats: _.debounce(function (typed) {
                if (!typed) return;

                this.loadingCategories = true;

                axios.get('/get-categories', {
                    params: {
                        name: typed
                    }
                }).then((response) => {
                    this.suggestedCats = response.data;
                    this.loadingCategories = false;
                }).catch(() => {
                    this.loadingCategories = false;
                });
            }, 600),

            /**
             * Switch the type.
             *
             * @param string newType
             */
            changeSubmissionType(newType) {
                this.submissionType = newType
            }
        },
    }
</script>
