<template>
	<div class="vo-modal"
	     id="messages"
	     v-loading="loadingMessages & page === 1">
		<header class="user-select">
			<div class="flex-space"
			     :class="{'padding-desktop-1-mobile-half': pageRoute == 'contacts'}">
				<!-- contacts page -->
				<el-input v-show="pageRoute == 'contacts'"
				          placeholder="Search by @username or name..."
				          :prefix-icon="loadingContacts ? 'el-icon-loading' : 'el-icon-search'"
				          v-model="filter"
				          @input="searchUsers(filter)"
				          clearable
				          ref="searchContacts"></el-input>

				<!-- Cancel Button -->
				<el-button type="text"
				           @click="close"
				           class="margin-left-1"
				           v-show="pageRoute == 'contacts'">
					Cancel
				</el-button>

				<!-- Chat page -->
				<div class="vo-modal-title flex-align-center"
				     v-if="pageRoute == 'chat'">
					<router-link :to="'/@' + currentContact.username">
						<h1 class="title desktop-only">
							<img v-bind:src="currentContact.avatar"> @{{ currentContact.username }}
						</h1>
					</router-link>

					<div class="v-message-badge desktop-only"
					     v-if="isBlocked">
						<el-tooltip content="Blocked"
						            placement="bottom"
						            transition="false"
						            :open-delay="500">
							<i class="v-icon v-block go-gray"
							   aria-hidden="true"></i>
						</el-tooltip>
					</div>
				</div>

				<!-- Modal Buttons -->
				<div class="buttons"
				     v-show="pageRoute == 'chat'">
					<!-- Close Button -->
					<el-tooltip content="Close (esc)"
					            placement="bottom"
					            transition="false"
					            :open-delay="500">
						<div class="v-close"
						     @click="close">
							<i class="el-icon-close"
							   aria-hidden="true"></i>
						</div>
					</el-tooltip>

					<!-- Menu Button -->
					<el-dropdown size="medium"
					             type="primary"
					             trigger="click"
					             :show-timeout="0"
					             :hide-timeout="0">
						<div class="v-back">
							<i class="el-icon-more-outline"></i>
						</div>

						<el-dropdown-menu slot="dropdown">
							<el-dropdown-item @click.native="leaveConversation">
								Leave Conversation
							</el-dropdown-item>

							<el-dropdown-item @click.native="blockUser">
								{{ isBlocked ? 'Unblock User' : 'Block User' }}
							</el-dropdown-item>
						</el-dropdown-menu>
					</el-dropdown>

					<!-- Back Button -->
					<el-tooltip content="Back to contacts"
					            placement="bottom"
					            transition="false"
					            :open-delay="500">
						<div class="v-back"
						     @click="backToContacts">
							<i class="el-icon-back"
							   aria-hidden="true"></i>
						</div>
					</el-tooltip>

					<!-- Delete Button -->
					<el-tooltip :content="'Delete ' + selectedMessages.length + ' selected messages'"
					            placement="bottom"
					            transition="false"
					            :open-delay="500">
						<div class="v-delete-button"
						     v-show="selectedMessages.length"
						     @click="deleteMessages">
							<i class="v-icon block-before v-trash"
							   aria-hidden="true"></i>
						</div>
					</el-tooltip>
				</div>
			</div>
		</header>

		<!-- contacts page -->
		<div class="middle background-white"
		     id="v-contacts"
		     v-show="pageRoute == 'contacts'"
		     :class="{'flex-center' : (!hasContacts && !hasSearchedContacts)}">
			<div class="col-7">
				<div class="user-select v-nth-box"
				     v-if="!hasContacts && !hasSearchedContacts">
					<contacts-icon width="250"
					               height="250"
					               class="margin-bottom-3"></contacts-icon>

					<h3 class="no-notifications">
						No contacts here yet
					</h3>
				</div>

				<ul class="v-contact-list">
					<li v-for="c in filteredContacts"
					    @click="getMessagesByContact(c.contact)"
					    :class="(!c.last_message.read_at && c.last_message.user_id != auth.id) ? 'has-unread-messages' : ''">
						<div class="v-contact-avatar">
							<img v-bind:src="c.contact.avatar"
							     v-bind:alt="c.contact.username" />
						</div>

						<div class="v-contact">
							<h3>
								@{{ c.contact.username }}
								<span class="v-contact-username">{{ c.contact.name }}</span>
							</h3>

							<p>
								{{ c.last_message.content.text }}
							</p>
						</div>
					</li>

					<li v-for="user in searchedUsers"
					    @click="getMessagesByUser(user)">
						<div class="v-contact-avatar">
							<img v-bind:src="user.avatar"
							     v-bind:alt="user.username" />
						</div>

						<div class="v-contact">
							<h3>@{{ user.username }}
								<span class="v-contact-username">{{ user.name }}</span>
							</h3>
							<p></p>
						</div>
					</li>
				</ul>
			</div>
		</div>

		<!-- Chat page -->
		<div class="container-fluid overflow-hidden"
		     id="v-messages"
		     v-show="pageRoute == 'chat'">
			<div class="messages-container"
			     id="chat-box"
			     :class="(!Store.state.messages || ! Store.state.messages.length) && !Store.state.messages.length ? 'flex-center' : 'flex-column-end'">
				<div class="user-select v-nth-box"
				     v-if="!hasMessages && !loadingMessages">
					<chat-icon width="250"
					           height="250"
					           class="margin-bottom-3"></chat-icon>

					<h3 class="no-messages">
						No messages here yet
					</h3>
				</div>

				<div class="overflow-auto"
				     v-if="hasMessages"
				     ref="scrollable"
				     id="scrollable-wrapper">
					<div class="flex-center"
					     v-if="moreToLoad">
						<el-button round @click="loadMore"
						           :loading="loadingMessages">Load More</el-button>
					</div>

					<message v-for="(value, index) in Store.state.messages"
					         :list="value"
					         :key="value.id"
					         :chatting="pageRoute == 'chat'"
					         :previous="Store.state.messages[index-1]"
					         :selected="selectedMessages.indexOf(value.id) != -1"
					         @select-message="selectMessage"
					         @last-was-read="markLastMessageAsRead(currentContactId)">
					</message>
				</div>

				<el-button round class="new-message-notify user-select"
				           size="small"
				           plain
				           icon="el-icon-arrow-down"
				           v-if="newMessagesNotifier"
				           @click="downToNewMessages">
					{{ newMessagesNotifier === 1 ? '1 new message' : (newMessagesNotifier + ' new messages') }}
				</el-button>
			</div>

			<!-- Chat Textarea -->
			<div class="padding-sides-1"
			     @keydown.down="handleKey($event, 'down')"
			     @keydown.up="handleKey($event, 'up')"
			     @keydown.enter="handleKey($event, 'enter')"
			     id="chat-textarea">
				<div v-if="preview && message"
				     class="form-wrapper margin-bottom-1 preview">
					<markdown :text="message.trim()"></markdown>
				</div>

				<form class="chat-input-form relative">
					<transition name="el-zoom-in-bottom">
						<quick-emoji-picker v-if="quickEmojiPicker.show"
						                    @close="quickEmojiPicker.show = false"
						                    :message="message"
						                    textareaid="message-form-textarea"
						                    :starter="quickEmojiPicker.starter"
						                    @pick="pick"></quick-emoji-picker>
					</transition>

					<transition name="el-zoom-in-bottom">
						<quick-channel-picker v-if="quickChannelPicker.show"
						                      @close="quickChannelPicker.show = false"
						                      :message="message"
						                      textareaid="message-form-textarea"
						                      @pick="pick"
						                      :starter="quickChannelPicker.starter"></quick-channel-picker>
					</transition>

					<el-input type="textarea"
					          autosize
					          placeholder="Type your message here..."
					          v-model="message"
                              @keydown.meta.enter.exact.native="submit"
                              @keydown.ctrl.enter.exact.native="submit"
					          :disabled="disableTextArea"
					          name="message"
					          :maxlength="5000"
					          id="message-form-textarea"
					          @input="typed"
					          ref="messageForm"></el-input>

					<span class="send-button comment-emoji-button"
					      v-if="isDesktop && !loading">
						<div @mouseout="closeEmojiPicker"
						     @mouseover="openEmojiPicker"
						     class="flex-center">
							<emoji-icon width="38"
							            height="38"></emoji-icon>

							<transition name="el-zoom-in-bottom">
								<emoji-picker v-if="emojiPicker"
								              textareaid="message-form-textarea"
								              @pick="pick"></emoji-picker>
							</transition>
						</div>
					</span>

					<button type="submit"
					        :class="{ 'go-green': message.trim() }"
					        @click="submit">
						<el-tooltip placement="bottom-end"
						            transition="false">
							<div slot="content">
								Press Command/Ctrl + Enter to send
							</div>
							<i class="v-icon v-send"
							   aria-hidden="true"></i>
						</el-tooltip>
					</button>
				</form>

				<div class="flex-right user-select comment-form-guide-wrapper">
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
		</div>
	</div>
