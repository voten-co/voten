<template>
    <section class="no-border" :class="isReply ? '' : 'full-comment-form'">
        <div class="content">
        	<div class="ui reply form flex-display">
                <textarea type="text" v-model="message" id="comment-form" class="v-comment-form" placeholder="Type your comment..." autocomplete="off" rows="1" v-on:keydown.enter="submit($event)" v-focus="focused" @focus="focused = true"></textarea>

                <span class="send-button comment-emoji-button">
                    <i class="v-icon v-smile h-yellow" aria-hidden="true" v-if="!loading" @click="toggleEmojiPicker"></i>

                    <emoji-picker v-if="emojiPicker" @emoji="emoji" v-on-clickaway="closeEmojiPicker"></emoji-picker>
                </span>

                <button class="send-button" v-bind:class="{ 'go-green': showSubmit }" @click="submit($event)">
                    <i class="v-icon v-send" aria-hidden="true" v-if="!loading"></i>
		        	<moon-loader :loading="loading" :size="'25px'" :color="'#555'"></moon-loader>
                </button>
            </div>

            <div class="flex-space user-select" v-if="!isReply">
	            <a class="comment-form-guide" @click="$eventHub.$emit('markdown-guide')">
	            	Formatting Guide
	            </a>

                <a></a>
            </div>
        </div>
    </section>
</template>

<script>
	import MoonLoader from '../components/MoonLoader.vue';
	import EmojiPicker from '../components/EmojiPicker.vue';
    import { mixin as clickaway } from 'vue-clickaway';
	import { focus } from 'vue-focus';
	import Helpers from '../mixins/Helpers';

    export default {

		directives: { focus },

    	components: { MoonLoader, EmojiPicker },

        props: ['parent', 'submission', 'editing', 'before', 'id'],

        mixins: [clickaway, Helpers],

        data: function () {
            return {
            	Store,
                focused: null,
                emojiPicker: false,
            	loading: false,
                message: '',
                temp: '',
            }
        },

        created() {
            this.setFocused()
            this.setEditing()
        },

        computed: {
        	isReply() {
        		return this.parent != 0
        	},

        	showSubmit () {
        		return this.loading == false && this.message.trim()
        	}
        },

		mounted: function () {
			this.$nextTick(function () {
        		this.$root.autoResize()
			})
		},

        methods: {
            setEditing() {
                if(this.editing) {
                    this.message = this.before
                }
            },

        	emoji(shortname){
        		this.message = this.message + shortname + " "
        	},

            toggleEmojiPicker() {
                this.emojiPicker = ! this.emojiPicker;
            },

            closeEmojiPicker() {
                this.emojiPicker = false
            },

            setFocused(){
                if(this.parent == 0){
                    this.focused = false
                    return
                }

                this.focused = true
            },


        	submit(event) {
        		if(event.shiftKey) return

        		event.preventDefault()

        		if(!this.message.trim()) return

                this.closeEmojiPicker()

            	if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

        		this.temp = this.message
        		this.message = ''

        		$('#comment-form').css('height', 49)

        		this.loading = true

                // edit
                if (this.editing) {
                    axios.post('/edit-comment', {
                        comment_id: this.id,
                        body: this.temp
                    }).then((response) => {
                        this.loading = false

                        this.$emit('patched-comment', this.temp)
                    }).catch((error) => {
                        this.loading = false
                    });

                    return;
                }

                // new comment
        		axios.post( '/comment', {
                    parent_id: this.parent,
                    submission_id: this.submission,
                    body: this.temp,
                } ).then((response) => {
                	Store.commentUpVotes.push(response.data.id)

                    /**
		             * Fire an event to catch by the commenter himself
		             * (use ajax response instead of pusher for commenter himself)
		             */
                    this.$eventHub.$emit('newComment', response.data)

        			this.loading = false
                }).catch((error) => {
                    this.loading = false
                });
        	},
        },

    }
</script>
