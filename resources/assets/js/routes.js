// for administrators only
import AdminPanel from './components/AdminPanel.vue';
import AdminPanelSuggestedChannels from './components/AdminPanelSuggestedChannels.vue';
import AdminPanelInactiveChannels from './components/AdminPanelInactiveChannels.vue';
import AdminPanelSubmissions from './components/AdminPanelSubmissions.vue';
import AdminPanelComments from './components/AdminPanelComments.vue';
import AdminPanelDashboard from './components/AdminPanelDashboard.vue';
import AdminPanelStatistics from './components/AdminPanelStatistics.vue';
import AdminPanelUsers from './components/AdminPanelUsers.vue';

// For moderators only
import ModeratorPanel from './components/ModeratorPanel.vue';
import ModeratorPanelRules from './components/ModeratorPanelRules.vue';
import ModeratorPanelModerators from './components/ModeratorPanelModerators.vue';
import ModeratorPanelBlockDomains from './components/ModeratorPanelBlockDomains.vue';
import ModeratorPanelBanUsers from './components/ModeratorPanelBanUsers.vue';
import ModeratorPanelReportedComments from './components/ModeratorPanelReportedComments.vue';
import ModeratorPanelReportedSubmissions from './components/ModeratorPanelReportedSubmissions.vue';
import ChannelSettings from './components/ChannelSettings.vue';

// static pages
import TermsOfService from './components/pages/TermsOfService.vue';
import PrivacyPolicy from './components/pages/PrivacyPolicy.vue';
import About from './components/pages/About.vue';
import Credits from './components/pages/Credits.vue';

import BookmarkedChannels from './components/BookmarkedChannels.vue';
import SubmissionRedirector from './components/SubmissionRedirector.vue';
import ChannelSubmissions from './components/ChannelSubmissions.vue';
import BookmarkedComments from './components/BookmarkedComments.vue';
import BookmarkedUsers from './components/BookmarkedUsers.vue';
import UserSubmissions from './components/UserSubmissions.vue';
import FindChannels from './components/FindChannels.vue';
import SubmissionPage from './components/SubmissionPage.vue';
import UserComments from './components/UserComments.vue';
import Bookmarks from './components/Bookmarks.vue';
import NotFound from './components/NotFound.vue';
import Home from './components/Home.vue';
import Channel from './components/Channel.vue';
import UserPage from './components/UserPage.vue';
import DeletedSubmissionPage from './components/DeletedSubmissionPage.vue';
import BookmarkedSubmissions from './components/BookmarkedSubmissions.vue';
import UserLikedSubmissions from './components/UserLikedSubmissions.vue';
import SubscribedChannels from './components/SubscribedChannels.vue';

