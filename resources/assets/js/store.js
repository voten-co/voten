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

        // client-side settings: There's not need to save these settings in the server-side. However, we do synce them to the cloud.
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
        },
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

    ////////////////////////////////////////
    /////////////// Functions //////////////
    ////////////////////////////////////////
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
    }
};