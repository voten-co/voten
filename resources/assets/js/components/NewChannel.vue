<template>
	<el-dialog title="New Channel"
	           :visible="visible"
	           :width="isMobile ? '99%' : '600px'"
	           @close="close"
	           append-to-body
	           class="user-select submit-form">
		<el-alert v-if="customError"
		          :title="customError"
		          class="margin-bottom-1"
		          type="error">
		</el-alert>

		<el-alert :title="warning"
		          type="warning">
		</el-alert>

		<el-form label-position="top"
		         label-width="10px">
			<el-form-item label="Name">
				<el-input placeholder="Name..."
				          name="name"
				          v-model="name"
				          autocorrect="off"
				          autocapitalize="off"
				          spellcheck="false"></el-input>

				<el-alert show-icon
				          :closable="false"
				          title="Names must be alpha-numeric, with no spaces. They're also not editable so make up your mind before continue! Examples: gaming, news, OldSchoolCool, modernWarfare2"
				          type="info">
				</el-alert>

				<el-alert v-for="e in errors.name"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Description">
				<el-input type="textarea"
				          placeholder="A few word to describe your channel..."
				          name="description"
				          :autosize="{ minRows: 4, maxRows: 10}"
				          v-model="description">
				</el-input>

				<el-alert show-icon
				          :closable="false"
				          :title="'The description field helps users find your channel. The first few words matter the most!'"
				          type="info">
				</el-alert>

				<el-alert v-for="e in errors.description"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<!-- NSFW Toggle -->
			<el-checkbox v-model="sfw">
				Safe for work
			</el-checkbox>
		</el-form>

		<span slot="footer"
		      class="dialog-footer">
			<el-button round type="success"
			           @click="submit"
			           :disabled="!validates"
			           :loading="loading"
			           size="medium">
				Create
			</el-button>
		</span>
	</el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    props: ['visible'],

    mixins: [Helpers],

    data() {
        return {
            name: '',
            description: '',
            sfw: true,
            errors: [],
            customError: '',
            loading: false,
            warning: `At the current stage, we're trying to keep the channels number short, making it easier for new users. Thus, please do a little search before creating your channel to make sure a similar one doesn't already exist. `
        };
    },

    watch: {
        visible() {
            if (this.visible) {
                window.location.hash = 'newChannel';
            } else {
                if (window.location.hash == '#newChannel') {
                    history.go(-1);
                }
            }
        }
    },

    computed: {
        validates() {
            return this.name.trim() && this.description.trim();
        }
    },

    methods: {
        close() {
            this.$emit('update:visible', false);
        },

        submit() {
            this.loading = true;

            axios
                .post('/channels', {
                    name: this.name,
                    description: this.description,
                    nsfw: !this.sfw
                })
                .then((response) => {
                    this.errors = [];

                    // let's add the categoriy_id to the user's moderatingAt and administratorAt
                    Store.state.moderatingAt.push(response.data.id);
                    Store.state.administratorAt.push(response.data.id);
                    Store.state.moderatingChannels.push(response.data);
                    Store.state.subscribedChannels.push(response.data);
                    Store.state.subscribedAt.push(response.data.id);
                    Store.page.channel.temp = response.data;

                    this.$router.push(
                        '/c/' + response.data.name + '/mod/settings?created=1'
                    );

                    this.loading = false;
                    this.reset();
                    this.close();
                })
                .catch((error) => {
                    this.errors = error.response.data.errors;

                    this.loading = false;
                });
        },

        reset() {
            this.name = '';
            this.description = '';
            this.sfw = true;
            this.errors = [];
            this.customError = '';
            this.loading = false;
        }
    }
};
</script>