const routes = [
    {
        name: 'home',
        path: '/',
        component: Home
    },

    {
        path: '/tos',
        component: TermsOfService,
        meta: { title: 'Terms Of Service' }
    },
    {
        path: '/privacy-policy',
        component: PrivacyPolicy,
        meta: { title: 'Privacy Policy' }
    },
    { path: '/about', component: About, meta: { title: 'About' } },
    { path: '/credits', component: Credits, meta: { title: 'Credits' } },
    {
        path: '/subscriptions',
        component: SubscribedChannels,
        name: 'subscriptions',
        meta: { title: 'My Subscriptions' }
    },
    {
        path: '/big-daddy',
        component: AdminPanel,
        children: [
            {
                path: '',
                component: AdminPanelDashboard,
                name: 'admin-panel-dashboard'
            },
            {
                path: 'statistics',
                component: AdminPanelStatistics,
                name: 'admin-panel-statistics'
            },
            {
                path: 'users',
                component: AdminPanelUsers,
                name: 'admin-panel-users'
            },
            {
                path: 'submissions',
                component: AdminPanelSubmissions,
                name: 'admin-panel-submissions'
            },
            {
                path: 'comments',
                component: AdminPanelComments,
                name: 'admin-panel-comments'
            },
            {
                path: 'channels/suggested',
                component: AdminPanelSuggestedChannels,
                name: 'admin-panel-suggested-channels'
            },
            {
                path: 'channels/inactive',
                component: AdminPanelInactiveChannels,
                name: 'admin-panel-inactive-channels'
            }
        ]
    },

    {
        path: '/@:username',
        component: UserPage,
        children: [
            { path: '', component: UserSubmissions, name: 'user-submissions' },
            {
                path: 'comments',
                component: UserComments,
                name: 'user-comments'
            },
            {
                path: 'liked-submissions',
                component: UserLikedSubmissions,
                name: 'user-likes'
            }
        ]
    },

    {
        path: '/c/:name/mod/reports',
        redirect: '/c/:name/mod/reports/submissions'
    },
    { path: '/c/:name/mod', redirect: '/c/:name/mod/reports/submissions' },
    {
        path: '/c/:name',
        component: Channel,
        children: [
            {
                path: '',
                component: ChannelSubmissions,
                name: 'channel-submissions'
            },
            {
                path: 'mod',
                component: ModeratorPanel,
                children: [
                    {
                        path: 'reports/submissions',
                        name: 'moderator-panel-reported-submissions',
                        component: ModeratorPanelReportedSubmissions,
                        meta: { title: 'Reports | Submissions' }
                    },
                    {
                        path: 'reports/comments',
                        name: 'moderator-panel-reported-comments',
                        component: ModeratorPanelReportedComments,
                        meta: { title: 'Reports | Comments' }
                    },
                    {
                        path: 'ban-users',
                        name: 'moderator-panel-ban-users',
                        component: ModeratorPanelBanUsers,
                        meta: { title: 'Ban Users | Moderator Panel' }
                    },
                    {
                        path: 'block-domains',
                        name: 'moderator-panel-block-domains',
                        component: ModeratorPanelBlockDomains,
                        meta: { title: 'Block Domains | Moderator Panel' }
                    },
                    {
                        path: 'rules',
                        name: 'moderator-panel-rules',
                        component: ModeratorPanelRules,
                        meta: { title: 'Rules | Moderator Panel' }
                    },
                    {
                        path: 'manage-moderators',
                        name: 'moderator-panel-manage-moderators',
                        component: ModeratorPanelModerators,
                        meta: { title: 'Manage Moderators | Moderator Panel' }
                    },
                    {
                        path: 'settings',
                        name: 'channel-settings',
                        component: ChannelSettings,
                        meta: { title: 'Settings | Moderator Panel' }
                    }
                ]
            }
        ]
    },

    { path: '/deleted-submission', component: DeletedSubmissionPage },
    { path: '/submission/:id', component: SubmissionRedirector },
    {
        path: '/discover-channels',
        component: FindChannels,
        name: 'discover-channels',
        meta: { title: 'Discover Channels' }
    },
    {
        path: '/404',
        component: NotFound,
        name: 'not-found',
        meta: { title: 'Not Found' }
    },
    {
        path: '/c/:name/:slug',
        component: SubmissionPage,
        name: 'submission-page'
    },

    { path: '/bookmarks', redirect: '/bookmarks/submissions' },
    {
        name: 'bookmarks',
        path: '/bookmarks',
        component: Bookmarks,
        children: [
            {
                path: 'submissions',
                component: BookmarkedSubmissions,
                name: 'bookmarked-submissions',
                meta: { title: 'Submissions | Bookmarks' }
            },
            {
                path: 'comments',
                component: BookmarkedComments,
                name: 'bookmarked-comments',
                meta: { title: 'Comments | Bookmarks' }
            },
            {
                path: 'channels',
                component: BookmarkedChannels,
                name: 'bookmarked-channels',
                meta: { title: 'Channels | Bookmarks' }
            },
            {
                path: 'users',
                component: BookmarkedUsers,
                name: 'bookmarked-users',
                meta: { title: 'Users | Bookmarks' }
            }
        ]
    },
    { path: '/home', redirect: '/' },
    { path: '*', component: NotFound, meta: { title: 'Not Found' } }
];

import VueRouter from 'vue-router';
Vue.use(VueRouter);

var router = new VueRouter({
    mode: 'history',
    routes
});

// scroll behavior
const scrollableElementId = 'submissions';
const scrollPositions = Object.create(null);

/**
 * Fills the <title> tag with the right value before navigating to the
 * new route.First it checks the title in the meta, if it exists it
 * sets it to that, otherwise sets it to the default (voten).
 */
router.beforeEach((to, from, next) => {
    // scroll behavior
    let element = document.getElementById(scrollableElementId);

    if (element !== null) {
        scrollPositions[from.name] = element.scrollTop;
    }

    // page title
    if (to.meta.title) {
        document.title = to.meta.title;
    } else {
        if (
            to.name != 'submission-page' &&
            to.name != 'channel' &&
            to.name != 'home' &&
            to.name != 'user-submissions' &&
            to.name != 'user-comments'
        ) {
            document.title = 'Voten';
        }
    }

    next();
});

// scroll behavior
window.addEventListener('popstate', () => {
    let currentRouteName = router.history.current.name;

    let element = document.getElementById(scrollableElementId);

    if (element !== null && currentRouteName in scrollPositions) {
        setTimeout(
            () => (element.scrollTop = scrollPositions[currentRouteName]),
            50
        );
    }
});

/**
 * Since Google Analytics's default tracking code doesn't play nice with
 * single-page-applications, we're gonna use this one. What it does
 * is simply running after navigating to each new route.
 */
router.afterEach((to, from) => {
    if (Laravel.env == 'production') {
        (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            (i[r] =
                i[r] ||
                function() {
                    (i[r].q = i[r].q || []).push(arguments);
                }),
                (i[r].l = 1 * new Date());
            (a = s.createElement(o)), (m = s.getElementsByTagName(o)[0]);
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m);
        })(
            window,
            document,
            'script',
            'https://www.google-analytics.com/analytics.js',
            'ga'
        );

        ga('create', 'UA-89431807-1', 'auto');

        ga('set', 'page', to.path);
        ga('send', 'pageview');
    }
});

export default router;
