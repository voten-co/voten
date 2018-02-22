import TextSubmission from '../components/submission/TextSubmission.vue';
import LinkSubmission from '../components/submission/LinkSubmission.vue';
import ImgSubmission from '../components/submission/ImgSubmission.vue';
import GifSubmission from '../components/submission/GifSubmission.vue';

export default {
    components: {
        TextSubmission,
        LinkSubmission,
        ImgSubmission,
        GifSubmission
    },

    data() {
        return { hidden: false };
    },

    props: ['list', 'full'],

    computed: {
        upvoted: {
            get() {
                return Store.state.submissions.upVotes.indexOf(this.list.id) !==
                    -1
                    ? true
                    : false;
            },

            set() {
                if (this.currentVote === 'upvote') {
                    this.list.upvotes_count--;
                    let index = Store.state.submissions.upVotes.indexOf(
                        this.list.id
                    );
                    Store.state.submissions.upVotes.splice(index, 1);

                    return;
                }

                if (this.currentVote === 'downvote') {
                    this.list.downvotes_count--;
                    let index = Store.state.submissions.downVotes.indexOf(
                        this.list.id
                    );
                    Store.state.submissions.downVotes.splice(index, 1);
                }

                this.list.upvotes_count++;
                Store.state.submissions.upVotes.push(this.list.id);
            }
        },

        downvoted: {
            get() {
                return Store.state.submissions.downVotes.indexOf(
                    this.list.id
                ) !== -1
                    ? true
                    : false;
            },

            set() {
                if (this.currentVote === 'downvote') {
                    this.list.downvotes_count--;
                    let index = Store.state.submissions.downVotes.indexOf(
                        this.list.id
                    );
                    Store.state.submissions.downVotes.splice(index, 1);

                    return;
                }

                if (this.currentVote === 'upvote') {
                    this.list.upvotes_count--;
                    let index = Store.state.submissions.upVotes.indexOf(
                        this.list.id
                    );
                    Store.state.submissions.upVotes.splice(index, 1);
                }

                this.list.downvotes_count++;
                Store.state.submissions.downVotes.push(this.list.id);
            }
        },

        bookmarked: {
            get() {
                return Store.state.bookmarks.submissions.indexOf(
                    this.list.id
                ) !== -1
                    ? true
                    : false;
            },

            set() {
                if (
                    Store.state.bookmarks.submissions.indexOf(this.list.id) !==
                    -1
                ) {
                    let index = Store.state.bookmarks.submissions.indexOf(
                        this.list.id
                    );
                    Store.state.bookmarks.submissions.splice(index, 1);

                    return;
                }

                Store.state.bookmarks.submissions.push(this.list.id);
            }
        },

        points() {
            let total = this.list.upvotes_count - this.list.downvotes_count;

            if (total < 0) return 0;

            return total;
        },

        /**
         * Does the auth user own the submission
         *
         * @return Boolean
         */
        owns() {
            return auth.id == this.list.author.id;
        },

        /**
         * The current vote type. It's being used to optimize the voing request on the server-side.
         *
         * @return mixed
         */
        currentVote() {
            return this.upvoted ? 'upvote' : this.downvoted ? 'downvote' : null;
        },

        date() {
            return moment(this.list.created_at)
                .utc(moment().format('Z'))
                .fromNow();
        },

        /**
         * Whether or not user wants to see NSFW content's image
         *
         * (Hint: The base idea is that we don't display NSFW content)
         * If the user wants to see NSFW media then return false, like it's not NSFW at all
         * Otherwise return true which means the media must not be displayed.
         * (false: the media will be displayed)
         *
         * @return boolean
         */
        nsfw() {
            return this.list.nsfw && !auth.show_nsfw_media;
        }
    },

    methods: {
        /**
         * Approves the submission. Only the moderators of channel are allowed to do this.
         *
         * @return void
         */
        approve() {
            this.list.approved_at = this.now();

            axios
                .post('/approve-submission', {
                    submission_id: this.list.id
                })
                .catch(() => {
                    this.list.approved_at = null;
                });
        },

        removeThumbnail() {
            this.list.content.thumbnail = null;
            this.list.content.img = null;

            axios.delete(`/submissions/${this.list.id}/thumbnail`);
        },

        /**
         * marks the submission as NSFW (not safe for work)
         *
         * @return void
         */
        markAsSFW() {
            this.list.nsfw = false;

            axios
                .post('/mark-submission-sfw', {
                    id: this.list.id
                })
                .catch(() => {
                    this.list.nsfw = true;
                });
        },

        /**
         * marks the submission as NSFW (not safe for work)
         *
         * @return void
         */
        markAsNSFW() {
            this.list.nsfw = true;

            axios
                .post('/mark-submission-nsfw', {
                    id: this.list.id
                })
                .catch(() => {
                    this.list.nsfw = false;
                });
        },

        voteUp: _.debounce(
            function() {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                axios.post('/upvote-submission', {
                    submission_id: this.list.id,
                    previous_vote: this.currentVote
                });

                if (this.currentVote === 'upvote') {
                    this.upvoted = false;
                    return;
                } else if (this.currentVote === 'downvote') {
                    this.downvoted = false;
                }

                this.upvoted = true;
            },
            200,
            { leading: true, trailing: false }
        ),

        voteDown: _.debounce(
            function() {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                axios.post('/downvote-submission', {
                    submission_id: this.list.id,
                    previous_vote: this.currentVote
                });

                if (this.currentVote === 'downvote') {
                    this.downvoted = false;
                    return;
                } else if (this.currentVote === 'upvote') {
                    this.upvoted = false;
                }

                this.downvoted = true;
            },
            200,
            { leading: true, trailing: false }
        ),

        bookmark: _.debounce(
            function() {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                this.bookmarked = !this.bookmarked;

                axios
                    .post('/bookmark-submission', {
                        id: this.list.id
                    })
                    .catch(() => {
                        this.bookmarked = !this.bookmarked;
                    });
            },
            200,
            { leading: true, trailing: false }
        ),

        report() {
            if (this.isGuest) {
                this.mustBeLogin();
                return;
            }

            Store.modals.reportSubmission.show = true;
            Store.modals.reportSubmission.submission = this.list;
        },

        showPhotoViewer(image) {
            Store.modals.photoViewer.image = image;
            Store.modals.photoViewer.submission = this.list;
            Store.modals.photoViewer.show = true;
        },

        showEmbed() {
            Store.modals.embedViewer.submission = this.list;
            Store.modals.embedViewer.show = true;
        },

        showGifPlayer(gif) {
            Store.modals.gifPlayer.gif = gif;
            Store.modals.gifPlayer.submission = this.list;
            Store.modals.gifPlayer.show = true;
        }
    }
};
