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

                    <!-- Radio Buttons -->
                    <div class="margin-bottom-1">
                        <div class="radio" v-for="item in subjects">
                            <label><input type="radio" name="subject" :value="item" v-model="subject">{{ item }}</label>
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
                <el-button type="success" size="medium"
                           @click="send"
                           :loading="sending"
                >
                    Submit
                </el-button>

                <el-button type="text" @click="close" size="medium" class="margin-right-1">
                    Cancel
                </el-button>
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
            subjects: [
                "It's spam",
                "It doesn't follow channel's exclusive rules",
                "It doesn't follow Voten's general rules",
                "It's abusive or harmful",
                "Other"
            ]
        }
    },

	mounted: function () {
		this.$nextTick(function () {
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
