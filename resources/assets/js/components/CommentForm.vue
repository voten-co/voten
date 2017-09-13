<template>
    <section class="no-border" :class="isReply ? '' : 'full-comment-form'">
        <div class="content">
        	<div class="ui reply form flex-display">
                <textarea type="text" v-model="message" :id="'comment-form-' + parent" class="v-comment-form"
                          placeholder="Type your comment..." autocomplete="off" rows="1" name="comment"
                          v-on:keydown.enter="submit($event)" v-focus="focused" @focus="focused = true"
                ></textarea>

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
	import 'jquery.caret';
	import 'at.js';

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
                mentioning: false,
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
            this.atWho();

			this.$nextTick(function () {
        		this.$root.autoResize();
			})
		},

        methods: {
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

        		this.temp = this.message
        		this.message = ''

        		$('#comment-form-' + this.parent).css('height', 49)

        		this.loading = true

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