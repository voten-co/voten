export default {
    submission: [],
    loadingSubmission: null,

    getSubmission(slug) {
        return new Promise((resolve, reject) => {
            // if landed on a submission page
            if (preload.submission) {
                this.submission = preload.submission;
                Store.page.channel.temp = preload.submission.channel;
                this.loadingSubmission = false;
                delete preload.submission;
                resolve();
                return;
            }

            axios
                .get('/submissions', {
                    params: {
                        slug, 
                        with_channel: 1 
                    }
                })
                .then(response => {
                    this.submission = response.data.data;

                    Store.page.channel.temp = response.data.data.channel;

                    this.loadingSubmission = false;

                    resolve(response.data);
                })
                .catch(error => {
                    this.loadingSubmission = false;

                    reject(error);
                });
        });
    },

    clearSubmission() {
        this.submission = [];
        this.loadingSubmission = null;
    }
};
