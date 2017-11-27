<template>
    <transition name="fade">
        <div class="submission-wrapper" v-show="!hidden">
            <article class="flex1" v-bind:class="'box-typical profile-post ' + list.type">
                <!-- header -->
                <div class="submission-header user-select">
                    <div class="submission-header-container">
                        <div class="submission-submitter-wrapper">
                            <router-link :to="'/' + '@' + list.owner.username" class="desktop-only">
                                <img v-bind:src="list.owner.avatar" v-bind:alt="list.owner.username"
                                     class="submission-avatar">
                            </router-link>

                            <div class="submission-submitter">
                                <router-link :to="'/' + '@' + list.owner.username" class="username">
                                    @{{ list.owner.username }}
                                </router-link>

                                <el-tooltip :content="'Created: ' + longDate" placement="bottom-start" transition="false" :open-delay="500">
                                    <span class="date">
                                        {{ date }}
                                    </span>
                                </el-tooltip>
                            </div>
                        </div>

                        <div class="flex-center">
                            <div>
                                <el-tooltip content="Edit" placement="bottom" transition="false" :open-delay="500">
                                    <a class="reply" v-if="owns && (list.type == 'text')" @click="edit">
                                        <i class="v-icon v-edit go-gray h-purple pointer"></i>
                                    </a>
                                </el-tooltip>
                            </div>

                            <div class="voting-wrapper display-none">
                                <a class="fa-stack align-right" @click="voteUp">
                                    <i class="v-icon v-up-fat" :class="upvoted ? 'go-primary animated bounceIn' : 'go-gray'"></i>
                                </a>

                                <el-tooltip :content="detailedPoints" placement="bottom" transition="false" :open-delay="500">
                                    <div class="detail">
                                        {{ points }} Points
                                    </div>
                                </el-tooltip>

                                <a class="fa-stack align-right" @click="voteDown">
                                    <i class="v-icon v-down-fat" :class="downvoted ? 'go-red animated bounceIn' : 'go-gray'"></i>
                                </a>
                            </div>

                            <div class="margin-left-1">
                                <el-dropdown size="medium" type="primary" trigger="click" :show-timeout="0" :hide-timeout="0">
                                    <i class="el-icon-more-outline"></i>

                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item v-if="!owns" @click.native="report">
                                            Report
                                        </el-dropdown-item>

                                        <el-dropdown-item @click.native="hide" v-if="!owns">
                                            Hide
                                        </el-dropdown-item>

                                        <el-dropdown-item @click.native="markAsNSFW" v-if="showNSFW">
                                            NSFW
                                        </el-dropdown-item>

                                        <el-dropdown-item @click.native="markAsSFW" v-if="showSFW">
                                            Family Safe
                                        </el-dropdown-item>

                                        <el-dropdown-item class="go-red" @click.native="destroy" v-if="owns">
                                            Delete
                                        </el-dropdown-item>

                                        <el-dropdown-item class="go-green" @click.native="approve" v-if="showApprove"
                                                          divided>
                                            Approve
                                        </el-dropdown-item>

                                        <el-dropdown-item class="go-red" @click.native="disapprove"
                                                          v-if="showDisapprove">
                                            Delete
                                        </el-dropdown-item>

                                        <el-dropdown-item class="go-red" @click.native="removeThumbnail"
                                                          v-if="showRemoveTumbnail">
                                            Remove Thumbnail
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>

                                <el-tooltip :content="bookmarked ? 'Unbookmark' : 'Bookmark'" placement="bottom-end" transition="false" :open-delay="500">
                                    <a class="fa-stack" @click="bookmark">
                                        <i class="v-icon h-yellow" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
                                    </a>
                                </el-tooltip>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- content -->
                <div class="profile-post-content">
                    <text-submission v-if="list.type == 'text'" :submission="list" :nsfw="nsfw"
                                     :full="full"></text-submission>

                    <img-submission v-if="list.type == 'img'" :submission="list" :nsfw="nsfw" :full="full"
                                    @zoom="showPhotoViewer"
                    ></img-submission>

                    <gif-submission v-if="list.type == 'gif'" :submission="list" :nsfw="nsfw" :full="full"
                                    @play-gif="showGifPlayer"
                    ></gif-submission>

                    <link-submission v-if="list.type == 'link'" :submission="list" :nsfw="nsfw" :full="full"
                                     @embed="showEmbed"
                    ></link-submission>
                </div>

                <photo-viewer v-if="photoViewer" :bookmarked="bookmarked" :points="points" @close="closeViwer"
                              :list="list" :photoindex="photoViewerIndex"
                              :upvoted="upvoted" :downvoted="downvoted" @bookmark="bookmark" @upvote="voteUp"
                              @downvote="voteDown"
                ></photo-viewer>

                <embed-viewer v-if="embedViewer" :bookmarked="bookmarked" :points="points" @close="closeEmbed"
                              :list="list"
                              :upvoted="upvoted" :downvoted="downvoted" @bookmark="bookmark" @upvote="voteUp"
                              @downvote="voteDown"
                ></embed-viewer>

                <gif-player v-if="gifPlayer" :bookmarked="bookmarked" :points="points" @close="closeGifPlayer"
                            :list="list"
                            :upvoted="upvoted" :downvoted="downvoted" @bookmark="bookmark" @upvote="voteUp"
                            @downvote="voteDown"
                ></gif-player>

                <report-submission :submission="list" :visible.sync="showReportModal" v-if="showReportModal"></report-submission>
            </article>
        </div>
    </transition>
