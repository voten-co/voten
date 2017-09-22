<template>
    <div class="v-modal-small user-select" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box" v-on-clickaway="close">
            <div class="flex1" v-if="! messageSent">
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

                <button type="button" class="v-button v-button--green"
                @click="send" >
                    Submit Report
                </button>

                <button type="button" class="v-button v-button--red"
                    data-toggle="tooltip" data-placement="bottom" title="Close (esc)"
                    @click="close">
                    Cancel
                </button>
            </div>

            <div class="padding-top-bottom-3 sent align-center" v-else>
                <i class="v-icon v-success" aria-hidden="true"
                    :class="{ 'rubberBand animated': messageSent }"
                ></i>

                <p>
                    Thank you for reporting.
                </p>
            </div>

        </div>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway';

export default {

    props: ['comment', 'category', 'sidebar'],

    mixins: [ clickaway ],

    data () {
        return {
            subject: "It's spam",
            description: "",
            messageSent: false,
        }
    },

	mounted: function () {
		this.$nextTick(function () {
    		$('.ui.checkbox').checkbox()
	    	document.getElementById('report-comment').focus()
	    	this.$root.autoResize()
		})
	},

    methods: {
        send: function(){
            this.messageSent = true;

            axios.post( '/report-comment', {
                comment_id: this.comment,
                subject: this.subject,
                description: this.description
            });

            setTimeout(function () { this.close() }.bind(this), 1500)
        },

    	/**
    	 * Fires the 'close' event which causes all the modals to be closed.
    	 *
    	 * @return void
    	 */
    	close () {
    		this.$eventHub.$emit('close')
    	},
    },
}
</script>
