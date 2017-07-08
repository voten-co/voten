require('./bootstrap')

/**
 * This is our global Store, we save so many stuff in it so that we don't have to duplicate data.
 * We might wanna refactor this to use Vuex instead, but untill this moment I don't see why!!!
 * The store gets filled by main vue-instance's ajax call at first load of the application.
 *
 * @type object
 */
window.Store = {
    category: [],
    user: [],

    submissionUpVotes: [],
    submissionDownVotes: [],

    commentUpVotes: [],
    commentDownVotes: [],

    submissionBookmarks: [],
    commentBookmarks: [],
    categoryBookmarks: [],
    userBookmarks: [],

    blockedUsers: [],

    moderatingCategories: [],
    moderatorAt: [],
    administratorAt: [],
    moderatingAt: [], // contains both moderator and administrator
    subscribedAt: [],

    notifications: [],
    contacts: [],

    contentRouter: 'content',
    feedFilter: '',
    sidebarFilter: '',

    subscribedCategories: [],

    loading: true,
}

require('./vue-codes');
