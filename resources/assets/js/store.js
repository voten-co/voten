window.Store = {
    // state data gets synced via the LocalStorage; which means it's the same accross all the open windows.
    state: {
        submissions: {
            upVotes: [],
            downVotes: [],
        },

        comments: {
            upVotes: [],
            downVotes: [],
        },

        bookmarks: {
            submissions: [],
            comments: [],
            categoryBookmarks: [],
            userBookmarks: [],
        },

        blocks: {
            users: [],
            categories: []
        },

        moderatingCategories: [],
        moderatorAt: [],
        administratorAt: [],
        moderatingAt: [], // contains both moderator and administrator
        subscribedAt: [],

        notifications: [],
        messages: [],
        contacts: [],

        subscribedCategories: [],

        // client-side settings. There's not need to save these settings in the server-side. However, we do synce them to the cloud.
        settings: {
            feed: {
                excludeUpvotedSubmissions: false,
                excludeDownvotedSubmissions: false,
                filter: null,
            },

            commentForm: {
                sendOnEnter: true,
            }
        }
    },

    page: {
        category: [],
        submission: [],
        user: [],
    },

    contentRouter: 'content',
    feedFilter: '',
    sidebarFilter: '',

    // Open tabs unique ID:
    pageUID: '_' + Math.random().toString(36).substr(2, 9),

    initialFilled: false,
};