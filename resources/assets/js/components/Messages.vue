<template>
	<div class="v-modal" id="messages">
	    <div class="v-close" v-tooltip.bottom="{content: 'Close (esc)'}" @click="close">
	        <i class="v-icon block-before v-cancel" aria-hidden="true"></i>
	    </div>

		<div class="ui icon top right green pointing dropdown v-more-actions" v-show="pageRoute == 'chat'">
			<i class="v-icon block-before v-dot-3" aria-hidden="true"></i>

			<div class="menu">
				<button class="item" @click="leaveConversation">
					Leave Conversation
				</button>

				<button class="item" @click="blockUser">
					{{ isBlocked ? 'Unblock User' : 'Block User' }}
				</button>
			</div>
		</div>


	    <div class="v-back" v-show="pageRoute == 'chat'" v-tooltip.bottom="{content: 'Back to contacts'}" @click="backToContacts">
	        <i class="v-icon block-before v-return" aria-hidden="true"></i>
	    </div>

	    <div v-show="pageRoute == 'chat'">
	    	<div class="v-delete-button" v-show="selectedMessages.length" v-tooltip.bottom="{content: 'Delete Selected Messages'}" @click="deleteMessages">
		        <i class="v-icon block-before v-trash" aria-hidden="true"></i>
		    </div>
	    </div>

	    <div class="v-modal-title user-select" v-show="pageRoute == 'contacts'">
	        <h1 class="title">
	            Contacts
	        </h1>

	        <h4 class="sub-title">
	            Send secure direct messages in real-time
	        </h4>
	    </div>

	    <div class="v-modal-title user-select flex-align-center" v-show="pageRoute == 'chat'">
	        <router-link :to="'/@' + currentContact.username">
		        <h1 class="title desktop-only">
		        	<img v-bind:src="currentContact.avatar">
		            @{{ currentContact.username }}
		        </h1>
	        </router-link>

			<div class="v-message-badge desktop-only" v-if="isBlocked">
				<i class="v-icon v-block go-gray" aria-hidden="true" v-tooltip.bottom="{content: 'Blocked'}"></i>
			</div>
	    </div>


	    <div class="v-modal-search-box" :class="{ 'left-1': !sidebar }" v-show="pageRoute == 'contacts'">
	        <div class="ui contacts search">
	            <div class="ui huge icon input">
	                <input class="v-search" v-model="filter" type="text" placeholder="Search by @username or name..."
					v-on:input="searchUsers(filter)">
	                <i v-show="!loadingContacts" class="v-icon v-search search icon"></i>
		        	<moon-loader :loading="loadingContacts" :size="'30px'" :color="'#777'"></moon-loader>
	            </div>
	        </div>
	    </div>


	    <div class="container" id="v-contacts" v-show="pageRoute == 'contacts'">
	    	<div class="v-push-15"></div>

	        <div class="col-7 user-select">
	            <ul class="v-contact-list">
	                <li v-for="c in filteredContacts" @click="getMessagesByContact(c.contact)" :class="(!c.last_message.read_at && c.last_message.user_id != auth.id) ? 'has-unread-messages' : ''">
	                    <div class="v-contact-avatar">
	                        <img v-bind:src="c.contact.avatar" v-bind:alt="c.contact.username" />
	                    </div>
	                    <div class="v-contact">
	                        <h3>@{{ c.contact.username }}<span class="v-contact-username">{{ c.contact.username }}</span></h3>
	                        <p>
	                            {{ c.last_message.data.text }}
	                        </p>
	                    </div>
	                </li>

	                <li v-for="user in searchedUsers" @click="getMessagesByUser(user)">
	                    <div class="v-contact-avatar">
	                        <img v-bind:src="user.avatar" v-bind:alt="user.username" />
	                    </div>

	                    <div class="v-contact">
	                        <h3>@{{ user.username }}<span class="v-contact-username">{{ user.username }}</span></h3>
	                        <p></p>
	                    </div>
	                </li>
	            </ul>
	        </div>
	    </div>

	    <div class="container-fluid" id="v-messages" v-show="pageRoute == 'chat'">
	        <div class="col-12" id="chat-box">
		        <div class="flex-center" v-if="moreToLoad && !loadingMessages">
		        	<button type="button" class="v-button" @click="loadMore">
		        		Load More
		        	</button>
				</div>

	            <message v-for="(value, index) in Store.messages" :list="value" :key="value.id" :chatting="pageRoute == 'chat'"
				:previous="Store.messages[index-1]" :selected="selectedMessages.indexOf(value.id) != -1" @select-message="selectMessage"
				@last-was-read="markLastMessageAsRead(currentContactId)"></message>

	            <div class="new-message-notify user-select" v-show="newMessagesNotifier" @click="downToNewMessages">
	            	{{ newMessagesNotifier }} new messages
	            </div>
	        </div>

	        <div class="v-message-input">
	            <textarea name="message" rows="1"
				v-on:keydown.enter="sendMessage" placeholder="Type your message here..." id="chatInput"
				autocomplete="off" v-model="messageText" v-focus="focused" :disabled="disableTextArea"
				@focus="focused = true"></textarea>

				<span class="send-button comment-emoji-button">
                    <i class="v-icon v-smile h-yellow" aria-hidden="true" @click="toggleEmojiPicker"></i>

                    <emoji-picker v-if="emojiPicker" @emoji="emoji" v-on-clickaway="closeEmojiPicker"></emoji-picker>
                </span>

                <button type="button" v-bind:class="{ 'go-green': messageText.trim() }" @click="sendMessage">
					<i class="v-icon v-send" aria-hidden="true"></i>
				</button>
	        </div>
	    </div>
	</div>
