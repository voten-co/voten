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
                return;
            }

            axios.get('/get-submission', {
                params: {
                    slug
                }
            }).then((response) => {
                this.submission = response.data;

                Store.page.channel.temp = response.data.channel;

                this.loadingSubmission = false;

                resolve(response);
            }).catch((error) => {
                this.loadingSubmission = false;

                reject(error); 
            });
        }); 
    },

    clearSubmission() {
        this.submission = []; 
        this.loadingSubmission = null; 
    }
}