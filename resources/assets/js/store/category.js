export default {
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

            axios.get(auth.isGuest == false ? '/auth/category-submissions' : '/category-submissions', {
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
}