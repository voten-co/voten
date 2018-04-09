export default {
    NoMoreItems: false,
    loading: null,
    nothingFound: false,
    submissions: [],
    page: 0,

    getSubmissions() {
        return new Promise((resolve, reject) => {
            this.page++;
            this.loading = true;

            axios
                .get('/submissions/bookmarked', { params: { page: this.page } })
                .then(response => {
                    this.submissions = [...this.submissions, ...response.data.data];

                    if (response.data.links.next == null) this.NoMoreItems = true;
                    if (this.submissions.length == 0) this.nothingFound = true;

                    this.loading = false;

                    resolve(response);
                })
                .catch(error => {
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
    }
};
