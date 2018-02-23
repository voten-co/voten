<template>
	<el-dialog title="Give Feedback"
	           :visible="visible"
	           :width="isMobile ? '99%' : '600px'"
	           @close="close"
	           append-to-body
	           class="user-select">
		<section class="user-select"
		         id="feedback-modal">
			<el-form label-position="top"
			         label-width="10px">
				<p>
					Please pick a subject for your feedback and tell us a few words about it. We review all feedbacks; however, we may not write back to all of them.
				</p>

				<el-form-item label="Subject">
					<el-select v-model="subject"
					           placeholder="Subject">
						<el-option v-for="item in subjects"
						           :key="item"
						           :label="item"
						           :value="item">
						</el-option>
					</el-select>
				</el-form-item>

				<el-form-item label="Desciption">
					<el-input type="textarea"
					          v-model="description"
					          placeholder="Desciption..."
					          name="description"
					          :maxlength="2000"
					          ref="description"
					          :autosize="{ minRows: 4, maxRows: 10}"></el-input>
				</el-form-item>
			</el-form>
		</section>

		<!-- submit -->
		<span slot="footer"
		      class="dialog-footer">
			<el-button type="text"
			           @click="close"
			           size="medium"
			           class="margin-right-1">
				Cancel
			</el-button>

			<el-button round type="success"
			           @click="send"
			           :loading="sending"
			           :disabled="!description.trim()"
			           size="medium">
				Send Feedback
			</el-button>
		</span>
	</el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    props: ['visible'],

    data() {
        return {
            sending: false,
            subject: 'Report a bug',
            description: '',
            subjects: [
                'Report a bug',
                'Thumbs-up about a feature',
                'Suggestion',
                'Other'
            ]
        };
    },

    mounted() {
        this.$nextTick(function() {
            this.$refs.description.$refs.textarea.focus();
        });
    },

    beforeDestroy() {
        if (window.location.hash == '#feedback') {
            history.go(-1);
        }
    },

    created() {
        window.location.hash = 'feedback';
    },

    methods: {
        close() {
            this.$emit('update:visible', false);
        },

        send() {
            this.sending = true;

            axios
                .post('/feedbacks', {
                    subject: this.subject,
                    description: this.description
                })
                .then(() => {
                    this.sending = false;

                    this.$message({
                        message: 'Feedback recieved. Thanks for caring!',
                        type: 'success'
                    });

                    this.close();
                })
                .catch((error) => {
                    this.sending = false;
                });
        }
    }
};
</script>
