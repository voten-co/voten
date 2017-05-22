<template>
    <div class="v-modal-small user-select" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box" v-on-clickaway="close">
            <div class="flex1" v-if="! messageSent">
                <p>
                    Please choose the subject of your feedback and say a few words about it:
                </p>

                <div class="form-group grouped fields">
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" value="Report a bug" tabindex="0" class="hidden" v-model="subject">
                            <label>Report a bug</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" value="Thumbs-up about a feature" tabindex="0" class="hidden" v-model="subject">
                            <label>Thumbs-up about a feature</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" value="Suggestion" tabindex="0" class="hidden" v-model="subject">
                            <label>Suggestion</label>
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
                    <textarea name="name" class="form-control" rows="2" id="feedback"
                        placeholder="Desciption..."
                        v-model="description"
                    ></textarea>
                </div>

                <button type="button" class="v-button v-button--green" :disabled="!description"
                @click="send" >
                    Send Feedback
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
                    Thank you for helping us creating something amazing!
                </p>
            </div>
        </div>
    </div>
</template>

	<script>
    import { mixin as clickaway } from 'vue-clickaway';

	export default {
		props: ['sidebar'],

        mixins: [ clickaway ],

	    data () {
	        return {
	            subject: 'Report a bug',
	            description: '',
	            messageSent: false,
	        }
	    },

		mounted: function () {
			this.$nextTick(function () {
		    	this.$root.loadCheckBox()
				this.$root.autoResize()
		    	document.getElementById('feedback').focus()
			})
		},

	    methods: {
	        send: function(){
	            this.messageSent = true

	            axios.post( '/feedback', {
	                subject: this.subject,
	                description: this.description
	            })

	            setTimeout(function () { this.close() }.bind(this), 1500)
	        },

	    	close () {
	    		this.$eventHub.$emit('close')
	    	},
	    },
	}
</script>
