export default {
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
}