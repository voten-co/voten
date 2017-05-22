<template>
    <div class="m-box" v-bind:class="{ 'margin-top-1': !ownsPrevious }">
	    <div class="m-box-left">
	    	<div class="m-info user-select">
				<img :src="list.owner.avatar" :alt="list.owner.avatarusername" class="m-avatar" v-bind:class="{ 'hidden': ownsPrevious }">
		    </div>

			<div class="m-content">
				<b v-if="!ownsPrevious" class="m-username user-select">{{ '@' + list.owner.username }}</b>
	        	<markdown :text="list.data.text"></markdown>
			</div>
	    </div>

	    <div class="m-actions user-select pointer" @click="$emit('select-message', list.id)">
		    <time class="go-gray" data-toggle="tooltip" data-placement="left" :title="'Sent on: ' + longDate">
	        	{{ date }}
	        </time>

			<i class="v-icon v-seen go-gray" aria-hidden="true" v-if="displaySeen"></i>

			<i class="v-icon v-checked go-primary" aria-hidden="true" v-show="selected"></i>
	    </div>
	</div>
</template>

<script>
import Markdown from '../components/Markdown.vue';
import Helpers from '../mixins/Helpers';

export default {
	props: ['list', 'selected', 'previous', 'chatting'],

	components: { Markdown },

	mixins: [Helpers],

	data () {
		return {
			auth
		}
	},

	created: function () {
		this.markAsRead()
	},

	mounted: function () {
		this.$nextTick(function () {
			this.$root.loadSemanticTooltip()
		})
	},

	computed: {
		/**
		 * Whether or not auth user owns the message
		 *
		 * @return Boolean
		 */
		owns () {
			return this.list.owner.id == auth.id
		},

		/**
		 * Whether or not display the seen icon
		 *
		 * @return Boolean
		 */
		displaySeen () {
			return this.owns && this.list.read_at
		},

		/**
		 * Determines whether or not the previuos message was sent by the same user (ownser).
		 * We'll be using this info to know whether or not display the avatar.
		 *
		 * @return Boolean
		 */
		ownsPrevious () {
			if (this.previous == undefined) {
				return false
			}

			if (this.previous.owner.id == this.list.owner.id) {
				return true
			}

			return false
		},

		/**
		 * Calculates the correct date to display.
		 *
		 * @return String
		 */
		date () {
			if (this.isItToday(this.list.created_at)) {
				return this.parseDateForToday(this.list.created_at)
			}

			return this.parseDate(this.list.created_at);
		},

		/**
		 * Calculates the long date to display for "Sent at".
		 *
		 * @return String
		 */
		longDate () {
			return this.parseFullDate(this.list.created_at);
		},

		/**
		 * Calculates the correct date to display for "Seen at".
		 *
		 * @return String
		 */
		seenDate () {
			if (this.list.read_at) {
				return this.parseFullDate(this.list.read_at);
			}

			return "Not seen yet"
		},

		isChatting () {
			return Store.contentRouter == "messages" && this.chatting
		}
	},

	methods: {
		/**
		 * If the message hasn't been already read, it's time for it! The database doesn't need to know (cuz
		 * it is gonna get update them all as "seen" the next time it fetches the messages). What we do
		 * need is to fire a MessageRead event and broadcast it to the sender of the message.
		 *
		 * @return void
		 */
		markAsRead () {
			if ( this.isChatting && !this.owns && !this.list.read_at ) {
				this.$emit('last-was-read')

				axios.post('/message-read', {
					message_id: this.list.id,
					sender_id: this.list.owner.id
				})
			}
		},
	}
}
</script>