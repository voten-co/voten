<template>
    <div class="fixed-comment-form-wrapper" id="comment-form">
        <div v-if="preview && message" class="form-wrapper margin-bottom-1 preview">
            <markdown :text="message"></markdown>
        </div>

        <form class="chat-input-form">
            <textarea 
                rows="1" v-on:keydown.enter="submit($event)" :disabled="loading"
                v-model="message" name="comment"
                @keydown="whisperTyping" @keyup="whisperFinishedTyping" :id="'comment-form-' + parent"
                autocomplete="off"
                :placeholder="loading ? 'Submitting...' : 'Type your comment...'"
            ></textarea>

            <span class="send-button comment-emoji-button" v-show="!loading">
                <div @click="toggleEmojiPicker" class="flex-center">
                    <emoji-icon width="38" height="38"></emoji-icon>
                </div>
                

                <emoji-picker v-if="emojiPicker" @emoji="emoji" v-on-clickaway="closeEmojiPicker"></emoji-picker>
            </span>

            <button type="submit" :class="{ 'go-green': message.trim() }" 
                @click="submit($event)" v-tooltip.left="{ content: 'Submit'}"
            >
                <i class="v-icon v-send" aria-hidden="true" v-show="!loading"></i>
                <moon-loader :loading="loading" :size="'25px'" :color="'#555'"></moon-loader>
            </button>
        </form>

        <div class="flex-space user-select comment-form-guide-wrapper">
            <typing></typing>

            <div>
                <button class="comment-form-guide" @click="preview =! preview" type="button" v-show="message">
                    Preview
                </button>

                <button class="comment-form-guide" @click="$eventHub.$emit('markdown-guide')" type="button">
                    Formatting Guide
                </button>
            </div>
        </div>
    </div>	
</template>

<script>
    import Markdown from '../components/Markdown.vue'; 
	import MoonLoader from '../components/MoonLoader.vue';
    import EmojiPicker from '../components/EmojiPicker.vue';
    import EmojiIcon from '../components/Icons/EmojiIcon.vue';
	import Typing from '../components/Typing.vue';
    import { mixin as clickaway } from 'vue-clickaway';
	import { focus } from 'vue-focus';
	import Helpers from '../mixins/Helpers';
	import 'jquery.caret';
	import 'at.js';

    export default {

		directives: { focus },

    	components: {
		    MoonLoader,
            EmojiPicker,
            EmojiIcon, 
            Markdown, 
            Typing
        },

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
                mentioning: false,
                EchoChannelAddress: 'submission.' + this.$route.params.slug,
                isTyping: false, 
                preview : false 
            }
        },

        created() {
            this.setFocused();
            this.setEditing();
            this.subscribeToEcho();
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
            this.atWho();

			this.$nextTick(function () {
        		this.$root.autoResize();
			});
		},

        methods: {
		    /**
             * Subscribes to the Echo channel. Prepares comment form for whispering "typing".
             *
             * @return void
             */
		    subscribeToEcho() {
                if (this.isGuest) return;

                Echo.private(this.EchoChannelAddress);
            },

		    /**
             * Broadcast "typing".
             *
             * @return void
             */
            whisperTyping() {
                if (this.isGuest) return;

                if (this.isTyping) return;

                if (this.editing) return;

                Echo.private(this.EchoChannelAddress).whisper('typing', {
                    username: auth.username
                });

                this.isTyping = true;
            },

            /**
             * Broadcast "finished-typing".
             *
             * @return void
             */
            whisperFinishedTyping: _.debounce(function () {
                if (this.isGuest) return;

                Echo.private(this.EchoChannelAddress).whisper('finished-typing', {
                    username: auth.username
                });

                this.isTyping = false;
            }, 600),

            /**
             * Loads the at.js stuff
             *
             * @return void
             */
            atWho() {
                $('#comment-form-' + this.parent).atwho({
                    at: "@",
                    delay: 750,
                    searchKey: "username",
                    insertTpl: "@${username}",
                    displayTpl: "<li><img src='${avatar}' height='20' width='20' />@${username}<small data-name='${name}'>${name}</small></li>",
                    callbacks: {
                        remoteFilter: function (query, callback) {
                            axios.get('/search-mentionables', {
                                params: {
                                    searched: query
                                }
                            }).then((response) => {
                                callback(response.data);
                            });
                        }
                    }
                });
            },

            setEditing() {
                if (this.editing) {
                    this.message = this.before;
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
                if(this.parent == 0) {
                    this.focused = false;
                    return;
                }

                this.focused = true;
            },


        	submit(event) {
                // ignore shift + enter
        		if(event.shiftKey) return;

        		// ignore if the mention suggestion box is open
                if ($("#atwho-ground-comment-form-" + this.parent + " .atwho-view").is(':visible')) return;

        		event.preventDefault();

        		if(!this.message.trim()) return;

                this.closeEmojiPicker();

            	if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

        		this.temp = this.message;
                this.focused = false;
        		this.message = '';

        		$('#comment-form-' + this.parent).css('height', 43);

        		this.loading = true;

                // edit
                if (this.editing) {
        		    if (this.temp == this.before) {
                        this.message = this.temp;
        		        this.loading = false;
                        this.$emit('patched-comment', this.temp)
        		        return;
                    }

                    axios.post('/edit-comment', {
                        comment_id: this.id,
                        body: this.temp
                    }).then((response) => {
                        this.loading = false;

                        this.$emit('patched-comment', this.temp);
                    }).catch((error) => {
        		        this.message = this.temp;

        		        this.loading = false;
                    });

                    return;
                }

                // new comment
        		axios.post( '/comment', {
                    parent_id: this.parent,
                    submission_id: this.submission,
                    body: this.temp,
                } ).then((response) => {
                	Store.commentUpVotes.push(response.data.id);

                    this.$eventHub.$emit('newComment', response.data);

        			this.loading = false;
                }).catch((error) => {
                    this.message = this.temp;

                    this.loading = false;
                });
        	},
        },

    }
</script>


<style>
    [data-name="null"] {
        display: none;
    }
</style>