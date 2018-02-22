export default {
    temp: [],
    page: 0,
    NoMoreItems: false,
    nothingFound: false,
    submissions: [],
    loading: null,

    getChannel(channel_name, set = true) {
        return new Promise((resolve, reject) => {
            // if a guest has landed on a submission page
            if (preload.channel) {
                this.setChannel(preload.channel);
                delete preload.channel;
                resolve();
                return;
            }

            if (
                typeof this.temp.name == 'undefined' ||
                this.temp.name != channel_name
            ) {
                axios
                    .get('/channels', {
                        params: {
                            name: channel_name
                        }
                    })
                    .then((response) => {
                        if (set == true) {
                            this.setChannel(response.data.data);
                            resolve(response);
                        }

                        resolve(response.data.data);
                    })
                    .catch((error) => {
                        reject(error);
                    });
            } else {
                resolve(this.temp);
            }
        });
    },

    setChannel(data) {
        this.temp = data;
    },

    getSubmissions(sort = 'hot', channelName) {
        return new Promise((resolve, reject) => {
            this.page++;
            this.loading = true;

            // if a guest has landed on a channel page
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
                .get('/channels/submissions', {
                    params: {
                        sort: sort,
                        page: this.page,
                        channel_name: channelName
                    }
                })
                .then((response) => {
                    this.submissions = [
                        ...this.submissions,
                        ...response.data.data
                    ];

                    if (!this.submissions.length) this.nothingFound = true;
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
        this.submissions = [];
        this.loading = true;
        this.nothingFound = false;
        this.NoMoreItems = false;
        this.page = 0;
    }
};
