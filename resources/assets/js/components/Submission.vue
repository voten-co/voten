<style lang="scss">
.side-actions {
    visibility: hidden;
}

.submission-item {
    &:hover {
        background-color: #f5f8fa;
    }

    .separator {
        margin-left: 0.2em;
        margin-right: 0.2em;
        color: #979797;
        font-weight: bold;
    }

    .source {
        color: #979797;
        font-size: 80%;
        
        &:hover {
            text-decoration: underline;
        }
    }

    border: 1px solid #eaeef5;
    border-bottom: 2px solid #eaeef5;
    border-radius: 2px;
    padding: 0 1em;
    margin-bottom: 0.5em;

    .info {
        color: #979797;
        font-size: 80%;

        a {
            color: #979797;
            font-weight: bold;

            &:hover {
                text-decoration: underline;
            }
        }
    }

    .title a {
        color: #333;
    }

    .content {
        padding: 1em;
        padding-right: 0;
        user-select: none;
        // margin-right: 1em;
    }

    .actions {
        margin-top: 1em;

        .left {
            a {
                font-size: 13px;
                font-weight: bold;
                display: inline-flex;
                align-items: center;
                min-width: 80px;
                color: #4c4b4b;
            }
        }

        .right {
            a {
                font-size: 13px;
                // font-weight: bold;
            }
        }

        i {
            color: #657786;
            font-size: 17px;
            margin-left: 1em;
        }

        .count {
            margin-left: 1em;
        }

        .date {
            color: #979797;
            font-size: 80%;
            font-weight: normal;

            &:hover {
                text-decoration: underline;
            }
        }

        .comments-icon:hover {
            i,
            .count {
                color: #78b38a;
            }
        }

        .bookamrks-icon:hover {
            i {
                color: #edb431;
            }
        }

        .more-icon:hover {
            i {
                color: #000;
            }
        }
    }

    .thumbnail {
        border: 1px solid #e3e3e3;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        border-radius: 0.2rem;
        height: 78px;
        width: 130px;
    }

    .no-thumbnail {
        border-right: 1px solid #e7e7e7;
    }

    .side-voting {
        padding-right: 1em;

        &:hover {
            cursor: pointer;

            i {
                color: #db6e6e !important;
            }
        }
    }
}

.submission-item:hover {
    .side-actions {
        visibility: inherit;
    }
}
</style>


