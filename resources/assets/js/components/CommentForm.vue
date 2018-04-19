
<template>
	<div class="fixed-comment-form-wrapper"
	     @keydown.down="handleKey($event, 'down')"
	     @keydown.up="handleKey($event, 'up')"
	     @keydown.enter="handleKey($event, 'enter')"
	     id="comment-form">
		<div class="editing-comment-wrapper user-select"
		     v-if="(editing || replying) && !loading">
             <el-tooltip content="Cancel (esc)"
             placement="right"
             transition="false"
             :open-delay="500"
             >
                <div class="close"
                    @click="clear">
                    <i class="v-icon v-cancel-small"></i>
                </div>
            </el-tooltip>
            

			<div class="editing-comment-previous">
				<h4 class="title">
					{{ editing ? 'Edit Comment' : replyingComment.author.username }}
				</h4>

				<div class="text"
				     v-text="editing ? str_limit(editingComment.content.text, 60) : str_limit(replyingComment.content.text, 60)">
				</div>
			</div>
		</div>

        <div v-if="preview && message"
             class="form-wrapper margin-bottom-1 preview">
            <markdown :text="message.trim()"></markdown>
        </div>

		<form class="chat-input-form relative">
			<transition name="el-zoom-in-bottom">
				<quick-emoji-picker v-if="quickEmojiPicker.show"
				                    @close="quickEmojiPicker.show = false"
				                    :message="message"
				                    textareaid="comment-form-textarea"
				                    :starter="quickEmojiPicker.starter"
				                    @pick="pick"></quick-emoji-picker>
			</transition>

			<transition name="el-zoom-in-bottom">
				<quick-mentioner v-if="quickMentioner.show"
				                 @close="quickMentioner.show = false"
				                 :message="message"
				                 textareaid="comment-form-textarea"
				                 @pick="pick"
				                 :suggestions="commentors"
				                 :starter="quickMentioner.starter"></quick-mentioner>
			</transition>

			<transition name="el-zoom-in-bottom">
				<quick-channel-picker v-if="quickChannelPicker.show"
				                      @close="quickChannelPicker.show = false"
				                      :message="message"
				                      textareaid="comment-form-textarea"
				                      @pick="pick"
				                      :starter="quickChannelPicker.starter"></quick-channel-picker>
			</transition>

			<el-input type="textarea"
			          autosize
			          :placeholder="loading ? 'Submitting...' : 'Type your comment...'"
			          v-model="message"
			          @keydown.native="whisperTyping"
			          @keyup.native="whisperFinishedTyping"
			          :id="'comment-form-textarea'"
			          @keydown.meta.enter.exact.native="submit($event)"
			          @keydown.ctrl.enter.exact.native="submit($event)"
			          :disabled="loading"
			          name="comment"
			          :maxlength="5000"
			          ref="input"
			          @input="typed">
			</el-input>

			<span class="send-button comment-emoji-button"
			      v-if="isDesktop && !loading">
				<div @mouseout="closeEmojiPicker"
				     @mouseover="openEmojiPicker"
				     class="flex-center">
					<emoji-icon width="38"
					            height="38"></emoji-icon>

					<transition name="el-zoom-in-bottom">
						<emoji-picker v-if="emojiPicker"
						              textareaid="comment-form-textarea"
						              @pick="pick"></emoji-picker>
					</transition>
				</div>
			</span>

			<button type="submit"
			        :class="{ 'go-green': message.trim() }"
			        @click="submit($event)">
				<el-tooltip placement="bottom-end"
				            transition="false"
				            v-show="!loading">
					<div slot="content">
						Press Command/Ctrl + Enter to send
					</div>
					<i class="v-icon v-send"
					   aria-hidden="true"></i>
				</el-tooltip>

				<moon-loader :loading="loading"
				             :size="'25px'"
				             :color="'#555'"></moon-loader>
			</button>
		</form>

		<div class="flex-space user-select comment-form-guide-wrapper">
			<typing></typing>

			<div>
				<button class="comment-form-guide"
				        @click="preview =! preview"
				        type="button"
				        v-show="message.trim()">
					Preview
				</button>

				<button class="comment-form-guide"
				        @click="Store.modals.markdownGuide.show = true"
				        type="button">
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
import QuickEmojiPicker from '../components/QuickEmojiPicker.vue';
import QuickChannelPicker from '../components/QuickChannelPicker.vue';
import QuickMentioner from '../components/QuickMentioner.vue';
import EmojiIcon from '../components/Icons/EmojiIcon.vue';
import Typing from '../components/Typing.vue';
import Helpers from '../mixins/Helpers';
import InputHelpers from '../mixins/InputHelpers';