</template>

<script>
import MoonLoader from '../components/MoonLoader.vue'
import InputHelpers from '../InputHelpers'
import Message from './Message.vue'
import { focus } from 'vue-focus'
import EmojiPicker from '../components/EmojiPicker.vue'
import { mixin as clickaway } from 'vue-clickaway';

export default {
	mixins: [InputHelpers, clickaway],

	directives: {
		focus
	},

    components: {
        Message,
        MoonLoader,
		EmojiPicker
    },

    props: ['sidebar'],

    data () {
        return {
        	focused: false,
        	filter: '',
	        searchedUsers: [],
			emojiPicker: false,
	        selectedMessages: [],
	        pageRoute: 'contacts',
	        currentContact: [],
		    currentChatId: '',
		    messageText: '',
		    currentContactId: 0,
		    page: 1,
		    loadingContacts: true,
		    loadingMessages: true,
		    moreToLoad: false,
		    newMessagesNotifier: 0,
		    auth,
		    Store,
        }
    },

    created () {
        this.getContacts();
        this.listen();
        this.$eventHub.$on('conversation', this.getMessagesByUser);
    },

    watch: {
		'Store.contentRouter': function () {
			if (Store.contentRouter == 'messages' && this.pageRoute == 'chat') {
				// cuase otherwise user has clicked on MessageButton (and there is no existing conversation)
				if (Store.messages.length) {
					this.broadcastAsRead()
					this.markLastMessageAsRead(this.currentContactId)
				}
			}
		}
	},

	mounted () {
		this.$nextTick(function () {
    		this.$root.autoResize();
		})
	},

    computed: {
		isBlocked() {
			return Store.blockedUsers.indexOf(this.currentContactId) != -1
		},

		disableTextArea() {
			return this.isBlocked || this.loadingMessages
		},

    	/**
    	 * The filtered version of contacts
    	 *
    	 * @return {Array} contacts
    	 */
    	filteredContacts () {
			var self = this

			if(Store.contacts) {
                return _.orderBy(Store.contacts.filter(function (item) {
                    return item.contact.username.indexOf(self.filter) !== -1
                }), 'last_message.created_at', 'desc')
			}

    	}
    },

    methods: {
		/**
		 * deletes all the messages from and the conversation
		 *
		 * @return void
		 */
		leaveConversation() {
		    axios.post('/leave-conversation', {
		        contact_id: this.currentContactId
		    }).then((response) => {
				let contactID = this.currentContactId

		        this.backToContacts()

				// remove the contact
				Store.contacts = Store.contacts.filter(function (contact) {
				  	return contact.contact_id != contactID
				})
		    })
		},

		/**
		 * adds the contact to the auth user's blocked user's list (the blocked usre won't be able
		 * to send further messages)
		 *
		 * @return void
		 */
		blockUser () {
			let wasBlocked = this.isBlocked

			axios.post('/block-contact', {
			    contact_id: this.currentContactId
			}).then((response) => {
				if (wasBlocked) {
					let index = Store.blockedUsers.indexOf(this.currentContactId)
					Store.blockedUsers.splice(index, 1)
				} else {
					Store.blockedUsers.push(this.currentContactId)
				}
			})
		},

		emoji(shortname){
			this.messageText = this.messageText + shortname + " "
		},

		toggleEmojiPicker() {
			this.emojiPicker = ! this.emojiPicker;
		},

		closeEmojiPicker() {
			this.emojiPicker = false
		},

    	/**
    	 * Toggles the message into the selected messages
    	 *
    	 * @return void
    	 */
    	selectMessage (id) {
    		if (this.selectedMessages.indexOf(id) != -1) {
    			var index = this.selectedMessages.indexOf(id)
    			this.selectedMessages.splice(index, 1)
    			return
    		}

    		this.selectedMessages.push(id)
    	},

    	/**
    	 * Loops through the messages and deletes the ones that
    	 * were selected. And sends the ajax-request to
    	 * server to do the same.
    	 *
    	 * @return void
    	 */
    	deleteMessages () {
    		for (var i = 0; i < this.selectedMessages.length; i++) {
			    for (var j = 0; j < Store.messages.length; j++) {
			    	if (Store.messages[j].id == this.selectedMessages[i]) {
			    		var index = Store.messages.indexOf(Store.messages[j])
                        Store.messages.splice(index, 1)
			    	}
				}
			}

    		axios.post('/delete-messages', {
                messages: this.selectedMessages,
            } ).then((response) => {
            	this.selectedMessages = []
            })
    	},

    	/**
    	 * Gets a collection of users that the auth user is actually
    	 * allowed to start a conversation with and adds it to the
    	 * this.searchedUsers which leads to being displayed.
    	 *
    	 * @return void
    	 */
    	searchUsers: _.debounce(function (typed) {
    		if(!typed.trim()) return

			this.loadingContacts = true

            axios.post( '/search-contacts', { filter: typed } ).then((response) => {
                this.searchedUsers = response.data

				this.loadingContacts = false
            }).catch((error) => {
				this.loadingContacts = false
			});
    	}, 600),

    	close () {
    		this.$eventHub.$emit('close')
    	},

        backToContacts () {
            this.pageRoute = 'contacts'
            this.focused = false
	    	this.currentContactId = 0
        },

        getContacts () {
			this.loadingContacts = true

            axios.get('/contacts').then((response) => {
                Store.contacts = response.data

				this.loadingContacts = false
            }).catch((error) => {
				this.loadingContacts = false
			});
        },

        getMessagesByContact (contact) {
        	this.currentContact = contact
        	this.getMessagesByContactId(contact.id)
        },
        getMessagesByUser (user) {
        	this.currentContact = user
        	this.getMessagesByContactId(user.id)
        },

        /**
     	 * Fetches the messages for the selected conversaion which is especified
     	 * by the contact_id. It also decides which event is needed to be
    	 * fired and fires it. Also focuses on the message input.
    	 *
    	 * @param {Integer} contact_id
    	 * @return void
    	 */
        getMessagesByContactId (contact_id) {
        	this.focused = true
        	this.pageRoute = 'chat'
        	this.page = 1
    		Store.messages = []
			this.loadingMessages = true
    		this.currentContactId = contact_id

            axios.get('/messages', {
            	params: {
	                contact_id: contact_id,
            		page: this.page,
            	}
            } ).then((response) => {
				this.loadingMessages = false

                Store.messages = response.data.data.reverse()
				this.chatScroll()

				this.moreToLoad = true

				if(response.data.next_page_url == null){
					this.moreToLoad = false
				}

				if(Store.messages.length){
					this.markLastMessageAsRead(contact_id)
				}
            }).catch((error) => {
				this.loadingMessages = false
			});
        },

        /**
    	 * In case there are more messages ready to be loaded
    	 * (especified by this.getMessagesByContactId()) it fetches
    	 * them and adds the to the begining of messages array.
    	 *
    	 * @return void
    	 */
        loadMore () {
        	this.page ++
			this.loadingMessages = true

        	axios.get('/messages', {
        		params: {
	                contact_id: this.currentContactId,
                	page: this.page,
        		}
            }).then((response) => {
                Store.messages.unshift(...response.data.data.reverse())

				this.loadingMessages = false

				if(response.data.next_page_url == null){
					this.moreToLoad = false
				}
       		}).catch((error) => {
				this.loadingMessages = false
			});
        },

        /**
    	 * Scrolls the chat page to the bottom of the chat window.
    	 *
    	 * @return void
    	 */
        downToNewMessages () {
        	this.newMessagesNotifier = 0
        	this.chatScroll()
        },

        /**
    	 * Scrolls the chat page to the bottom of the chat window.
    	 *
    	 * @return void
    	 */
		chatScroll () {
			setTimeout(function() {
				$("#chat-box").stop().animate({
					scrollTop: $("#chat-box")[0].scrollHeight
				}, 500)
			}, 200)
		},

		/**
    	 * Listens for the new messages. When receives one adds it to the
    	 * Store.messages array, in case it's not for the current chat, stores
    	 * it for the contact and then fires necceccary events to notify user.
    	 *
    	 * @return void
    	 */
        listen () {
            Echo.private('App.User.' + auth.id)
				.listen('MessageCreated', (e) => {
	            	this.updateLastMessage(e.contact_id, e.message)

					if (this.currentContactId == e.contact_id) {
						var chatBox = $("#chat-box")
						if(chatBox[0].scrollHeight - chatBox.scrollTop() < (chatBox.outerHeight() + 500)) {
							this.chatScroll()
						} else {
							this.newMessagesNotifier ++
						}
                        Store.messages.push(e.message)
					}

					// Sending web notifications to user's OS(if website is not active)
                    if(document.hidden == true) {
                        let body = e.message.data.text
                        let link = 'new-message'
                        let avatar = e.message.owner.avatar

                        let title = 'New Message'

                        const data = {
                            title: title,
                            body: body,
                            url: link,
                            icon: avatar
                        }

        				this.$eventHub.$emit('push-notification', data)
                    }

	            }).listen('MessageRead', (e) => {
	            	this.markMessageAsRead(e.message_id, e.contact_id)
	            }).listen('ConversationRead', (e) => {
	            	this.markConversationAsRead(e.contact_id)
	            })
        },

        /**
    	 * Updates the last saved message from the contact in "contacts page". If
    	 * the contanct doesn't exist in the Store.contacts array, it creates
    	 * One containing the last message which is sent as an arguman.
    	 *
    	 * @param Integer contact_id (contact_id)
    	 * @param Object message
    	 *
    	 * @return void
    	 */
    	updateLastMessage (contact_id, message) {
            function findObject(ob) {
                return ob.contact.id === contact_id
            }
            var i = Store.contacts.findIndex(findObject)

            if ( i != -1) {
            	Store.contacts[i].last_message = message
            } else {
            	var contact = message.owner
            	var last_message = message
				Store.contacts.push({
					contact,
					contact_id: contact.id,
					message_id: last_message.id,
					last_message,
				})
            }
    	},

    	/**
    	 * Marks the last message of the conversaion as read, so it won't be counted in unreadMessages counting.
    	 *
    	 * @param Integer contact_id
    	 * @return void
    	 */
    	markLastMessageAsRead (contact_id) {
    		function findObject(ob) {
                return ob.contact.id === contact_id
            }

            var i = Store.contacts.findIndex(findObject)

            Store.contacts[i].last_message.read_at = moment().utc().format('YYYY-MM-DD HH:mm:ss')
    	},

        /**
    	 * Submits the message, scrolls ...
    	 *
    	 * @return void
    	 */
		sendMessage (event) {
            if (this.shiftPlusEnter(event)) return

    		event.preventDefault()

			if (!this.messageText.trim()) return

			this.closeEmojiPicker()

			var msgText = this.messageText
			this.messageText = ''

			$(event.target).css('height', 50)

			if (this.isEmpty(msgText)) return

			let data = { text: msgText.trim() };

            Store.messages.push({
				data,
				owner: auth,
				user_id: auth.id,
				read_at: null,
				created_at: moment().utc().format('YYYY-MM-DD HH:mm:ss')
			});

            this.chatScroll()

			axios.post('/message', {
				contact: this.currentContactId,
				text: msgText
			}).then((response) => {
				this.updateMessage(response.data.id, response.data.data)

				if (Store.messages.length == 1) {
            		this.turnUserToContact(this.currentContactId, response.data)
				} else {
					this.updateLastMessage(this.currentContactId, response.data)
				}
			})
		},


		turnUserToContact (contactID, message) {
			this.searchedUsers = []
			this.filter = ''

			Store.contacts.unshift({
				contact: this.currentContact,
				contact_id: this.currentContactId,
				created_at: moment().utc().format('YYYY-MM-DD HH:mm:ss'),
				id: (Store.contacts.length + 1),
				last_message: message,
				message_id: message.id,
				user_id: auth.id
			})
		},


		/**
		 * Updates the message with the ajax respond info
		 *
		 * @return
		 */
		updateMessage (id, d) {
			var data = d;

            function findObject(ob) {
                return ob.data.text === data.text
            }

            Store.messages.find(findObject).id = id;
		},

    	/**
    	 * Marks all the messages (owned by auth) as read
    	 * (The contact has opened the conversatioon)
    	 *
    	 * @return void
    	 */
    	markConversationAsRead (contactId) {
    		if (this.currentContactId != contactId) return

            Store.messages.forEach( function(element, index) {
				if (element.owner.id == auth.id) {
					element.read_at = moment().utc().format('YYYY-MM-DD HH:mm:ss')
				}
			});
    	},

    	broadcastAsRead () {
    		axios.post('/conversation-read', {
				sender_id: this.currentContactId
			})
    	},

		/**
		 * Marks the message as Read
		 *
		 * @return void
		 */
		markMessageAsRead (messageId, contactId) {
			if (this.currentContactId != contactId) return

            function findObject(ob) {
                return ob.id === messageId
            }

            Store.messages.find(findObject).read_at = moment().utc().format('YYYY-MM-DD HH:mm:ss');
		}
    },
}
</script>
