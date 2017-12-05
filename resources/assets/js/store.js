window.Store = {
    // state: data stored in the Store.state gets synced via the LocalStorage; which means it's the same accross all the open windows.
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

        // client-side settings: There's not need to save these settings in the server-side. However, we do sync them to the cloud.
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
        category: {
            temp: [],
            page: 0,
            NoMoreItems: false,
            nothingFound: false,
            submissions: [],
            loading: null,

            getCategory(category_name, set = true) {
                return new Promise((resolve, reject) => {
                    // if a guest has landed on a submission page
                    if (preload.category && preload.category.name == app.$route.params.name) {
                        Store.page.category.temp = preload.category;
                        delete preload.category;
                        resolve(Store.page.category.temp);
                    }

                    if (typeof Store.page.category.temp.name == "undefined" || Store.page.category.temp.name != category_name) {
                        axios.get('/get-category-store', {
                            params: {
                                name: category_name
                            }
                        }).then((response) => {
                            if (set == true) {
                                this.setCategory(response.data);
                                resolve(response);
                            } 

                            resolve(response.data);
                        }).catch((error) => {
                            reject(error);
                        });
                    } else {
                        resolve(Store.page.category.temp);
                    }
                });
            },

            setCategory(data) {
                Store.page.category.temp = data;
            }, 

            getSubmissions(sort = 'hot', categoryName) {
                return new Promise((resolve, reject) => {
                    Store.page.category.page++;
                    Store.page.category.loading = true;

                    // if a guest has landed on a category page
                    if (preload.submissions && Store.page.category.page == 1) {
                        Store.page.category.submissions = preload.submissions.data;
                        if (! Store.page.category.submissions.length) Store.page.category.nothingFound = true;
                        if (preload.submissions.next_page_url == null) Store.page.category.NoMoreItems = true;
                        Store.page.category.loading = false;
                        delete preload.submissions;
                        return;
                    }

                    axios.get(auth.isGuest == true ? '/auth/category-submissions' : '/category-submissions', {
                        params: {
                            sort: sort,
                            page: Store.page.category.page,
                            category: categoryName
                        }
                    }).then((response) => {
                        Store.page.category.submissions = [...Store.page.category.submissions, ...response.data.data];

                        if (! Store.page.category.submissions.length) Store.page.category.nothingFound = true;
                        if (response.data.next_page_url == null) Store.page.category.NoMoreItems = true;

                        Store.page.category.loading = false;

                        resolve(response);
                    }).catch((error) => {
                        reject(error);
                    });
                });
            }, 

            clear() {
                Store.page.category.submissions = [];
                Store.page.category.loading = true;
                Store.page.category.nothingFound = false;
                Store.page.category.NoMoreItems = false;
                Store.page.category.page = 0;
            }
        },
        submission: [],
        user: [],
        home: {
            page: 0,
            NoMoreItems: false,
            nothingFound: false,
            submissions: [],
            loading: null,

            getSubmissions(sort = 'hot') {
                return new Promise((resolve, reject) => {
                    Store.page.home.page++;
                    Store.page.home.loading = true;

                    // if landed on the home page as guest
                    if (preload.submissions && app.$route.name == 'home') {
                        Store.page.home.submissions = preload.submissions.data;
                        if (!Store.page.home.submissions.length) Store.page.home.nothingFound = true;
                        if (preload.submissions.next_page_url == null) Store.page.home.NoMoreItems = true;
                        Store.page.home.loading = false;
                        delete preload.submissions;
                        return;
                    }

                    axios.get(auth.isGuest == true ? '/auth/home' : '/home', {
                        params: {
                            sort: sort,
                            page: Store.page.home.page,
                            filter: Store.feedFilter
                        }
                    }).then((response) => {
                        Store.page.home.submissions = [...Store.page.home.submissions, ...response.data.data];

                        if (!Store.page.home.submissions.length) Store.page.home.nothingFound = true;
                        if (response.data.next_page_url == null) Store.page.home.NoMoreItems = true;

                        Store.page.home.loading = false;

                        resolve(response);
                    }).catch((error) => {
                        reject(error);
                    });
                });
            }, 

            clear() {
                Store.page.home.page = 0;
                Store.page.home.nothingFound = false;
                Store.page.home.NoMoreItems = false;
                Store.page.home.submissions = [];
                Store.page.home.loading = true;
            }
        }
    },

    contentRouter: 'content',
    feedFilter: '',
    sidebarFilter: '',

    // Open tabs unique ID:
    pageUID: '_' + Math.random().toString(36).substr(2, 9),

    initialFilled: false,
};