<template>
    <section class="container margin-top-5 col-7 user-select" id="feedback-page">
        <div class="flex1" v-show="! messageSent">
            <h1 class="align-center">
                Send a feedback
            </h1>

            <p>
                Please pick a subject for your feedback and tell us few words about it. We review all feedbacks seriously; however, we may not write back to all of them.
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
                    <textarea name="name" class="form-control v-input-big" rows="5" id="feedback"
                              placeholder="Desciption..."
                              v-model="description"
                    ></textarea>
            </div>

            <button type="button" class="v-button v-button--green" :disabled="!description"
                    @click="send" >
                Send Feedback
            </button>
        </div>

        <div class="padding-top-bottom-3 sent align-center" v-show="messageSent">
            <i class="v-icon v-success go-green" aria-hidden="true"
               :class="{ 'rubberBand animated': messageSent }"
            ></i>

            <p>
                Thanks for helping us create something amazing!
            </p>

            <router-link class="v-button" to="/">
                Go Home
            </router-link>

            <button class="v-button" @click="reset">
                Send Another Feedback
            </button>
        </div>
    </section>
</template>

	<script>
	export default {
	    data () {
	        return {
	            subject: 'Report a bug',
	            description: '',
	            messageSent: false,
	        }
	    },

		mounted: function () {
			this.$nextTick(function () {
		    	this.$root.loadCheckBox();
				this.$root.autoResize();
		    	document.getElementById('feedback').focus();
			})
		},

	    methods: {
		    reset() {
                this.subject = 'Report a bug';
                this.description = '';
                this.messageSent = false;

                this.$root.loadCheckBox();
                this.$root.autoResize();
                document.getElementById('feedback').focus();
            },

	        send: function(){
	            this.messageSent = true;

	            axios.post( '/feedback', {
	                subject: this.subject,
	                description: this.description
	            }).catch((error) => {
                    this.messageSent = false;
                });
	        },
	    },
	}
</script>

<style>
    #feedback-page .sent i {
        display: block;
        font-size: 7em;
        margin-bottom: 15px;
    }

    #feedback-page .sent p {
        font-size: 25px;
    }

    #feedback-page .ui.checkbox label:before {
        background: #f3f6f5;
        border: 2px solid #d7d7d7;
    }
</style>