</template>

<script>
import InputHelpers from '../mixins/InputHelpers';
import Markdown from '../components/Markdown.vue';
import Helpers from '../mixins/Helpers';
import Message from './Message.vue';
import EmojiPicker from '../components/EmojiPicker.vue';
import ContactsIcon from './Icons/ContactsIcon.vue';
import ChatIcon from '../components/Icons/ChatIcon.vue';
import EmojiIcon from '../components/Icons/EmojiIcon.vue';
import QuickEmojiPicker from '../components/QuickEmojiPicker.vue';
import QuickChannelPicker from '../components/QuickChannelPicker.vue';

export default {
    mixins: [InputHelpers, Helpers],

    components: {
        QuickChannelPicker,
        QuickEmojiPicker,
        Message,
        EmojiPicker,
        ContactsIcon,
        ChatIcon,
        EmojiIcon,
        Markdown
    },

    data() {
        return {
            loading: false,
            filter: '',
            searchedUsers: [],
            emojiPicker: false,
            selectedMessages: [],
            pageRoute: 'contacts',
            currentContact: [],
            message: '',
            currentContactId: 0,
            page: 1,
            loadingContacts: true,
            loadingMessages: true,
            moreToLoad: false,
            newMessagesNotifier: 0,
            preview: false,

            quickEmojiPicker: {
                show: false,
                starter: null
            },

            quickChannelPicker: {
                show: false,
                starter: null
            }
        };
    },

    created() {
        this.getContacts();
        this.listen();
        this.$eventHub.$on('conversation', this.getMessagesByUser);
        this.$eventHub.$on('pressed-esc', this.handleEscapteKeyup);
    },

    beforeDestroy() {
        this.$eventHub.$off('conversation', this.getMessagesByUser);
        this.$eventHub.$off('pressed-esc', this.handleEscapteKeyup);
    },

    watch: {
        'Store.modals.messages.show'() {
            if (Store.modals.messages.show === true) {
                window.location.hash = 'messages';

                if (this.pageRoute === 'chat') {
                    // Because otherwise user has clicked on MessageButton (and there is no existing conversation)
                    if (this.hasMessages) {
                        this.broadcastAsRead();
                        this.markLastMessageAsRead(this.currentContactId);
                    }

                    this.$refs.messageForm.$refs.textarea.focus();
                } else if (this.pageRoute === 'contacts') {
                    this.$refs.searchContacts.$refs.input.focus();
                }
            }
        }
    },

    computed: {
        hasMessages() {
            return Store.state.messages.length !== 0;
        },

        hasContacts() {
            return Store.state.contacts.length !== 0;
        },

        hasSearchedContacts() {
            return this.searchedUsers.length !== 0;
        },

        isBlocked() {
            return (
                Store.state.blocks.users.indexOf(this.currentContactId) !== -1
            );
        },

        disableTextArea() {
            return this.isBlocked || this.loadingMessages;
        },

        filteredContacts() {
            let self = this;

            if (Store.state.contacts) {
                return _.orderBy(
                    Store.state.contacts.filter(
                        (item) =>
                            item.contact.username.indexOf(self.filter) !== -1
                    ),
                    'last_message.created_at',
                    'desc'
                );
            }
        }
    },

    methods: {
        typed(string) {
            // close on empty input
            if (!string.trim()) {
                this.quickEmojiPicker.show = false;
                this.quickChannelPicker.show = false;
                return;
            }

            // get the last typed character (but not the last character of the string)
            let lastStrIndex = this.lastTypedCharacter('message-form-textarea');
            let lastStr = string[lastStrIndex];

            // close on space
            if (lastStr == ' ') {
                this.quickEmojiPicker.show = false;
                this.quickChannelPicker.show = false;
                return;
            }

            if (lastStr == ':') {
                this.quickEmojiPicker.show = true;
                this.quickEmojiPicker.starter = lastStrIndex;

                this.quickChannelPicker.show = false;
            } else if (lastStr == '#') {
                this.quickChannelPicker.show = true;
                this.quickChannelPicker.starter = lastStrIndex;

                this.quickEmojiPicker.show = false;
            }
        },

        handleKey(event, key) {
            if (!this.quickEmojiPicker.show && !this.quickChannelPicker.show)
                return;

            event.preventDefault();

            this.$eventHub.$emit('keyup:' + key);
        },

        handleEscapteKeyup() {
            if (this.quickEmojiPicker.show) {
                this.quickEmojiPicker.show = false;
            } else if (this.quickChannelPicker.show) {
                this.quickChannelPicker.show = false;
            } else {
                this.close();
            }
        },

        pick(pickedStr, starterIndex, typedLength) {
            this.insertPickedItem(
                'message-form-textarea',
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

        /**
         * deletes all the messages from and the conversation
         *
         * @return void
         */
        leaveConversation() {
            axios
                .delete('/conversations', {
                    params: {
                        user_id: this.currentContactId
                    }
                })
                .then(() => {
                    let contactID = this.currentContactId;

                    this.backToContacts();

                    // remove the contact
                    Store.state.contacts = Store.state.contacts.filter(
                        (contact) => contact.user_id != contactID
                    );
                });
        },

        /**
         * adds the contact to the auth user's blocked user's list (the blocked usre won't be able
         * to send further messages)
         *
         * @return void
         */
        blockUser() {
            let wasBlocked = this.isBlocked;

            axios
                .post(`/users/${this.currentContactId}/block`)
                .then(() => {
                    if (wasBlocked) {
                        let index = Store.state.blocks.users.indexOf(
                            this.currentContactId
                        );
                        
                        Store.state.blocks.users.splice(index, 1);
                    } else {
                        Store.state.blocks.users.push(this.currentContactId);
                    }
                });
        },

        /**
         * Toggles the message into the selected messages
         *
         * @return void
         */
        selectMessage(id) {
            if (this.selectedMessages.indexOf(id) !== -1) {
                let index = this.selectedMessages.indexOf(id);
                this.selectedMessages.splice(index, 1);
                return;
            }

            this.selectedMessages.push(id);
        },

        /**
         * Loops through the messages and deletes the ones that
         * were selected. And sends the ajax-request to
         * server to do the same.
         *
         * @return void
         */
        deleteMessages() {
            for (let i = 0; i < this.selectedMessages.length; i++) {
                for (let j = 0; j < Store.state.messages.length; j++) {
                    if (
                        Store.state.messages[j].id === this.selectedMessages[i]
                    ) {
                        let index = Store.state.messages.indexOf(
                            Store.state.messages[j]
                        );
                        Store.state.messages.splice(index, 1);
                    }
                }
            }

            axios
                .delete('/messages', {
                    params: {
                        messages: this.selectedMessages
                    }
                })
                .then(() => {
                    this.selectedMessages = [];
                });
        },

        /**
         * Gets a collection of users that the auth user is actually
         * allowed to start a conversation with and adds it to the
         * this.searchedUsers which leads to being displayed.
         *
         * @return void
         */
        searchUsers: _.debounce(function(typed) {
            if (!typed.trim()) return;

            this.loadingContacts = true;

            axios
                .get('/conversations/search', {
                    params: {
                        keyword: typed
                    }
                })
                .then((response) => {
                    this.searchedUsers = response.data.data;

                    this.loadingContacts = false;
                })
                .catch(() => {
                    this.loadingContacts = false;
                });
        }, 600),

        close() {
            if (window.location.hash == '#messages') {
                history.go(-1);
            }

            Store.modals.messages.show = false;
        },

        backToContacts() {
            this.pageRoute = 'contacts';
            this.currentContactId = 0;
        },

        getContacts() {
            this.loadingContacts = true;

            axios
                .get('/conversations', {
                    params: {
                        with_last_message: 1,
                        with_contact: 1
                    }
                })
                .then((response) => {
                    Store.state.contacts = response.data.data;

                    this.loadingContacts = false;
                })
                .catch(() => {
                    this.loadingContacts = false;
                });
        },

        getMessagesByContact(contact) {
            this.currentContact = contact;
            this.getMessagesByContactId(contact.id);
        },
        getMessagesByUser(user) {
            this.currentContact = user;
            this.getMessagesByContactId(user.id);
        },

        /**
         * Fetches the messages for the selected conversaion which is especified
         * by the contact_id. It also decides which event is needed to be
         * fired and fires it. Also focuses on the message input.
         *
         * @param integer contact_id
         * @return void
         */
        getMessagesByContactId(contact_id) {
            this.pageRoute = 'chat';
            this.page = 1;
            Store.state.messages = [];
            this.loadingMessages = true;
            this.currentContactId = contact_id;

            axios
                .get('/messages', {
                    params: {
                        contact_id: contact_id,
                        page: this.page
                    }
                })
                .then((response) => {
                    this.loadingMessages = false;

                    Store.state.messages = response.data.data.reverse();

                    this.moreToLoad = true;

                    if (response.data.links.next === null) {
                        this.moreToLoad = false;
                    }

                    if (Store.state.messages.length) {
                        this.markLastMessageAsRead(contact_id);
                    }

                    this.$nextTick(() => {
                        this.$refs.messageForm.$refs.textarea.focus();
                        this.chatScroll();
                    });
                })
                .catch(() => {
                    this.loadingMessages = false;
                });
        },

        /**
         * In case there are more messages ready to be loaded
         * (specified by this.getMessagesByContactId()) it fetches
         * them and adds the to the beginning of messages array.
         *
         * @return void
         */
        loadMore() {
            this.page++;
            this.loadingMessages = true;

            axios
                .get('/messages', {
                    params: {
                        contact_id: this.currentContactId,
                        page: this.page
                    }
                })
                .then((response) => {
                    Store.state.messages.unshift(
                        ...response.data.data.reverse()
                    );

                    this.loadingMessages = false;

                    if (response.data.links.next == null) {
                        this.moreToLoad = false;
                    }
                })
                .catch(() => {
                    this.loadingMessages = false;
                });
        },

        /**
         * Scrolls the chat page to the bottom of the chat window.
         *
         * @return void
         */
        downToNewMessages() {
            this.newMessagesNotifier = 0;
            this.chatScroll();
        },

        /**
         * Scrolls the chat page to the bottom of the chat window.
         *
         * @return void
         */
        chatScroll() {
            this.$nextTick(() => {
                this.scrollToBottom('scrollable-wrapper');
            });
        },

        /**
         * Listens for the new messages. When receives one adds it to the
         * Store.state.messages array, in case it's not for the current chat, stores
         * it for the contact and then fires necessary events to notify user.
         *
         * @return void
         */
        listen() {
            Echo.private('App.User.' + auth.id)
                .listen('MessageCreated', (event) => {
                    this.updateLastMessage(event.data.author.id, event.data);

                    if (this.currentContactId == event.data.author.id) {
                        let chatBox = this.$refs.scrollable;

                        if (
                            chatBox.scrollHeight - chatBox.scrollTop <
                            chatBox.offsetHeight + 500
                        ) {
                            this.$nextTick(() => {
                                this.chatScroll();
                            });
                        } else {
                            this.newMessagesNotifier++;
                        }

                        Store.state.messages.push(event.data);
                    }

                    // Sending web notifications to user's OS(if website is not active)
                    if (document.hidden == true) {
                        let body = event.data.content.text;
                        let link = 'new-message';
                        let avatar = event.data.auhtor.avatar;

                        let title = 'New Message';

                        const data = {
                            title: title,
                            body: body,
                            url: link,
                            icon: avatar
                        };

                        this.$eventHub.$emit('push-notification', data);
                    }
                })
                .listen('MessageRead', (event) => {                    
                    this.markMessageAsRead(event.data);
                })
                .listen('ConversationRead', (event) => {
                    this.markConversationAsRead(event.data.user_id);
                });
        },

        /**
         * Updates the last saved message from the contact in "contacts page". If
         * the contanct doesn't exist in the Store.state.contacts array, it creates
         * One containing the last message which is sent as an arguman.
         *
         * @param integer contact_id (contact_id)
         * @param object message
         *
         * @return void
         */
        updateLastMessage(contact_id, message) {
            let i = Store.state.contacts.findIndex(
                (c) => c.contact.id === contact_id
            );

            if (i !== -1) {
                Store.state.contacts[i].last_message = message;
            } else {
                let contact = message.author;
                let last_message = message;

                Store.state.contacts.push({
                    contact,
                    user_id: contact.id,
                    last_message_id: last_message.id,
                    last_message
                });
            }
        },

        /**
         * Marks the last message of the conversaion as read, so it won't be counted in unreadMessages counting.
         *
         * @param integer contact_id
         * @return void
         */
        markLastMessageAsRead(contact_id) {
            let i = Store.state.contacts.findIndex(
                (c) => c.contact.id === contact_id
            );

            Store.state.contacts[i].last_message.read_at = this.now();
        },

        /**
         * Submits the message, scrolls ...
         *
         * @return void
         */
        submit(event) {
            event.preventDefault();

            if (!this.message.trim()) return;

            // ignore if any quick pciking box is open
            if (this.quickEmojiPicker.show || this.quickChannelPicker.show)
                return;

            this.closeEmojiPicker();

            let msgText = this.message;
            this.message = '';

            if (this.isEmpty(msgText)) return;

            axios
                .post('/messages', {
                    user_id: this.currentContactId,
                    body: msgText
                })
                .then((response) => {
                    Store.state.messages.push(response.data.data);

                    if (Store.state.messages.length === 1) {
                        this.turnUserToContact(
                            this.currentContactId,
                            response.data.data
                        );
                    } else {
                        this.updateLastMessage(
                            this.currentContactId,
                            response.data.data
                        );
                    }

                    this.chatScroll();
                })
                .catch((error) => {
                    this.message = msgText;
                });
        },

        turnUserToContact(contactID, message) {
            this.searchedUsers = [];
            this.filter = '';

            Store.state.contacts.unshift({
                contact: this.currentContact,
                user_id: this.currentContactId,
                created_at: this.now(),
                id: Store.state.contacts.length + 1,
                last_message: message,
                last_message_id: message.id
            });
        },

        /**
         * Marks all the messages (owned by auth) as read
         * (The contact has opened the conversatioon)
         *
         * @return void
         */
        markConversationAsRead(contactId) {
            if (this.currentContactId != contactId) return;

            Store.state.messages.forEach((element, index) => {
                if (element.owner.id == auth.id) {
                    element.read_at = this.now();
                }
            });
        },

        broadcastAsRead() {
            axios.post(`/conversations/${this.currentContactId}/read`);
        },

        /**
         * Marks the message as Read
         *
         * @return void
         */
        markMessageAsRead(message) {
            Store.state.messages.find(m => m.id === message.id).read_at = this.now();
        }
    }
};
</script>
