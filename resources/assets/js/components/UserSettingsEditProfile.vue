<template>
    <section>
        <h3 class="dotted-title">
            <span>
                Avatar
            </span>
        </h3>

        <el-alert
                v-if="customError"
                :title="customError"
                type="error"
        ></el-alert>

        <div class="form-group">
            <div class="flex-space">
                <div>
                    <el-button class="el-button v-button--upload" type="button">
                        <i class="v-icon v-upload" aria-hidden="true"></i> Click To Browse 

                        <input class="v-button" type="file" @change="passToCropModal"/>
                    </el-button>

                    <p class="go-gray go-small">
                        You can upload any size image file. After uploading is done, you'll get to position and size your image. 
                    </p>
                </div>

                <div class="edit-avatar-preview">
                    <img :alt="auth.username" :src="auth.avatar" class="circle"/>
                </div>
            </div>
        </div>

        <h3 class="dotted-title">
            <span>
                Public Profile
            </span>
        </h3>

        <el-form label-position="top" label-width="10px" :model="form">
            <el-form-item label="Cover Color">
                <el-select v-model="form.color" placeholder="Cover Color..." filterable>
                    <el-option
                            v-for="item in coverColors"
                            :key="item"
                            :label="item"
                            :value="item">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="Full Name">
                <el-input placeholder="Your full name..." v-model="form.name"></el-input>
                <el-alert v-for="e in errors.name" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <el-form-item label="Bio">
                <el-input
                        placeholder="How would you describe you?" v-model="form.bio" type="textarea"
                        :autosize="{ minRows: 4, maxRows: 10}"
                ></el-input>
                <el-alert v-for="e in errors.bio" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <el-form-item label="Website">
                <el-input placeholder="Website..." v-model="form.website" type="url"></el-input>
                <el-alert v-for="e in errors.website" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <el-form-item label="Location">
                <el-input placeholder="Location..." v-model="form.location"></el-input>
                <el-alert v-for="e in errors.location" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <el-form-item label="Twitter Username">
                <el-input placeholder="Twitter Username..." v-model="form.twitter">
                    <template slot="prepend">Https://twitter.com/</template>
                </el-input>

                <el-alert v-for="e in errors.twitter" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <!-- submit -->
            <el-form-item v-if="changed">
                <el-button type="success" @click="save" :loading="sending" size="medium">Save</el-button>
            </el-form-item>
        </el-form>

        <crop-modal :visible.sync="showImageCropModal" v-if="showImageCropModal" :type="'user'"></crop-modal>
    </section>
</template>

<script>
    import CropModal from '../components/CropModal.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        mixins: [Helpers],

        components: { CropModal },

        data() {
            return {
                showImageCropModal: false,
                sending: false,
                errors: [],
                customError: '',
                auth,
                Store,
                form: {
                    name: auth.name,
                    bio: auth.bio,
                    website: auth.info.website,
                    color: auth.color,
                    location: auth.location,
                    twitter: auth.info.twitter,
                },

                coverColors: [
                    'Blue', 'Dark Blue', 'Red', 'Dark', 'Dark Green', 'Bright Green', 'Purple', 'Pink', 'Orange'
                ],
                fileUploadFormData: new FormData(),
            }
        },

        created () {
            document.title = 'My Profile | Settings';
        },

        computed: {
            changed () {
                if (
                    auth.name != this.form.name ||
                    auth.bio != this.form.bio ||
                    auth.info.website != this.form.website ||
                    auth.location != this.form.location ||
                    auth.color != this.form.color ||
                    auth.info.twitter != this.form.twitter
                ) {
                    return true;
                }

                return false;
            },
        },

        methods: {
            /**
             * Passes the photo to the cropModal to take care of the rest
             *
             * @return void
             */
            passToCropModal (e) {
                this.fileUploadFormData.append('photo', e.target.files[0]);

                axios.post('/upload-temp-avatar', this.fileUploadFormData)
                     .then((response) => {
                         this.$eventHub.$emit('crop-photo-uploaded', response.data);
                     });

                this.showImageCropModal = true;
            },

            /**
             * Stores the changes in the database. (using the recently changed values)
             *
             * @return void
             */
            save() {
                this.sending = true;

                axios.post('/update-profile', {
                    name: this.form.name,
                    bio: this.form.bio,
                    website: this.form.website,
                    location: this.form.location,
                    color: this.form.color,
                    twitter: this.form.twitter,
                }).then(() => {
                    this.errors = [];
                    this.customError = '';

                    auth.name = this.form.name
                    auth.bio = this.form.bio
                    auth.location = this.form.location
                    auth.color = this.form.color
                    auth.info.website = this.form.website
                    auth.info.twitter = this.form.twitter

                    this.sending = false;
                }).catch((error) => {
                    if (error.response.status == 500) {
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
