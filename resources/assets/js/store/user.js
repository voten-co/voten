export default {
    temp: [],

    getUser(username, set = true) {
        return new Promise((resolve, reject) => {
            // If a guest has landed on the user page
            if (preload.user) {
                this.setUser(preload.user);
                delete preload.user;
                resolve();
                return;
            }

            axios
                .get('/users', {
                    params: {
                        username,
                        with_info: 1,
                        with_stats: 1
                    }
                })
                .then((response) => {
                    if (response.data.data.id == auth.id)
                        auth.stats = this.temp.stats;

                    if (set == true) {
                        this.setUser(response.data.data);
                        resolve(response);
                    }

                    resolve(response.data.data);
                })
                .catch((error) => {
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
                if (preload.submissions && this.page == 1) {
                    this.submissions = preload.submissions.data;
                    if (!this.submissions.length) this.nothingFound = true;
                    if (preload.submissions.next_page_url == null)
                        this.NoMoreItems = true;
                    this.loading = false;
                    delete preload.submissions;
                    resolve();
                    return;
                }

                axios
                    .get('/user-submissions', {
                        params: {
                            page: this.page,
                            username: username
                        }
                    })
                    .then((response) => {
                        this.submissions = [
                            ...this.submissions,
                            ...response.data.data
                        ];

                        if (this.submissions.length < 1)
                            this.nothingFound = true;
                        if (response.data.links.next == null)
                            this.NoMoreItems = true;

                        this.loading = false;

                        resolve(response);
                    })
                    .catch((error) => {
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

    likedSubmissions: {
        NoMoreItems: false,
        submissions: [],
        loading: null,
        page: 0,
        nothingFound: false,

        getSubmissions() {
            return new Promise((resolve, reject) => {
                this.page++;
                this.loading = true;

                axios
                    .get('/auth/submissions/liked', {
                        params: { page: this.page }
                    })
                    .then((response) => {
                        this.submissions = [
                            ...this.submissions,
                            ...response.data.data
                        ];

                        if (response.data.links.next == null)
                            this.NoMoreItems = true;
                        if (this.submissions.length < 1)
                            this.nothingFound = true;

                        this.loading = false;

                        resolve(response);
                    })
                    .catch((error) => {
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
                if (preload.comments && this.page == 1) {
                    this.comments = preload.comments.data;
                    if (!this.comments.length) this.nothingFound = true;
                    if (preload.comments.next_page_url == null)
                        this.NoMoreItems = true;
                    this.loading = false;
                    delete preload.comments;
                    resolve();
                    return;
                }

                axios
                    .get('/user-comments', {
                        params: { page: this.page, username: username }
                    })
                    .then((response) => {
                        this.comments = [
                            ...this.comments,
                            ...response.data.data
                        ];

                        if (response.data.links.next == null)
                            this.NoMoreItems = true;
                        if (this.comments.length < 1) this.nothingFound = true;

                        this.loading = false;

                        resolve(response);
                    })
                    .catch((error) => {
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
    }
};
