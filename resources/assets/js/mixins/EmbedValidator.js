export default {
    data: function() {
        return {
            allowedDomains: [
                'youtube.com',
                'youtu.be',
                'm.youtube.com',
                'vimeo.com',
                'twitch.tv'
            ]
        };
    },

    computed: {
        isValidSourceForEmbed() {
            return (
                this.allowedDomains.indexOf(this.submission.content.domain) !=
                -1
            );
        }
    }
};
