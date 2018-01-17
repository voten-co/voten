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

    // Set a hash for each modal to make it searchable to see which modal the
    // window.location.hash is pointing to.
    modals: {
        preferences: {
            show: false,
            hash: 'preferences'
        },
        notifications: {
            show: false,
            hash: 'notifications'
        },
        newChannel: {
            show: false,
            hash: 'newChannel'
        },
        feedback: {
            show: false,
            hash: 'feedback'
        },
        newSubmission: {
            show: false,
            hash: 'newSubmission'
        },
        keyboardShortcutsGuide: {
            show: false,
            hash: 'keyboardShortcutsGuide'
        },
        markdownGuide: {
            show: false,
            hash: 'markdownGuide'
        },
        authintication: {
            show: false,
            hash: 'authintication'
        },
        reportSubmission: {
            show: false,
            hash: 'reportSubmission',
            submission: []
        },
        reportComment: {
            show: false,
            hash: 'reportComment',
            comment: []
        },
        feedSettings: {
            show: false,
            hash: 'feedSettings'
        },
        sidebarSettings: {
            show: false,
            hash: 'sidebarSettings'
        },
        photoViewer: {
            show: false,
            hash: 'photoViewer',
            image: [],
            submission: []
        },
        gifPlayer: {
            show: false,
            hash: 'gifPlayer',
            gif: [],
            submission: []
        },
        embedViewer: {
            show: false,
            hash: 'embedViewer',
            submission: []
        },
        messages: {
            show: false,
            hash: 'messages'
        }, 
        search: {
            show: false,
            hash: 'search'
        }
    },

    // Open tab's unique ID:
    pageUID:
        '_' +
        Math.random()
            .toString(36)
            .substr(2, 9),

    initialFilled: false
};