</template>

<script>
    import TextSubmission from '../components/submission/TextSubmission.vue';
    import ReportSubmission from '../components/ReportSubmission.vue';
    import LinkSubmission from '../components/submission/LinkSubmission.vue';
    import ImgSubmission from '../components/submission/ImgSubmission.vue';
    import GifSubmission from '../components/submission/GifSubmission.vue';
    import PhotoViewer from '../components/PhotoViewer.vue';
    import EmbedViewer from '../components/Embed.vue';
    import GifPlayer from '../components/GifPlayer.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        props: ['list', 'full'],

        mixins: [Helpers],

        components: {
            ReportSubmission,
            TextSubmission,
            LinkSubmission,
            ImgSubmission,
            GifSubmission,
            PhotoViewer,
            EmbedViewer,
            GifPlayer,
        },

        data () {
            return {
                bookmarked: false,
                upvoted: false,
                downvoted: false,
                hidden: false,
                showReportModal: false,
                photoViewerIndex: null,
                photoViewer: false,
                embedViewer: false,
                gifPlayer: false,
                auth,
                Store
            }
        },

        created () {
            this.setBookmarked();
            this.setVoteds();
            this.$eventHub.$on('photo-viewer', this.showPhotoViewer);
            this.$eventHub.$on('scape', this.closeViwer);
        },

        watch: {
            // call again the method if the route changes
            '$route' () {
                this.setBookmarked();
                this.setVoteds();
            },

            'Store.state.submissions.upVotes' () {
                this.setVoteds();
            },

            'Store.state.submissions.downVotes' () {
                this.setVoteds();
            },

            'Store.state.bookmarks.submissions' () {
                this.setBookmarked();
            },
        },


        computed: {
            points() {
                let total = this.list.upvotes - this.list.downvotes;
                if (total < 0) return 0;
                return total;
            },

            detailedPoints() {
                return `+${this.list.upvotes} | -${this.list.downvotes}`;
            },

            /**
             * Does the auth user own the submission
             *
             * @return Boolean
             */
            owns() {
                return auth.id == this.list.owner.id;
            },

            showApprove() {
                return !this.list.approved_at && Store.state.moderatingAt.indexOf(this.list.category_id) != -1 && !this.owns;
            },

            showDisapprove() {
                return !this.list.deleted_at && Store.state.moderatingAt.indexOf(this.list.category_id) != -1 && !this.owns;
            },

            showNSFW() {
                return (this.owns || Store.state.moderatingAt.indexOf(this.list.category_id) != -1) && !this.list.nsfw;
            },

            showSFW() {
                return (this.owns || Store.state.moderatingAt.indexOf(this.list.category_id) != -1) && this.list.nsfw;
            },

            showRemoveTumbnail() {
                return this.owns && this.list.data.thumbnail ? true : false;
            },

            /**
             * Whether or not user wants to see NSFW content's image.
             *
             * (Hint: The base idea is that we don't display NSFW content)
             * If the user wants to see NSFW media then return false, like it's not NSFW at all
             * Otherwise return true which means the media must not be displayed.
             * (false: the media will be displayed)
             *
             * @return boolean
             */
            nsfw() {
                return this.list.nsfw && !auth.nsfwMedia
            },

            /**
             * The current vote type. It's being used to optimize the voing request on the server-side.
             *
             * @return mixed
             */
            currentVote () {
                if (this.upvoted)
                    return "upvote";

                if (this.downvoted)
                    return "downvote";

                return null;
            },

            date () {
                return moment(this.list.created_at).utc(moment().format("Z")).fromNow();
            },

            /**
             * Calculates the long date to display for hover over date.
             *
             * @return String
             */
            longDate() {
                return this.parseFullDate(this.list.created_at);
            },
        },

        methods: {
            /**
             * Fires the "submission-edit" event that gets picked up by the TextSubmission.vue component.
             *
             * @return void
             */
            edit() {
                this.$eventHub.$emit('edit-submission');
            },

            /**
             * Removes the thumbnail
             *
             * @return
             */
            removeThumbnail() {
                this.list.data.thumbnail = null;
                this.list.data.img = null;

                axios.post('/remove-thumbnail', {
                    id: this.list.id
                });
            },

            /**
             * marks the submission as NSFW (not safe for work)
             *
             * @return void
             */
            markAsNSFW() {
                axios.post('/mark-submission-nsfw', {
                    id: this.list.id
                }).then((response) => {
                    this.list.nsfw = true;
                })
            },

            /**
             * marks the submission as NSFW (not safe for work)
             *
             * @return void
             */
            markAsSFW() {
                axios.post('/mark-submission-sfw', {
                    id: this.list.id
                }).then(() => {
                    this.list.nsfw = false;
                });
            },

            /**
             * whether or not the user has voted on submission
             *
             * @return void
             */
            setVoteds () {
                if (Store.state.submissions.upVotes.indexOf(this.list.id) != -1) {
                    this.upvoted = true;
                    this.downvoted = false;
                    return;
                }

                if (Store.state.submissions.downVotes.indexOf(this.list.id) != -1) {
                    this.downvoted = true;
                    this.upvoted = false;
                    return;
                }

                this.downvoted = false;
                this.upvoted = false;
            },

            /**
             * Whether or not user has bookmarked the submission
             *
             * @return void
             */
            setBookmarked () {
                if (Store.state.bookmarks.submissions.indexOf(this.list.id) != -1) {
                    this.bookmarked = true;
                } else {
                    this.bookmarked = false;
                }
            },

            /**
             * Toggles the submission into bookmarks
             *
             * @return void
             */
            bookmark (submission) {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                this.bookmarked = !this.bookmarked

                axios.post('/bookmark-submission', {
                    id: this.list.id
                }).then(() => {
                    if (Store.state.bookmarks.submissions.indexOf(this.list.id) != -1) {
                        let index = Store.state.bookmarks.submissions.indexOf(this.list.id);
                        Store.state.bookmarks.submissions.splice(index, 1);

                        return;
                    }

                    Store.state.bookmarks.submissions.push(this.list.id);
                })
            },

            /**
             * hide(block) submission
             *
             * @return void
             */
            hide() {
                axios.post('/hide-submission', { submission_id: this.list.id }).then(() => {
                    this.$message({
                        message: 'You will no longer see this post in your feed.',
                        type: 'success'
                    });
                });

                history.go(-1);
            },

            /**
             * Deletes the submission. Only the owner is allowed to make such decision.
             *
             * @return void
             */
            destroy() {
                axios.post('/destroy-submission', { id: this.list.id }).then(() => {
                    this.$message({
                        message: 'Post was successfully deleted.',
                        type: 'success'
                    });
                });

                if (this.full) {
                    history.go(-1);
                } else {
                    this.hidden = true;
                }
            },

            /**
             * Approves the submission. Only the moderators of category are allowed to do this.
             *
             * @return void
             */
            approve() {
                axios.post('/approve-submission', {
                    submission_id: this.list.id
                }).then(() => {
                    this.list.approved_at = moment().utc().format('YYYY-MM-DD HH:mm:ss');
                })
            },

            /**
             * Disapproves the submission. Only the moderators of category are allowed to do this.
             *
             * @return void
             */
            disapprove(){
                axios.post('/disapprove-submission', {
                    submission_id: this.list.id
                }).then(() => {
                    this.$message({
                        message: 'Post was successfully deleted.',
                        type: 'success'
                    });
                });

                if (this.full) {
                    history.go(-1);
                } else {
                    this.hidden = true;
                }
            },

            /**
             * Report submission
             *
             * @return void
             */
            report () {
                this.showReportModal = true;
            },

            /**
             * Upvote submission
             *
             * @return void
             */
            voteUp () {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                let id = this.list.id

                axios.post('/upvote-submission', {
                    submission_id: id,
                    previous_vote: this.currentVote
                })

                // Have up-voted
                if (this.upvoted) {
                    this.upvoted = false
                    this.list.upvotes--

                    var index = Store.state.submissions.upVotes.indexOf(id);
                    Store.state.submissions.upVotes.splice(index, 1);

                    return
                }

                // Have down-voted
                if (this.downvoted) {
                    this.downvoted = false
                    this.list.downvotes--

                    var index = Store.state.submissions.downVotes.indexOf(id);
                    Store.state.submissions.downVotes.splice(index, 1);
                }

                // Not voted
                this.upvoted = true
                this.list.upvotes++
                Store.state.submissions.upVotes.push(id)
            },


            /**
             * Downvote submission
             *
             * @return void
             */
            voteDown () {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                let id = this.list.id;

                axios.post('/downvote-submission', {
                    submission_id: id,
                    previous_vote: this.currentVote
                });

                // Have down-voted
                if (this.downvoted) {
                    this.downvoted = false;
                    this.list.downvotes--;

                    var index = Store.state.submissions.downVotes.indexOf(id);
                    Store.state.submissions.downVotes.splice(index, 1);

                    return;
                }

                // Have up-voted
                if (this.upvoted) {
                    this.upvoted = false;
                    this.list.upvotes--;

                    var index = Store.state.submissions.upVotes.indexOf(id);
                    Store.state.submissions.upVotes.splice(index, 1);
                }

                // Not voted
                this.downvoted = true;
                this.list.downvotes++;
                Store.state.submissions.downVotes.push(id);
            },

            showPhotoViewer(index = null) {
                if (index !== null) {
                    this.photoViewerIndex = index;
                }

                this.photoViewer = true;
            },

            showEmbed() {
                this.embedViewer = true;
            },

            showGifPlayer() {
                this.gifPlayer = true;
            },

            closeViwer() {
                this.photoViewer = false;
            },

            closeEmbed() {
                this.embedViewer = false;
            },

            closeGifPlayer() {
                this.gifPlayer = false;
            }
        }
    }
</script>
