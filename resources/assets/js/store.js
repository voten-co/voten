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
            categories: [],
            users: [],
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
                submissionsFilter: null,
            },

            commentForm: {
                sendOnEnter: true,
            },

            sidebar: {
                categoriesFilter: null
            }
        }
    },

    page: {
        category: [],
        submission: [],
        user: [],
        home: {
            page: 0,
            NoMoreItems: false,
            nothingFound: false,
            submissions: [],
            loading: null,
        }
    },

    contentRouter: 'content',
    feedFilter: '',
    sidebarFilter: '',

    // Open tabs unique ID:
    pageUID: '_' + Math.random().toString(36).substr(2, 9),

    initialFilled: false,
};