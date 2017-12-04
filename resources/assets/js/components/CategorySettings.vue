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
                    <img :alt="Store.page.category.temp.name" :src="Store.page.category.temp.avatar" class="circle"/>
                </div>
            </div>
        </div>


        <h3 class="dotted-title">
			<span>
				Settings
			</span>
        </h3>

        <el-form label-position="top" label-width="10px">
            <el-form-item label="Description">
                <el-input
                        type="textarea"
                        :placeholder="'How would you describe #' + Store.page.category.temp.name + '?'"
                        name="description"
                        :autosize="{ minRows: 4, maxRows: 10}"
                        :maxlength="230"
                        v-model="description">
                </el-input>

                <el-alert v-for="e in errors.description" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <el-form-item label="Cover Color">
                <el-select v-model="color" placeholder="Cover Color..." filterable>
                    <el-option
                            v-for="item in colors"
                            :key="item"
                            :label="item"
                            :value="item">
                    </el-option>
                </el-select>
            </el-form-item>

            <div class="form-toggle no-border">
                This channel contains mostly NSFW content:
                <el-switch v-model="nsfw"></el-switch>
            </div>

            <el-form-item v-if="changed">
                <el-button type="success" size="medium" @click="save" :loading="sending">Save</el-button>
            </el-form-item>
        </el-form>

        <crop-modal :visible.sync="showImageCropModal" v-if="showImageCropModal" :type="'category'"></crop-modal>
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
                errors: [],
                customError: '',
                sending: false,
                description: Store.page.category.temp.description,
                nsfw: Store.page.category.temp.nsfw,
                color: Store.page.category.temp.color,
                colors: [
                    'Blue', 'Dark Blue', 'Red', 'Dark', 'Dark Green', 'Bright Green', 'Purple', 'Orange', 'Pink'
                ],
                fileUploadFormData: new FormData(),
            }
        },

        watch: {
            'Store.page.category.temp': function () {
                this.description = Store.page.category.temp.description
                this.nsfw = Store.page.category.temp.nsfw
                this.color = Store.page.category.temp.color
            }
        },

        computed: {
            changed () {
                if (
                    Store.page.category.temp.color != this.color ||
                    Store.page.category.temp.nsfw != this.nsfw ||
                    Store.page.category.temp.description != this.description
                ) {
                    return true
                }

                return false
            },
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

                this.showImageCropModal = true;
            },

            save () {
                this.sending = true

                axios.post('/category-patch', {
                    name: Store.page.category.temp.name,
                    description: this.description,
                    nsfw: this.nsfw,
                    color: this.color
                }).then(() => {
                    this.errors = []
                    this.customError = ''

                    Store.page.category.temp.nsfw = this.nsfw
                    Store.page.category.temp.color = this.color
                    Store.page.category.temp.description = this.description
                    this.sending = false
                }).catch((error) => {
                    if (error.response.status == 500) {
                        this.sending = false
                        this.customError = error.response.data
                        this.errors = []
                        return
                    }

                    this.errors = error.response.data.errors;
                    this.sending = false
                });
            },
        },


        beforeRouteEnter(to, from, next){
            if (Store.page.category.temp.name == to.params.name) {
                // loaded
                if (Store.state.administratorAt.indexOf(Store.page.category.temp.id) != -1) {
                    next()
                }
            } else {
                // not loaded but let's continue (the server-side is still protecting us!)
                next()
            }
        },
    };
</script>
