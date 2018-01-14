import home from './store/home'; 
import channel from './store/channel'; 
import user from './store/user'; 
import submission from './store/submission'; 
import bookmarkedSubmissions from './store/bookmarkedSubmissions'; 
import bookmarkedComments from './store/bookmarkedComments'; 
import bookmarkedChannels from './store/bookmarkedChannels'; 
import subscribedChannels from './store/subscribedChannels'; 
import bookmarkedUsers from './store/bookmarkedUsers'; 

window.Store = {
    // state: data stored in the Store.state gets synced via the LocalStorage;
    // which means it's the same accross all the open windows.
    state: {
        submissions: {
            upVotes: [],
            downVotes: []
        },

        comments: {
            upVotes: [],
            downVotes: []
        },

        bookmarks: {
            submissions: [],
            comments: [],
            channels: [],
            users: []
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

        subscribedChannels: []
    },

    methods: {
        // Mark all notifications as seen.
        seenAllNotifications() {
            axios.post('/notifications/seen').then(() => {
                Store.state.notifications.forEach((element, index) => {
                    if (!element.read_at) {
                        element.read_at = moment()
                            .utc()
                            .format('YYYY-MM-DD HH:mm:ss');
                    }
                });
            });
        }
    },

    page: {
        channel,
        submission,
        user,
        home,
        bookmarkedSubmissions,
        bookmarkedComments,
        bookmarkedChannels,
        bookmarkedUsers,
        subscribedChannels
    },

    // client-side settings: There's not need to save these settings in the server-side.
    // However, we do sync them to the cloud.
    settings: {
        feed: {
            excludeUpvotedSubmissions: false,
            excludeDownvotedSubmissions: true,
            excludeBookmarkedSubmissions: false,
            submissionsFilter: 'subscribed',
            submissionsType: 'All'
        },

        rightSidebar: {
            channelsFilter: 'subscribed',
            channelsLimit: 15,
            showChannelAvatars: true,
            color: 'Gray'
        }
    },

    contentRouter: 'content',
    showPreferences: false,
    showNotifications: false,
    showNewChannelModal: false,
    showFeedbackModal: false,
    showNewSubmissionModal: false,
    showKeyboardShortcutsGuide: false,
    showMarkdownGuide: false,
    showAuthinticationModal: false,

    photoViewer: {
        show: false,
        image: [],
        submission: []
    },
    gifPlayer: {
        show: false,
        gif: [],
        submission: []
    },
    embedViewer: {
        show: false,
        submission: []
    },

    // Open tab's unique ID:
    pageUID:
        '_' +
        Math.random()
            .toString(36)
            .substr(2, 9),

    initialFilled: false
};