<template>
	<transition name="el-fade-in-linear">
		<div class="submission-item submission-wrapper"
		     @dblclick="doubleClicked"
		     v-show="!hidden"
		     :id="'submission' + list.id">
			<!-- side-voting -->
			<div class="side-voting"
			     :class="{'no-thumbnail' : !showThumbnail}"
			     @click="like">
				<i class="v-icon side-vote-icon"
				   :class="liked ? 'v-heart-filled go-red animated bounceIn' : 'v-heart go-gray'"></i>

				<div class="user-select vote-number go-gray">
					{{ points }}
				</div>
			</div>

			<!-- thumbnail -->
			<div :style="thumbnail"
			     v-if="showThumbnail"
			     @click="embedOrOpen"
			     class="thumbnail pointer">
			</div>

			<!-- content -->
			<div class="flex1"
			     :class="'content ' + type">
				<div class="info">
					<router-link v-text="'@' + list.author.username"
					             :to="userUrl(list.author.username)"></router-link> to
					<router-link v-text="'#' + list.channel_name"
					             :to="channelUrl(list.channel_name)"></router-link>
				</div>

				<div>
					<h3 class="title">
						<router-link :to="url"
						             class="flex-space v-ultra-bold">
							{{ list.title }}        
						</router-link>
					</h3>
                    
                    <span class="separator" v-if="type === 'link'">
                        &#183;
                    </span>

                    <a :href="list.content.url" v-if="type === 'link'" target="_blank" class="source">
                        {{ list.domain }}
                    </a>

                    <el-tag size="mini"
                            type="danger"
                            class="margin-left-half"
                            v-if="list.nsfw">NSFW</el-tag>
				</div>

				<div class="actions flex-space">
					<div class="left">
						<router-link :to="url"
						             class="comments-icon">
							<el-tooltip class="item"
							            content="Comments"
							            placement="top"
							            transition="false"
							            :open-delay="500">
								<i class="v-icon v-comment margin-left-0"></i>
							</el-tooltip>
							<span v-if="list.comments_count"
							      class="count"
							      v-text="list.comments_count"></span>
						</router-link>

						<a @click.prevent="bookmark"
						   href="#bookmark"
						   class="bookamrks-icon">
							<el-tooltip class="item"
							            :content="bookmarked ? 'Unbookmark' : 'Bookmark'"
							            placement="top"
							            transition="false"
							            :open-delay="500">
								<i class="v-icon pointer"
								   :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
							</el-tooltip>
						</a>

						<el-dropdown size="mini"
						             type="primary"
						             class="margin-left-1 more-icon"
						             trigger="click"
						             :show-timeout="0"
						             :hide-timeout="0">
							<i class="el-icon-more-outline"></i>

							<el-dropdown-menu slot="dropdown">
								<el-dropdown-item v-if="!owns"
								                  @click.native="report">
									Report
								</el-dropdown-item>

								<el-dropdown-item @click.native="hide"
								                  v-if="!owns">
									Hide
								</el-dropdown-item>

								<el-dropdown-item @click.native="markAsNSFW"
								                  v-if="showNSFW">
									NSFW
								</el-dropdown-item>

								<el-dropdown-item @click.native="markAsSFW"
								                  v-if="showSFW">
									Family Safe
								</el-dropdown-item>

								<el-dropdown-item class="go-red"
								                  @click.native="destroy"
								                  v-if="owns">
									Delete
								</el-dropdown-item>

								<el-dropdown-item class="go-green"
								                  @click.native="approve"
								                  v-if="showApprove"
								                  divided>
									Approve
								</el-dropdown-item>

								<el-dropdown-item class="go-red"
								                  @click.native="disapprove"
								                  v-if="showDisapprove">
									Delete
								</el-dropdown-item>
							</el-dropdown-menu>
						</el-dropdown>
					</div>

					<div class="right">
						<router-link class="date"
						             v-text="date"
						             :to="url"></router-link>
					</div>
				</div>
			</div>
		</div>
	</transition>
</template>

<script>
import Helpers from '../mixins/Helpers';
import Submission from '../mixins/Submission';

export default {
    mixins: [Helpers, Submission],

    computed: {
        thumbnail() {
            if (this.type === 'link') {
                return {
                    backgroundImage: 'url(' + this.list.content.thumbnail + ')'
                };
            } else if (this.type === 'img') {
                return {
                    backgroundImage: 'url(' + this.list.content.thumbnail_path + ')'
                };
            }
        },

        showThumbnail() {
            return this.list.content.thumbnail || this.list.content.thumbnail_path;
        },

        url() {
            return this.submissionUrl(this.list);
        },

        date() {
            return this.parsDiffForHumans(this.list.created_at);
        }
    },

    methods: {
        doubleClicked() {
            if (this.isGuest) return;

            if (!this.liked) {
                this.like();
            }
        },

        embedOrOpen(event) {
            if (this.type === 'link') {
                this.$router.push(this.url);
            }

            if (this.type === 'img') {
                this.showPhotoViewer(this.list.content);
            }
        },

        /**
         * Deletes the submission. Only the author is allowed to make such decision.
         *
         * @return void
         */
        destroy() {
            this.hidden = true;

            axios.delete(`/submissions/${this.list.id}`).catch(() => {
                this.hidden = false;
            });
        },

        /**
         * Disapproves the submission. Only the moderators of channel are allowed to do this.
         *
         * @return void
         */
        disapprove() {
            this.hidden = true;

            axios.post(`/submissions/${this.list.id}/disapprove`).catch(() => (this.hidden = false));
        }
    }
};
</script>
