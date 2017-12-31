<template>
    <div class="fixed-comment-form-wrapper" id="comment-form">
        <div v-if="preview && message" class="form-wrapper margin-bottom-1 preview">
            <markdown :text="message.trim()"></markdown>
        </div>

        <div class="editing-comment-wrapper user-select" v-if="(editing || replying) && !loading">
            <div class="close" @click="clear">
                <i class="v-icon v-cancel-small"></i>
            </div>
            
            <div class="editing-comment-previous">
                <h4 class="title">
                    {{ editing ? 'Edit Comment' : replyingComment.owner.username }}
                </h4>

                <div class="text" 
                    v-text="editing ? str_limit(editingComment.body, 60) : str_limit(replyingComment.body, 60)">
                </div>
            </div>
        </div>

        <form class="chat-input-form">
            <el-input
                    type="textarea"
                    autosize
                    :placeholder="loading ? 'Submitting...' : 'Type your comment...'"
                    v-model="message"
                    @keydown.native="whisperTyping" @keyup.native="whisperFinishedTyping" :id="'comment-form-' + parent"
                    @keydown.enter.native="submit($event)"
                    :disabled="loading"
                    name="comment"
                    :maxlength="5000"
                    ref="input"
            ></el-input>

            <span class="send-button comment-emoji-button" v-show="!loading">
                <div @click="toggleEmojiPicker" class="flex-center">
                    <emoji-icon width="38" height="38"></emoji-icon>
                </div>

                <emoji-picker v-if="emojiPicker" @emoji="emoji" v-on-clickaway="closeEmojiPicker"></emoji-picker>
            </span>

            <button type="submit" :class="{ 'go-green': message.trim() }" @click="submit($event)">
                <el-tooltip placement="bottom-end" transition="false" 
                    v-show="!loading"
                >
                    <div slot="content">
                        Press Enter to send<br/>
                        Press Shift+Enter to add a new paragraph 
                    </div>
                    <i class="v-icon v-send" aria-hidden="true"></i>
                </el-tooltip>

                <moon-loader :loading="loading" :size="'25px'" :color="'#555'"></moon-loader>
            </button>
        </form>

        <div class="flex-space user-select comment-form-guide-wrapper">
            <typing></typing>
            
            <small class="go-red" v-if="error" v-text="error"></small>

            <div>
                <button class="comment-form-guide" @click="preview =! preview" type="button" v-show="message.trim()">
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
	import Helpers from '../mixins/Helpers';
	import 'jquery.caret';
	import 'at.js';

    export default {
    	components: {
		    MoonLoader,
            EmojiPicker,
            EmojiIcon, 
            Markdown, 
            Typing
        },

        props: ['submission', 'before'],

        mixins: [clickaway, Helpers],

        data() {
            return {
            	Store,
                emojiPicker: false,
            	loading: false,
                message: '',
                temp: '',
                mentioning: false,
                EchoChannelAddress: 'submission.' + this.$route.params.slug,
                isTyping: false, 
                preview : false, 
                editingComment: [], 
                replyingComment: [], 
                parent: 0,
                error: null 
            }
        },

        created() {
            this.subscribeToEcho();
            this.$eventHub.$on('edit-comment', this.setEditing);
            this.$eventHub.$on('reply-comment', this.setReplying);
            this.$eventHub.$on('pressed-esc', this.clear);
        },

        beforeDestroy() {
            this.$eventHub.$off('edit-comment', this.setEditing);
            this.$eventHub.$off('reply-comment', this.setReplying);
            this.$eventHub.$off('pressed-esc', this.clear);
        }, 

        computed: {
        	replying() {
        		return ! (_.isEmpty(this.replyingComment));
        	},

        	showSubmit() {
        		return this.loading == false && this.message.trim(); 
            }, 
            
            editing() {
                return ! (_.isEmpty(this.editingComment));
            }
        },

		mounted() {
            this.atWho();
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

            setEditing(comment) {
                this.clear(); 
                
                this.editingComment = comment; 
                this.message = this.editingComment.body; 
                this.parent = this.editingComment.parent_id; 

                this.$refs.input.focus();
            },

            setReplying(comment) {
                this.clear(); 

                this.replyingComment = comment; 
                this.parent = this.replyingComment.id; 

                this.$refs.input.focus();
            }, 

            /**
             * Like it never happened! 
             * 
             * @return void  
             */
            clear() {
                this.editingComment = []; 
                this.replyingComment = []; 
                this.message = '';
                this.loading = false; 
                this.preview = false; 
                this.parent = 0;
                this.error = null; 
            },

        	emoji(shortname) {
        		this.message = this.message + shortname + " "; 
        	},

            toggleEmojiPicker() {
                this.emojiPicker =! this.emojiPicker;
            },

            closeEmojiPicker() {
                this.emojiPicker = false; 
            },

        	submit(event) {
                // ignore shift + enter
        		if (event.shiftKey) return;

        		// ignore if the mention suggestion box is open
                if ($("#atwho-ground-comment-form-" + this.parent + " .atwho-view").is(':visible')) return;

        		event.preventDefault();

        		if (!this.message.trim()) return;

                this.closeEmojiPicker();

            	if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

        		this.temp = this.message;
        		this.message = '';

        		$('#comment-form-' + this.parent).css('height', 43);

        		this.loading = true;

                // edit
                if (this.editing) {
        		    if (this.temp == this.before) {
                        this.message = this.temp;
        		        this.loading = false;
                        this.$eventHub.$emit('patchedComment', this.editingComment);

        		        return;
                    }

                    axios.post('/edit-comment', {
                        comment_id: this.editingComment.id,
                        body: this.temp
                    }).then(() => {
                        this.editingComment.body = this.temp;
                        this.$eventHub.$emit('patchedComment', this.editingComment);
                        
                        this.clear();                        
                    }).catch(error => {
                        this.clear(); 
                        this.error = error.response.data; 
                    });

                    return;
                }

                // new comment
        		axios.post( '/comment', {
                    parent_id: this.parent,
                    submission_id: this.submission,
                    body: this.temp,
                }).then((response) => {
                	Store.state.comments.upVotes.push(response.data.id);
                    this.$eventHub.$emit('newComment', response.data);

        			this.clear();
                }).catch(error => {
                    this.clear(); 
                    this.error = error.response.data; 
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