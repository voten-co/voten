export default {
    NoMoreItems: false,
    loading: null,
    nothingFound: false,
    comments: [],
    page: 0,

    getComments() {
        return new Promise((resolve, reject) => {
            this.loading = true;
            this.page++;

            axios
                .get('/comments/bookmarked', { params: { page: this.page } })
                .then(response => {
                    this.comments = [...this.comments, ...response.data.data];

                    if (response.data.links.next == null) this.NoMoreItems = true;
                    if (this.comments.length == 0) this.nothingFound = true;

                    this.loading = false;

                    resolve(response);
                })
                .catch(error => {
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
    }
};
