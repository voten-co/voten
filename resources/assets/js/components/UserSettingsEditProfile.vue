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
					<div round class="el-button v-button--upload el-button--default is-plain is-round" plain>
						<i class="margin-right-half" :class="avatar.uploading ? 'el-icon-loading' : 'el-icon-upload'"></i>

						{{ avatar.uploading ? 'Uploading...' : 'Click To Browse'}}
 
						<input class="v-button" type="file" @change="uploadAvatar" />
					</div>

					<p class="go-gray go-small">
						The Uploaded photo must have a minimum of 
						<strong>250*250 pixels</strong> with a
						<strong>ratio of 1/1</strong> (such as a square or circle)
					</p>

					<el-alert v-for="e in avatar.errors.photo"
					          :title="e"
					          type="error"
					          :key="e"></el-alert>
				</div>

				<div class="edit-avatar-preview">
					<img :alt="auth.username"
					     :src="auth.avatar"
					     class="circle" />
				</div>
			</div>
		</div>

		<h3 class="dotted-title">
			<span>
				Public Profile
			</span>
		</h3>

		<el-form label-position="top"
		         label-width="10px"
		         :model="form">
			<el-form-item label="Cover Color">
				<el-select v-model="form.cover_color"
				           placeholder="Cover Color..."
				           filterable>
					<el-option v-for="item in coverColors"
					           :key="item"
					           :label="item"
					           :value="item">
					</el-option>
				</el-select>
			</el-form-item>

			<el-form-item label="Full Name">
				<el-input placeholder="Your full name..."
				          v-model="form.name"></el-input>
				<el-alert v-for="e in errors.name"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Bio">
				<el-input placeholder="How would you describe you?"
				          v-model="form.bio"
				          type="textarea"
				          :autosize="{ minRows: 4, maxRows: 10}"></el-input>
				<el-alert v-for="e in errors.bio"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Website">
				<el-input placeholder="Website..."
				          v-model="form.website"
				          type="url"></el-input>
				<el-alert v-for="e in errors.website"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Location">
				<el-input placeholder="Location..."
				          v-model="form.location"></el-input>
				<el-alert v-for="e in errors.location"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Twitter Username">
				<el-input placeholder="Twitter Username..."
				          v-model="form.twitter">
					<template slot="prepend">Https://twitter.com/</template>
				</el-input>

				<el-alert v-for="e in errors.twitter"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<!-- submit -->
			<el-form-item v-if="changed">
				<el-button round type="success"
				           @click="save"
				           :loading="sending"
				           size="medium">Save</el-button>
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

            form: {
                name: auth.name,
                bio: auth.bio,
                website: auth.info.website,
                cover_color: auth.cover_color,
                location: auth.info.location,
                twitter: auth.info.twitter
            },

            coverColors: [
                'Blue',
                'Dark Blue',
                'Red',
                'Dark',
                'Dark Green',
                'Bright Green',
                'Purple',
                'Pink',
                'Orange'
            ],

            avatar: {
                fileUploadFormData: new FormData(),
                uploading: false,
                errors: []
            }
        };
    },

    computed: {
        changed() {
            if (
                auth.name != this.form.name ||
                auth.bio != this.form.bio ||
                auth.info.website != this.form.website ||
                auth.info.location != this.form.location ||
                auth.cover_color != this.form.cover_color ||
                auth.info.twitter != this.form.twitter
            ) {
                return true;
            }

            return false;
        }
    },

    methods: {
        uploadAvatar(e) {
            this.avatar.uploading = true;
            this.avatar.errors = [];
            this.avatar.fileUploadFormData = new FormData();

            this.avatar.fileUploadFormData.append('photo', e.target.files[0]);

            axios
                .post('/auth/avatar', this.avatar.fileUploadFormData)
                .then((response) => {
                    location.reload();

                    this.avatar.uploading = false;
                })
                .catch((error) => {
                    this.avatar.errors = error.response.data.errors;
                    this.avatar.uploading = false;
                });
        },

        save() {
            this.sending = true;

            axios
                .patch('/users/profile', {
                    name: this.form.name,
                    bio: this.form.bio,
                    website: this.form.website,
                    location: this.form.location,
                    cover_color: this.form.cover_color,
                    twitter: this.form.twitter
                })
                .then(() => {
                    this.errors = [];

                    auth.name = this.form.name;
                    auth.bio = this.form.bio;
                    auth.info.location = this.form.location;
                    auth.cover_color = this.form.cover_color;
                    auth.info.website = this.form.website;
                    auth.info.twitter = this.form.twitter;

                    if (
                        typeof Store.page.user.temp.username != 'undefined' &&
                        Store.page.user.temp.id == auth.id
                    ) {
                        Store.page.user.temp.name = auth.name;
                        Store.page.user.temp.bio = auth.bio;
                        Store.page.user.temp.cover_color = auth.cover_color;
                        Store.page.user.temp.location = auth.info.location;
                        Store.page.user.temp.info.website = auth.info.website;
                        Store.page.user.temp.info.twitter = auth.info.twitter;
                    }

                    this.sending = false;
                })
                .catch((error) => {
                    this.sending = false;
                    this.errors = error.response.data.errors;
                });
        }
    }
};
</script>
