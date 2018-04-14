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
        type() {
            return this.list.type;
        }, 

        liked: {
            get() {
                try {
                    return Store.state.submissions.likes.indexOf(this.list.id) !== -1 ? true : false;
                } catch (error) {
                    return false;
                }
            },

            set() {
                if (this.liked) {
                    this.list.likes_count--;
                    let index = Store.state.submissions.likes.indexOf(this.list.id);
                    Store.state.submissions.likes.splice(index, 1);

                    return;
                }
            
                this.list.likes_count++;
                Store.state.submissions.likes.push(this.list.id);
            }
        },

        bookmarked: {
            get() {
                try {
                    return Store.state.bookmarks.submissions.indexOf(this.list.id) !== -1 ? true : false;
                } catch (error) {
                    return false;
                }
            },

            set() {
                if (Store.state.bookmarks.submissions.indexOf(this.list.id) !== -1) {
                    let index = Store.state.bookmarks.submissions.indexOf(this.list.id);
                    Store.state.bookmarks.submissions.splice(index, 1);

                    return;
                }

                Store.state.bookmarks.submissions.push(this.list.id);
            }
        },

        points() {
            return this.list.likes_count; 
        },

        /**
         * Does the auth user own the submission
         *
         * @return Boolean
         */
        owns() {
            return auth.id == this.list.author.id;
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
            return this.list.nsfw && !Store.settings.feed.include_nsfw_submissions;
        },

        owns() {
            return auth.id === this.list.author.id;  
        }, 

        showApprove() {
            return (
                !this.list.approved_at &&
                (Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 || meta.isVotenAdministrator) &&
                !this.owns
            );
        },

        showDisapprove() {
            return !this.list.disapproved_at && (Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 || meta.isVotenAdministrator) && !this.owns;
        },

        showNSFW() {
            return (
                (this.owns ||
                    Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 ||
                    meta.isVotenAdministrator) &&
                !this.list.nsfw
            );
        },

        showSFW() {
            return (
                (this.owns ||
                    Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 ||
                    meta.isVotenAdministrator) &&
                this.list.nsfw
            );
        },

        showRemoveTumbnail() {
            return this.owns && this.list.content.thumbnail ? true : false;
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
                .post(`/submissions/${this.list.id}/approve`)
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

            axios.delete(`/submissions/${this.list.id}/nsfw`).catch(() => {
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

            axios.post(`/submissions/${this.list.id}/nsfw`).catch(() => {
                this.list.nsfw = false;
            });
        },

        like: _.debounce(
            function() {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }
               
                this.liked =! this.liked; 

                axios.post(`/submissions/${this.list.id}/like`).catch(error => {
                    this.liked =! this.liked; 
                });  
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

                axios.post(`/submissions/${this.list.id}/bookmark`).catch(() => {
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
      
        hide() {
            if (this.isGuest) {
                this.mustBeLogin();
                return;
            }

            this.hidden = true;

            axios.post(`/submissions/${this.list.id}/hide`).catch(() => {
                this.hidden = false;
            });
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
