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

						<input class="v-button"
						       type="file"
						       @change="uploadAvatar" />
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
					<el-alert v-for="e in avatar.errors.channel_name"
					          :title="e"
					          type="error"
					          :key="e"></el-alert>
				</div>

				<div class="edit-avatar-preview">
					<img :alt="Store.page.channel.temp.name"
					     :src="Store.page.channel.temp.avatar"
					     class="circle" />
				</div>
			</div>
		</div>

		<h3 class="dotted-title">
			<span>
				Settings
			</span>
		</h3>

		<el-form label-position="top"
		         label-width="10px">
			<el-form-item label="Description">
				<el-input type="textarea"
				          :placeholder="'How would you describe #' + Store.page.channel.temp.name + '?'"
				          name="description"
				          :autosize="{ minRows: 4, maxRows: 10}"
				          :maxlength="230"
				          v-model="description">
				</el-input>

				<el-alert v-for="e in errors.description"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Cover Color">
				<el-select v-model="cover_color"
				           placeholder="Cover Color..."
				           filterable>
					<el-option v-for="item in colors"
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
				<el-button round type="success"
				           size="medium"
				           @click="save"
				           :loading="sending">Save</el-button>
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
            errors: [],
            sending: false,
            description: Store.page.channel.temp.description,
            nsfw: Store.page.channel.temp.nsfw,
            cover_color: Store.page.channel.temp.cover_color,
            colors: [
                'Blue',
                'Dark Blue',
                'Red',
                'Dark',
                'Dark Green',
                'Bright Green',
                'Purple',
                'Orange',
                'Pink'
            ],
            avatar: {
                fileUploadFormData: new FormData(),
                uploading: false,
                errors: []
            }
        };
    },

    watch: {
        'Store.page.channel.temp': function() {
            this.description = Store.page.channel.temp.description;
            this.nsfw = Store.page.channel.temp.nsfw;
            this.cover_color = Store.page.channel.temp.cover_color;
        }
    },

    computed: {
        changed() {
            if (
                Store.page.channel.temp.cover_color != this.cover_color ||
                Store.page.channel.temp.nsfw != this.nsfw ||
                Store.page.channel.temp.description != this.description
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
            this.avatar.fileUploadFormData.append(
                'channel_name',
                Store.page.channel.temp.name
            );

            axios
                .post(`/channels/${Store.page.channel.temp.id}/avatar`, this.avatar.fileUploadFormData)
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
                .patch(`/channels/${Store.page.channel.temp.id}`, {
                    description: this.description,
                    nsfw: this.nsfw,
                    cover_color: this.cover_color
                })
                .then(() => {
                    this.errors = [];

                    Store.page.channel.temp.nsfw = this.nsfw;
                    Store.page.channel.temp.cover_color = this.cover_color;
                    Store.page.channel.temp.description = this.description;
                    this.sending = false;
                })
                .catch((error) => {
                    this.errors = error.response.data.errors;
                    this.sending = false;
                });
        }
    }
};
</script>
