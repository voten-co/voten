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
            if (typeof preload != 'undefined' && preload.submissions) {
                this.submissions = preload.submissions.data;
                if (!this.submissions.length) this.nothingFound = true;
                if (preload.submissions.next_page_url == null) this.NoMoreItems = true;
                this.loading = false;
                delete preload.submissions;
                resolve();
                return;
            }

            axios
                .get('/feed', {
                    params: {
                        sort,
                        page: this.page,
                        filter: Store.settings.feed.submissionsFilter,
                        exclude_liked_submissions: Store.settings.feed.excludeLikedSubmissions ? 1 : 0,
                        include_nsfw_submissions: Store.settings.feed.include_nsfw_submissions ? 1 : 0,
                        exclude_bookmarked_submissions: Store.settings.feed.excludeBookmarkedSubmissions ? 1 : 0,
                        submissions_type: Store.settings.feed.submissionsType
                    }
                })
                .then(response => {
                    this.submissions = [...this.submissions, ...response.data.data];

                    if (!this.submissions.length) this.nothingFound = true;
                    if (response.data.links.next == null) this.NoMoreItems = true;

                    this.loading = false;

                    resolve(response);
                })
                .catch(error => {
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
};
