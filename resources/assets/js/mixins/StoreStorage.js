export default {
    methods: {
        /**
         * Filles the Store
         *
         * @return void
         */
        fillBasicStore() {
        	if (auth.isGuest == true) return;

        	// make sure sideFilter is set
        	if (this.isSetLS('sidebar-filter')) {
   				Store.sidebarFilter = this.getLS('sidebar-filter');
   			} else {
   				Store.sidebarFilter = 'subscribed-channels';
   			}

            axios.get('/fill-basic-store', {
            	params: {
            		sidebar_filter: Store.sidebarFilter
            	}
            }).then((response) => {
                Store.submissionUpVotes = response.data.submissionUpvotes;
                Store.submissionDownVotes = response.data.submissionDownvotes;
                Store.commentUpVotes = response.data.commentUpvotes;
                Store.commentDownVotes = response.data.commentDownvotes;
                Store.submissionBookmarks = response.data.bookmarkedSubmissions;
                Store.commentBookmarks = response.data.bookmarkedComments;
                Store.categoryBookmarks = response.data.bookmarkedCategories;
                Store.userBookmarks = response.data.bookmarkedUsers;
                Store.subscribedCategories = response.data.subscribedCategories;
                Store.moderatingCategories = response.data.moderatingCategories;
                Store.blockedUsers = response.data.blockedUsers;

                response.data.moderatingCategories.forEach(function(element, index) {
                    Store.moderatingAt.push(element.id);
                });

                response.data.subscribedCategories.forEach(function(element, index) {
                    Store.subscribedAt.push(element.id);
                });

                response.data.moderatingCategoriesRecords.forEach(function(element, index) {
                    if (element.role == "administrator") {
                        Store.administratorAt.push(element.category_id);
                    } else if (element.role == "moderator") {
                        Store.moderatorAt.push(element.category_id);
                    }
                });

                Store.loading = false;
            })
        },

        updateSubmissionVotes() {
            if (Store.submissionUpVotes.length == Vue.ls.get('submissionUpVotes').length) {
                return;
            }

            Store.submissionUpVotes = Vue.ls.get('submissionUpVotes');
        },
        updateSubmissionDownVotes() {
            if (Store.submissionDownVotes.length == Vue.ls.get('submissionDownVotes').length) {
                return;
            }

            Store.submissionDownVotes = Vue.ls.get('submissionDownVotes');
        },


        updateCommentUpVotes() {
            if (Store.commentUpVotes.length == Vue.ls.get('commentUpVotes').length) {
                return;
            }

            Store.commentUpVotes = Vue.ls.get('commentUpVotes');
        },
        updateCommentDownVotes() {
            if (Store.commentDownVotes.length == Vue.ls.get('commentDownVotes').length) {
                return;
            }

            Store.commentDownVotes = Vue.ls.get('commentDownVotes');
        },

        updateSubmissionBookmarks() {
            if (Store.submissionBookmarks.length == Vue.ls.get('submissionBookmarks').length) {
                return;
            }

            Store.submissionBookmarks = Vue.ls.get('submissionBookmarks');
        },
        updateCommentBookmarks() {
            if (Store.commentBookmarks.length == Vue.ls.get('commentBookmarks').length) {
                return;
            }

            Store.commentBookmarks = Vue.ls.get('commentBookmarks');
        },
        updateCategoryBookmarks() {
            if (Store.categoryBookmarks.length == Vue.ls.get('categoryBookmarks').length) {
                return;
            }

            Store.categoryBookmarks = Vue.ls.get('categoryBookmarks');
        },
        updateUserBookmarks() {
            if (Store.userBookmarks.length == Vue.ls.get('userBookmarks').length) {
                return;
            }

            Store.userBookmarks = Vue.ls.get('userBookmarks');
        },

        updateSubscribedCategories() {
            if (Store.subscribedCategories.length == Vue.ls.get('subscribedCategories').length) {
                return;
            }

            Store.subscribedCategories = Vue.ls.get('subscribedCategories');
        },

        updateModeratingCategories() {
            if (Store.moderatingCategories.length == Vue.ls.get('moderatingCategories').length) {
                return;
            }

            Store.moderatingCategories = Vue.ls.get('moderatingCategories');
        },

        updateBlockedUsers() {
            if (Store.blockedUsers.length == Vue.ls.get('blockedUsers').length) {
                return;
            }

            Store.blockedUsers = Vue.ls.get('blockedUsers');
        },

        updateModeratingAt() {
            if (Store.moderatingAt.length == Vue.ls.get('moderatingAt').length) {
                return;
            }

            Store.moderatingAt = Vue.ls.get('moderatingAt');
        },

        updateSubscribedAt() {
            if (Store.subscribedAt.length == Vue.ls.get('subscribedAt').length) {
                return;
            }

            Store.subscribedAt = Vue.ls.get('subscribedAt');
        },

        updateSubscribedAt() {
            if (Store.subscribedAt.length == Vue.ls.get('subscribedAt').length) {
                return;
            }

            Store.subscribedAt = Vue.ls.get('subscribedAt');
        },

        updateModeratingCategoriesRecords() {
            if (Store.administratorAt.length != Vue.ls.get('administratorAt').length) {
                Store.administratorAt = Vue.ls.get('administratorAt');
            }

            if (Store.moderatorAt.length != Vue.ls.get('moderatorAt').length) {
                Store.moderatorAt = Vue.ls.get('moderatorAt');
            }
        },

        updateNotifications() {
            if (Store.notifications.length == Vue.ls.get('notifications').length) {
                return;
            }

            Store.notifications = Vue.ls.get('notifications');
        },

        updateMessages() {
            if (Store.messages.length == Vue.ls.get('messages').length) {
                return;
            }

            Store.messages = Vue.ls.get('messages');
        },

        updateContacts() {
            if (Store.contacts.length == Vue.ls.get('contacts').length) {
                return;
            }

            Store.contacts = Vue.ls.get('contacts');
        }
    },

    /**
     * Listen for localStorage changes. This makes it possible for us to synce all open tabs
     * together wo we won't have data missing in one tab.
     */
    mounted() {
        Vue.ls.on('submissionUpVotes', this.updateSubmissionVotes);
        Vue.ls.on('submissionDownVotes', this.updateSubmissionDownVotes);
        Vue.ls.on('commentUpVotes', this.updateCommentUpVotes);
        Vue.ls.on('commentDownVotes', this.updateCommentDownVotes);
        Vue.ls.on('submissionBookmarks', this.updateSubmissionBookmarks);
        Vue.ls.on('commentBookmarks', this.updateCommentBookmarks);
        Vue.ls.on('categoryBookmarks', this.updateCategoryBookmarks);
        Vue.ls.on('userBookmarks', this.updateUserBookmarks);
        Vue.ls.on('subscribedCategories', this.updateSubscribedCategories);
        Vue.ls.on('moderatingCategories', this.updateModeratingCategories);
        Vue.ls.on('blockedUsers', this.updateBlockedUsers);
        Vue.ls.on('moderatingAt', this.updateModeratingAt);
        Vue.ls.on('subscribedAt', this.updateSubscribedAt);
        Vue.ls.on('administratorAt', this.updateModeratingCategoriesRecords);
        Vue.ls.on('moderatorAt', this.updateModeratingCategoriesRecords);
        Vue.ls.on('notifications', this.updateNotifications);
        Vue.ls.on('messages', this.updateMessages);
        Vue.ls.on('contacts', this.updateContacts);
    },

    watch: {
        'Store.submissionUpVotes' () {
            Vue.ls.set('submissionUpVotes', Store.submissionUpVotes, 60 * 60 * 1000);
        },

        'Store.submissionDownVotes' () {
            Vue.ls.set('submissionDownVotes', Store.submissionDownVotes, 60 * 60 * 1000);
        },

        'Store.commentUpVotes' () {
            Vue.ls.set('commentUpVotes', Store.commentUpVotes, 60 * 60 * 1000);
        },

        'Store.commentDownVotes' () {
            Vue.ls.set('commentDownVotes', Store.commentDownVotes, 60 * 60 * 1000);
        },

        'Store.submissionBookmarks' () {
            Vue.ls.set('submissionBookmarks', Store.submissionBookmarks, 60 * 60 * 1000);
        },

        'Store.commentBookmarks' () {
            Vue.ls.set('commentBookmarks', Store.commentBookmarks, 60 * 60 * 1000);
        },

        'Store.categoryBookmarks' () {
            Vue.ls.set('categoryBookmarks', Store.categoryBookmarks, 60 * 60 * 1000);
        },

        'Store.userBookmarks' () {
            Vue.ls.set('userBookmarks', Store.userBookmarks, 60 * 60 * 1000);
        },

        'Store.subscribedCategories' () {
            Vue.ls.set('subscribedCategories', Store.subscribedCategories, 60 * 60 * 1000);
        },

        'Store.moderatingCategories' () {
            Vue.ls.set('moderatingCategories', Store.moderatingCategories, 60 * 60 * 1000);
        },

        'Store.blockedUsers' () {
            Vue.ls.set('blockedUsers', Store.blockedUsers, 60 * 60 * 1000);
        },

        'Store.moderatingAt' () {
            Vue.ls.set('moderatingAt', Store.moderatingAt, 60 * 60 * 1000);
        },

        'Store.subscribedAt' () {
            Vue.ls.set('subscribedAt', Store.subscribedAt, 60 * 60 * 1000);
        },

        'Store.administratorAt' () {
            Vue.ls.set('administratorAt', Store.administratorAt, 60 * 60 * 1000);
        },

        'Store.moderatorAt' () {
            Vue.ls.set('moderatorAt', Store.moderatorAt, 60 * 60 * 1000);
        },

        'Store.notifications' () {
            Vue.ls.set('notifications', Store.notifications, 60 * 60 * 1000);
        },

        'Store.messages' () {
            Vue.ls.set('messages', Store.messages, 60 * 60 * 1000);
        },

        'Store.contacts' () {
            Vue.ls.set('contacts', Store.contacts, 60 * 60 * 1000);
        },
    },
};