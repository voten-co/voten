<template>
    <div class="v-modal-small">
        <div class="wrapper" v-on-clickaway="close">
            <header class="user-select">
                <h3>
                    Report Comment
                </h3>

                <div class="close" @click="close">
                    <i class="v-icon v-cancel-small"></i>
                </div>
            </header>

            <div class="middle">
                <div class="flex1">
                    <p>
                        Please help us understand the problem. What is wrong with this comment?
                    </p>

                    <div class="form-group grouped fields">
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="radio" value="It's spam" tabindex="0" class="hidden" v-model="subject">
                                <label>It's spam</label>
                            </div>
                        </div>

                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="radio" value="It doesn't follow channel's exclusive rules" tabindex="0" class="hidden" v-model="subject">
                                <label>It doesn't follow #{{category}}'s exclusive rules</label>
                            </div>
                        </div>

                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="radio" value="It doesn't follow Voten's general rules" tabindex="0" class="hidden" v-model="subject">
                                <label>It doesn't follow Voten's general rules</label>
                            </div>
                        </div>

                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="radio" value="It's abusive or harmful" tabindex="0" class="hidden" v-model="subject">
                                <label>It's abusive or harmful</label>
                            </div>
                        </div>

                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="radio" value="Other" tabindex="0" class="hidden" v-model="subject">
                                <label>Other</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <textarea name="name" class="form-control" rows="2" id="report-comment"
                                  placeholder="(optional) Additional desciption..."
                                  v-model="description"
                        ></textarea>
                    </div>
                </div>
            </div>

            <footer>
                <button type="button" class="v-button v-button--green"
                        @click="send"
                        :disabled="sending"
                        v-text="sending ? 'Submitting...' : 'Submit'"
                ></button>

                <button type="button" class="v-button v-button--red" @click="close">
                    Cancel
                </button>
            </footer>
        </div>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway';

export default {
    props: ['comment', 'category'],

    mixins: [ clickaway ],

    data () {
        return {
            subject: "It's spam",
            description: "",
            sending: false,
        }
    },

	mounted: function () {
		this.$nextTick(function () {
    		$('.ui.checkbox').checkbox();
	    	document.getElementById('report-comment').focus();
	    	this.$root.autoResize();
		})
	},

    methods: {
        send() {
            this.sending = true;

            axios.post( '/report-comment', {
                comment_id: this.comment,
                subject: this.subject,
                description: this.description
            }).then((response) => {
                this.close();
            });
        },

    	close () {
    		this.$eventHub.$emit('close')
    	},
    },
}
</script>
