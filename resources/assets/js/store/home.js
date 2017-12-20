export default {
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

            axios.get(auth.isGuest == false ? '/auth/home' : '/home', {
                params: {
                    sort,
                    page: this.page,
                    // filter: Store.feedFilter, 
                    exclude_upvoted_submissions: Store.settings.feed.excludeUpvotedSubmissions, 
                    exclude_downvoted_submissions: Store.settings.feed.excludeDownvotedSubmissions, 
                    types: Store.settings.feed.submissionsTypes
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
    }, 
}