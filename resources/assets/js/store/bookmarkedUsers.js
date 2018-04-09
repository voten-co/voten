export default {
    NoMoreItems: false,
    loading: null,
    nothingFound: false,
    users: [],
    page: 0,

    getUsers() {
        return new Promise((resolve, reject) => {
            this.loading = true;
            this.page++;

            axios
                .get('/users/bookmarked', { params: { page: this.page } })
                .then(response => {
                    this.users = [...this.users, ...response.data.data];

                    if (response.data.links.next == null) this.NoMoreItems = true;
                    if (this.users.length == 0) this.nothingFound = true;

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
        this.users = [];
        this.page = 0;
    }
};
