import home from './store/home';
import tour from './store/tour';
import channel from './store/channel';
import user from './store/user';
import modals from './store/modals';
import state from './store/state';
import settings from './store/settings';
import methods from './store/methods';
import submission from './store/submission';
import bookmarkedSubmissions from './store/bookmarkedSubmissions';
import bookmarkedComments from './store/bookmarkedComments';
import bookmarkedChannels from './store/bookmarkedChannels';
import subscribedChannels from './store/subscribedChannels';
import bookmarkedUsers from './store/bookmarkedUsers';

window.Store = {
    // state: data stored in the Store.state gets synced via the LocalStorage;
    // which means it's the same accross all the open windows.
    state,

    // general settings that aren't limited to one componenet
    methods,

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
    settings: _.merge(settings, window.clientsideSettings),

    // Set a hash for each modal to make it searchable to see which modal the
    // window.location.hash is pointing to.
    modals,

    // the tour for new new registered users
    tour,

    // is the Store initial filled yet? (it gets filled right after a page reload)
    initialFilled: false
};
