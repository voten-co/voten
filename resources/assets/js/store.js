import home from './store/home'; 
import channel from './store/channel'; 
import user from './store/user'; 
import submission from './store/submission'; 
import bookmarkedSubmissions from './store/bookmarkedSubmissions'; 
import bookmarkedComments from './store/bookmarkedComments'; 
import bookmarkedChannels from './store/bookmarkedChannels'; 
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
            channels: [],
            users: [],
        },

        blocks: {
            users: [],
            channels: []
        },

        moderatingChannels: [],
        bookmarkedChannels: [],
        moderatorAt: [],
        administratorAt: [],
        moderatingAt: [], // contains both moderator and administrator
        subscribedAt: [],

        notifications: [],
        messages: [],
        contacts: [],

        subscribedChannels: [],
    },

    page: {
        channel, 
        submission,
        user,
        home, 
        bookmarkedSubmissions,
        bookmarkedComments,
        bookmarkedChannels,
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
            channelsFilter: 'subscribed', 
            channelsLimit: 10, 
            showChannelAvatars: true, 
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