export default {
    components: {
        QuickChannelPicker,
        QuickEmojiPicker,
        QuickMentioner,
        MoonLoader,
        EmojiPicker,
        EmojiIcon,
        Markdown,
        Typing
    },

    props: ['submission', 'before', 'commentors'],

    mixins: [Helpers, InputHelpers],

    data() {
        return {
            emojiPicker: false,
            loading: false,
            message: '',
            temp: '',
            mentioning: false,
            EchoChannelAddress: 'submission.' + this.$route.params.slug,
            isTyping: false,
            preview: false,

            quickMentioner: {
                show: false,
                starter: null
            },

            quickEmojiPicker: {
                show: false,
                starter: null
            },

            quickChannelPicker: {
                show: false,
                starter: null
            },

            editingComment: [],
            replyingComment: [],
            parent: 0
        };
    },

    created() {
        this.subscribeToEcho();
        this.$eventHub.$on('edit-comment', this.setEditing);
        this.$eventHub.$on('reply-comment', this.setReplying);
        this.$eventHub.$on('pressed-esc', this.handleEscapteKeyup);
    },

    beforeDestroy() {
        this.$eventHub.$off('edit-comment', this.setEditing);
        this.$eventHub.$off('reply-comment', this.setReplying);
        this.$eventHub.$off('pressed-esc', this.handleEscapteKeyup);
    },

    watch: {
        $route() {
            this.clear();
        }
    },

    computed: {
        replying() {
            return !_.isEmpty(this.replyingComment);
        },

        editing() {
            return !_.isEmpty(this.editingComment);
        },

        showSubmit() {
            return this.loading == false && this.message.trim();
        }
    },

    methods: {
        typed(string) {
            // close on empty input
            if (!string.trim()) {
                this.quickMentioner.show = false;
                this.quickEmojiPicker.show = false;
                this.quickChannelPicker.show = false;
                return;
            }

            // get the last typed character (but not the last character of the string)
            let lastStrIndex = this.lastTypedCharacter('comment-form-textarea');
            let lastStr = string[lastStrIndex];
            // let previousStr = string[lastStrIndex - 1];

            // close on space
            if (lastStr == ' ') {
                this.quickMentioner.show = false;
                this.quickEmojiPicker.show = false;
                this.quickChannelPicker.show = false;
                return;
            }

            // previous must be empty space to continue
            // if (previousStr != ' ' && string.length > 1) return;

            if (lastStr == '@') {
                this.quickMentioner.show = true;
                this.quickMentioner.starter = lastStrIndex;

                this.quickEmojiPicker.show = false;
                this.quickChannelPicker.show = false;
            } else if (lastStr == ':') {
                this.quickEmojiPicker.show = true;
                this.quickEmojiPicker.starter = lastStrIndex;

                this.quickMentioner.show = false;
                this.quickChannelPicker.show = false;
            } else if (lastStr == '#') {
                this.quickChannelPicker.show = true;
                this.quickChannelPicker.starter = lastStrIndex;

                this.quickEmojiPicker.show = false;
                this.quickMentioner.show = false;
            }
        },

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
        whisperFinishedTyping: _.debounce(function() {
            if (this.isGuest) return;

            Echo.private(this.EchoChannelAddress).whisper('finished-typing', {
                username: auth.username
            });

            this.isTyping = false;
        }, 600),

        handleKey(event, key) {
            if (
                !this.quickEmojiPicker.show &&
                !this.quickMentioner.show &&
                !this.quickChannelPicker.show
            )
                return;

            event.preventDefault();

            this.$eventHub.$emit('keyup:' + key);
        },

        setEditing(comment) {
            this.clear();

            this.editingComment = comment;
            this.message = this.editingComment.content.text;
            this.parent = this.editingComment.parent_id;

            this.$refs.input.focus();
        },

        setReplying(comment) {
            this.clear();

            this.replyingComment = comment;
            this.parent = this.replyingComment.id;

            this.$refs.input.focus();
        },

        handleEscapteKeyup() {
            if (this.quickEmojiPicker.show) {
                this.quickEmojiPicker.show = false;
            } else if (this.quickMentioner.show) {
                this.quickMentioner.show = false;
            } else if (this.quickChannelPicker.show) {
                this.quickChannelPicker.show = false;
            } else {
                if (
                    !_.isEmpty(this.editingComment) ||
                    !_.isEmpty(this.replyingComment)
                ) {
                    this.clear();
                }
            }
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
        },

        pick(pickedStr, starterIndex, typedLength) {
            this.insertPickedItem(
                'comment-form-textarea',
                pickedStr + ' ',
                starterIndex,
                typedLength
            );
        },

        openEmojiPicker() {
            this.emojiPicker = true;
        },

        closeEmojiPicker() {
            this.emojiPicker = false;
        },

        submit(event) {
            event.preventDefault();

            // ignore if any quick pciking box is open
            if (
                this.quickEmojiPicker.show ||
                this.quickMentioner.show ||
                this.quickChannelPicker.show ||
                !this.message.trim()
            )
                return;

            this.closeEmojiPicker();

            if (this.isGuest) {
                this.mustBeLogin();
                return;
            }

            this.temp = this.message;
            this.message = '';

            this.loading = true;

            // we're editing, not posting a new comment
            if (this.editing) {
                if (this.temp == this.before) {
                    this.message = this.temp;
                    this.loading = false;
                    this.$eventHub.$emit('patchedComment', this.editingComment);

                    return;
                }

                this.patchComment();
                return;
            }

            // new comment
            this.postComment();
        },

        patchComment() {
            axios
                .patch(`/comments/${this.editingComment.id}`, {
                    body: this.temp
                })
                .then(() => {
                    this.editingComment.content.text = this.temp;
                    this.$eventHub.$emit('patchedComment', this.editingComment);

                    this.clear();
                })
                .catch((error) => {
                    this.loading = false;
                    this.message = this.temp;
                });
        },

        postComment() {
            axios
                .post(`/submissions/${this.submission}/comments`, {
                    parent_id: this.parent,
                    body: this.temp
                })
                .then((response) => {
                    Store.state.comments.likes.push(response.data.data.id);
                    this.$eventHub.$emit('newComment', response.data.data);

                    this.clear();
                })
                .catch((error) => {
                    this.loading = false;
                    this.message = this.temp;
                });
        }
    }
};
</script>
