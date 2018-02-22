export default {
	data() {
		return { clientsideSettings };
	},

	methods: {
		/**
		 * Preloads few Store data using the HTML5 local storage. This'll get more updates in the future.
		 * Let's begin by preloading sidebar channels.
		 *
		 * @return void
		 */
		preloadStore() {
			if (Vue.isSetLS('store-state')) {
				Store.state = Vue.getLS('store-state');
			}
		},

		/**
		 * Filles the Store
		 *
		 * @return void
		 */
		fillBasicStore() {
			if (this.isGuest) return;

			// preLoad few Store values. This is used to avoid need for loading. Sure it might be fast enough now,
			// but it's not instant! This makes it instant. Also, we need to make sure the preloaded data is
			// fresh, and that's why we're still doing the ajax request to update it. Performance baby!
			// this.preloadStore();

			axios.get('/fill-basic-store').then((response) => {
				Store.state.submissions.upVotes =
					response.data.submissionUpvotes;
				Store.state.submissions.downVotes =
					response.data.submissionDownvotes;
				Store.state.comments.upVotes = response.data.commentUpvotes;
				Store.state.comments.downVotes = response.data.commentDownvotes;
				Store.state.bookmarks.submissions =
					response.data.bookmarkedSubmissions;
				Store.state.bookmarks.comments =
					response.data.bookmarkedComments;
				Store.state.bookmarks.channels =
					response.data.bookmarkedChannels;
				Store.state.bookmarks.users = response.data.bookmarkedUsers;
				Store.state.subscribedChannels =
					response.data.subscribedChannels;
				Store.state.moderatingChannels =
					response.data.moderatingChannels;
				Store.state.bookmarkedChannels =
					response.data.bookmarkedChannelsRecords;
				Store.state.blocks.users = response.data.blockedUsers;

				response.data.moderatingChannels.forEach((element, index) => {
					Store.state.moderatingAt.push(element.id);
				});

				response.data.subscribedChannels.forEach((element, index) => {
					Store.state.subscribedAt.push(element.id);
				});

				response.data.moderatingChannelsRecords.forEach(
					(element, index) => {
						if (element.role == 'administrator') {
							Store.state.administratorAt.push(
								element.channel_id
							);
						} else if (element.role == 'moderator') {
							Store.state.moderatorAt.push(element.channel_id);
						}
					}
				);

				Store.initialFilled = true;
			});
		},

		/**
		 * Pulls 'store-state' from LocalStorage and put it in the Store.state.
		 * In other words, loads the Store from the LocalStorage.
		 *
		 * @return void
		 */
		pullStore() {
			Store.state = Vue.getLS('store-state');
		},

		/**
		 * Pushes Store.state into LocalStorage's 'store-state'.
		 * In other words, saves the Store into the LocalStorage.
		 *
		 * @return void
		 */
		pushStore(delay = false) {
			if (delay === false) {
				this.pushStoreNow();
				return;
			}

			this.optimizedPushStore();
		},

		pushSettingsToServer() {
			axios.post('/clientside-settings', {
				platform: 'Web',
				json: JSON.stringify(Store.settings)
			});
		},

		pushStoreNow() {
			Vue.putLS('store-state', Store.state);
		},

		optimizedPushStore: _.debounce(function() {
			Vue.putLS('store-state', Store.state);
		}, 1000)
	},

	created() {
		if (this.isGuest) return;

		this.$eventHub.$on('push-store', this.pushStore);
		this.$eventHub.$on('pull-store', this.pullStore);
		this.$eventHub.$on('push-settings', this.pushSettingsToServer);

		document.addEventListener('visibilitychange', function() {
			// Just opened (or clicked on) the window
			if (document.visibilityState == 'visible') {
				let tempState = Vue.getLS('store-state', true);

				if (tempState != null) {
					Store.state = tempState;
				}
			}
		});
	},

	watch: {
		'Store.state': {
			handler() {
				if (this.isGuest) return;
				if (!Store.initialFilled) return;

				this.$eventHub.$emit('push-store');
			},

			deep: true
		},

		'Store.settings': {
			handler() {
				if (this.isGuest) return;
				if (!Store.initialFilled) return;

				this.$eventHub.$emit('push-settings');
			},

			deep: true
		}
	}
};
