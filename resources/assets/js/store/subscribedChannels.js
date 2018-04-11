export default {
    NoMoreItems: false,
    loading: null,
    nothingFound: false,
    page: 0,
    channels: [],

    getChannels() {
        return new Promise((resolve, reject) => {
            this.page++;
            this.loading = true;

            axios
                .get('/channels/subscribed', { params: { page: this.page } })
                .then(response => {
                    this.channels = [...this.channels, ...response.data.data];

                    if (response.data.links.next == null) this.NoMoreItems = true;
                    if (this.channels.length == 0) this.nothingFound = true;

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
        this.page = 0;
        this.channels = [];
    }
};
