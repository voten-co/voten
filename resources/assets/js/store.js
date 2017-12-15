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
                        this.temp = preload.category;
                        delete preload.category;
                        resolve(this.temp);
                    }

                    if (typeof this.temp.name == "undefined" || this.temp.name != category_name) {
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
                        resolve(this.temp);
                    }
                });
            },

            setCategory(data) {
                this.temp = data;
            },

            getSubmissions(sort = 'hot', categoryName) {
                return new Promise((resolve, reject) => {
                    this.page++;
                    this.loading = true;

                    // if a guest has landed on a category page
                    if (preload.submissions && this.page == 1) {
                        this.submissions = preload.submissions.data;
                        if (!this.submissions.length) this.nothingFound = true;
                        if (preload.submissions.next_page_url == null) this.NoMoreItems = true;
                        this.loading = false;
                        delete preload.submissions;
                        return;
                    }

                    axios.get(auth.isGuest == true ? '/auth/category-submissions' : '/category-submissions', {
                        params: {
                            sort: sort,
                            page: this.page,
                            category: categoryName
                        }
                    }).then((response) => {
                        this.submissions = [...this.submissions, ...response.data.data];

                        if (!this.submissions.length) this.nothingFound = true;
                        if (response.data.next_page_url == null) this.NoMoreItems = true;

                        this.loading = false;

                        resolve(response);
                    }).catch((error) => {
                        this.loading = false; 

                        reject(error);
                    });
                });
            },

            clear() {
                this.submissions = [];
                this.loading = true;
                this.nothingFound = false;
                this.NoMoreItems = false;
                this.page = 0;
            }
        },

        submission: {
            submission: [],
            loadingSubmission: null,

            getSubmission(slug) {
                return new Promise((resolve, reject) => {
                    // if landed on a submission page
                    if (preload.submission) {
                        this.submission = preload.submission;
                        Store.page.category.temp = preload.submission.category;
                        this.loadingSubmission = false;
                        delete preload.submission;
                        return;
                    }

                    axios.get('/get-submission', {
                        params: {
                            slug
                        }
                    }).then((response) => {
                        this.submission = response.data;

                        Store.page.category.temp = response.data.category;

                        this.loadingSubmission = false;

                        resolve(response);
                    }).catch((error) => {
                        this.loadingSubmission = false;

                        reject(error); 
                    });
                }); 
            },

            clearSubmission() {
                this.submission = []; 
                this.loadingSubmission = null; 
            }
        },

        user: {
            temp: [],

            getUser(username, set = true) {
                return new Promise((resolve, reject) => {
                    // If a guest has landed on the user page
                    if (preload.user) {
                        this.submissions.submissions = preload.user;
                        this.temp = preload.user
                        if (this.temp.id == auth.id) auth.stats = this.temp.stats; 
                        delete preload.user;
                        resolve(); 
                    }

                    axios.get('/get-user-store', {
                        params: {
                            username
                        }
                    }).then((response) => {
                        if (response.data.id == auth.id) auth.stats = this.temp.stats; 

                        if (set == true) {
                            this.setUser(response.data);
                            resolve(response);
                        }

                        resolve(response.data);
                    }).catch((error) => {
                        reject(error); 
                    });
                }); 
            }, 

            setUser(data) {
                this.temp = data;
            },

            submissions: {
                NoMoreItems: false,
                submissions: [],
                loading: null,
                page: 0,
                nothingFound: false,
                
                getSubmissions(username) {
                    return new Promise((resolve, reject) => {
                        this.page++; 
                        this.loading = true; 

                        // if a guest has landed on the user page
                        if (preload.submissions && this.$route.name == 'user-submissions' && this.page == 1) {
                            this.submissions = preload.submissions.data;
                            if (!this.submissions.length) this.nothingFound = true; 
                            if (preload.submissions.next_page_url == null) this.NoMoreItems = true; 
                            this.loading = false;
                            delete preload.submissions;

                            return;
                        }

                        axios.get('/user-submissions', {
                            params: {
                                page: this.page,
                                username: username
                            }
                        }).then((response) => {
                            this.submissions = [...this.submissions, ...response.data.data]; 

                            if (response.data.next_page_url == null) this.NoMoreItems = true; 
                            if (this.submissions.length < 1) this.nothingFound = true; 

                            this.loading = false; 

                            resolve(response); 
                        }).catch(error => {
                            this.loading = false; 

                            reject(error); 
                        }); 
                    }); 
                },

                clear() {
                    this.NoMoreItems = false;
                    this.submissions = [];
                    this.loading = null;
                    this.page = 0;
                    this.nothingFound = false;
                }
            },
            
            upVotedSubmissions: {
                NoMoreItems: false,
                submissions: [],
                loading: null,
                page: 0,
                nothingFound: false,
                
                getSubmissions() {
                    return new Promise((resolve, reject) => {
                        this.page++; 
                        this.loading = true; 

                        axios.get('/upvoted-submissions', {
                            params: {
                                page: this.page
                            }
                        }).then((response) => {
                            this.submissions = [...this.submissions, ...response.data.data]; 

                            if (response.data.next_page_url == null) this.NoMoreItems = true; 
                            if (this.submissions.length < 1) this.nothingFound = true; 

                            this.loading = false; 

                            resolve(response); 
                        }).catch(error => {
                            this.loading = false; 

                            reject(error); 
                        }); 
                    }); 
                },

                clear() {
                    this.NoMoreItems = false;
                    this.submissions = [];
                    this.loading = null;
                    this.page = 0;
                    this.nothingFound = false;
                }
            },
            
            downVotedSubmissions: {
                NoMoreItems: false,
                submissions: [],
                loading: null,
                page: 0,
                nothingFound: false,
                
                getSubmissions() {
                    return new Promise((resolve, reject) => {
                        this.page++; 
                        this.loading = true; 

                        axios.get('/downvoted-submissions', {
                            params: {
                                page: this.page
                            }
                        }).then((response) => {
                            this.submissions = [...this.submissions, ...response.data.data]; 

                            if (response.data.next_page_url == null) this.NoMoreItems = true; 
                            if (this.submissions.length < 1) this.nothingFound = true; 

                            this.loading = false; 

                            resolve(response); 
                        }).catch(error => {
                            this.loading = false; 

                            reject(error); 
                        }); 
                    }); 
                },

                clear() {
                    this.NoMoreItems = false;
                    this.submissions = [];
                    this.loading = null;
                    this.page = 0;
                    this.nothingFound = false;
                }
            },
            
            comments: {
                NoMoreItems: false,
                comments: [],
                loading: null,
                page: 0,
                nothingFound: false,
                
                getComments(username) {
                    return new Promise((resolve, reject) => {
                        this.page++; 
                        this.loading = true; 

                        // if a guest has landed on the user page
                        if (preload.comments && this.$route.name == 'user-comments' && this.page == 1) {
                            this.comments = preload.comments.data;
                            if (!this.comments.length) this.nothingFound = true; 
                            if (preload.comments.next_page_url == null) this.NoMoreItems = true; 
                            this.loading = false;
                            delete preload.comments;

                            return;
                        }

                        axios.get('/user-comments', {
                            params: {
                                page: this.page,
                                username: username
                            }
                        }).then((response) => {
                            this.comments = [...this.comments, ...response.data.data]; 

                            if (response.data.next_page_url == null) this.NoMoreItems = true; 
                            if (this.comments.length < 1) this.nothingFound = true; 

                            this.loading = false; 

                            resolve(response); 
                        }).catch(error => {
                            this.loading = false; 

                            reject(error); 
                        }); 
                    }); 
                },

                clear() {
                    this.NoMoreItems = false;
                    this.comments = [];
                    this.loading = null;
                    this.page = 0;
                    this.nothingFound = false;
                }
            },


        },

        home: {
            page: 0,
            NoMoreItems: false,
            nothingFound: false,
            submissions: [],
            loading: null,

            getSubmissions(sort = 'hot') {
                return new Promise((resolve, reject) => {
                    this.page++;
                    this.loading = true;

                    // if landed on the home page as guest
                    if (preload.submissions && app.$route.name == 'home') {
                        this.submissions = preload.submissions.data;
                        if (!this.submissions.length) this.nothingFound = true;
                        if (preload.submissions.next_page_url == null) this.NoMoreItems = true;
                        this.loading = false;
                        delete preload.submissions;
                        return;
                    }

                    axios.get(auth.isGuest == true ? '/auth/home' : '/home', {
                        params: {
                            sort: sort,
                            page: this.page,
                            filter: Store.feedFilter
                        }
                    }).then((response) => {
                        this.submissions = [...this.submissions, ...response.data.data];

                        if (!this.submissions.length) this.nothingFound = true;
                        if (response.data.next_page_url == null) this.NoMoreItems = true;

                        this.loading = false;

                        resolve(response);
                    }).catch((error) => {
                        reject(error);
                    });
                });
            },

            clear() {
                this.page = 0;
                this.nothingFound = false;
                this.NoMoreItems = false;
                this.submissions = [];
                this.loading = true;
            }
        },

        bookmarkedSubmissions: {
            NoMoreItems: false,
            loading: null,
            nothingFound: false,
            submissions: [],
            page: 0,

            getSubmissions() {
                return new Promise((resolve, reject) => {
                    this.page++;
                    this.loading = true;

                    axios.get('/bookmarked-submissions', {
                        params: {
                            page: this.page
                        }
                    }).then((response) => {
                        this.submissions = [...this.submissions, ...response.data.data];

                        if (response.data.next_page_url == null) this.NoMoreItems = true;
                        if (this.submissions.length == 0) this.nothingFound = true;

                        this.loading = false;

                        resolve(response);
                    }).catch(error => {
                        reject(error);
                    });
                });
            },

            clear() {
                this.nothingFound = false;
                this.submissions = [];
                this.loading = null;
                this.page = 0;
                this.NoMoreItems = false;
            },
        },

        bookmarkedComments: {
            NoMoreItems: false,
            loading: null,
            nothingFound: false,
            comments: [],
            page: 0,

            getComments() {
                return new Promise((resolve, reject) => {
                    this.loading = true;
                    this.page++;

                    axios.get('/bookmarked-comments', {
                        params: {
                            page: this.page
                        }
                    }).then((response) => {
                        this.comments = [...this.comments, ...response.data.data];

                        if (response.data.next_page_url == null) this.NoMoreItems = true;
                        if (this.comments.length == 0) this.nothingFound = true;

                        this.loading = false;

                        resolve(response);
                    }).catch((error) => {
                        reject(error);
                    });
                });
            },

            clear() {
                this.NoMoreItems = false;
                this.loading = null;
                this.nothingFound = false;
                this.comments = [];
                this.page = 0;
            },
        },

        bookmarkedCategories: {
            NoMoreItems: false,
            loading: null,
            nothingFound: false,
            page: 0,
            categories: [],

            getCategories() {
                return new Promise((resolve, reject) => {
                    this.page++;
                    this.loading = true;

                    axios.get('/bookmarked-categories', {
                        params: {
                            page: this.page
                        }
                    }).then((response) => {
                        this.categories = [...this.categories, ...response.data.data];

                        if (response.data.next_page_url == null) this.NoMoreItems = true;
                        if (this.categories.length == 0) this.nothingFound = true;

                        this.loading = false;

                        resolve(response);
                    }).catch(error => {
                        reject(error);
                    });
                });
            },

            clear() {
                this.NoMoreItems = false;
                this.loading = null;
                this.nothingFound = false;
                this.page = 0;
                this.categories = [];
            }
        },

        bookmarkedUsers: {
            NoMoreItems: false,
            loading: null,
            nothingFound: false,
            users: [],
            page: 0,

            getUsers() {
                return new Promise((resolve, reject) => {
                    this.loading = true;
                    this.page++;

                    axios.get('/bookmarked-users', {
                        params: {
                            page: this.page
                        }
                    }).then((response) => {
                        this.users = [...this.users, ...response.data.data];

                        if (response.data.next_page_url == null) this.NoMoreItems = true;
                        if (this.users.length == 0) this.nothingFound = true;

                        this.loading = false;

                        resolve(response);
                    }).catch(error => {
                        reject(error);
                    });
                });
            },

            clear() {
                this.NoMoreItems = false;
                this.loading = null;
                this.nothingFound = false;
                this.users = [];
                this.page = 0;
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