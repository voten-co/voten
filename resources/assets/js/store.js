import home from './store/home'; 
import category from './store/category'; 
import user from './store/user'; 
import submission from './store/submission'; 
import bookmarkedSubmissions from './store/bookmarkedSubmissions'; 
import bookmarkedComments from './store/bookmarkedComments'; 
import bookmarkedCategories from './store/bookmarkedCategories'; 
import bookmarkedUsers from './store/bookmarkedUsers'; 

window.Store = {
    // state: data stored in the Store.state gets synced via the LocalStorage; 
    // which means it's the same accross all the open windows.
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
        bookmarkedCategories: [],
        moderatorAt: [],
        administratorAt: [],
        moderatingAt: [], // contains both moderator and administrator
        subscribedAt: [],

        notifications: [],
        messages: [],
        contacts: [],

        subscribedCategories: [],
    },

    page: {
        category, 
        submission,
        user,
        home, 
        bookmarkedSubmissions,
        bookmarkedComments,
        bookmarkedCategories,
        bookmarkedUsers
    },


    // client-side settings: There's not need to save these settings in the server-side. 
    // However, we do sync them to the cloud.
    settings: {
        feed: {
            excludeUpvotedSubmissions: false,
            excludeDownvotedSubmissions: true,
            submissionsFilter: '',
            submissionsTypes: ['All']
        },

        rightSidebar: {
            categoriesFilter: 'subscribed', 
            categoriesLimit: 10, 
            showCategoryAvatars: true, 
            color: 'Gray'
        }
    }, 

    contentRouter: 'content',
    feedFilter: '',
    sidebarFilter: '',


    // Open tab's unique ID:
    pageUID: '_' + Math.random().toString(36).substr(2, 9),

    initialFilled: false,
};