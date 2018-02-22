<template>
	<el-dialog title="Customize Feed"
	           :visible="visible"
	           :width="isMobile ? '99%' : '550px'"
	           @close="close"
	           append-to-body>
		<section class="user-select">
			<p>
				Other than subscribing to channels, there are more filters available to make sure you get the content that suits you the best.
			</p>

			<el-form label-position="top"
			         label-width="10px"
			         :model="form">
				<div class="form-toggle">
					<span>Include NSFW submissions:
						<small>(You must be 18 or older)</small>
					</span>
					<el-switch v-model="form.include_nsfw_submissions"></el-switch>
				</div>

				<div class="form-toggle">
					<span>Exclude my upvoted submissions:</span>
					<el-switch v-model="form.excludeUpvotedSubmissions"></el-switch>
				</div>

				<div class="form-toggle">
					<span>Exclude my downvoted submissions:</span>
					<el-switch v-model="form.excludeDownvotedSubmissions"></el-switch>
				</div>

				<div class="form-toggle no-border">
					<span>Exclude my bookmarked submissions:</span>
					<el-switch v-model="form.excludeBookmarkedSubmissions"></el-switch>
				</div>

				<el-form-item label="Submissions Type:">
					<el-radio-group v-model="form.submissionsType"
					                size="small">
						<el-radio-button label="All"></el-radio-button>
						<el-radio-button label="Link"></el-radio-button>
						<el-radio-button label="Text"></el-radio-button>
						<el-radio-button label="Image"></el-radio-button>
						<el-radio-button label="GIF"></el-radio-button>
					</el-radio-group>
				</el-form-item>

				<el-form-item label="Limit submissions to:">
					<el-select v-model="form.submissionsFilter"
					           placeholder="Limit submissions to:"
					           filterable>
						<el-option v-for="item in filters"
						           :key="item.value"
						           :label="item.description"
						           :value="item.value">
						</el-option>
					</el-select>
				</el-form-item>
			</el-form>
		</section>

		<span slot="footer"
		      class="dialog-footer"
		      v-if="changed">
			<el-button round type="success"
			           @click="save"
			           size="medium">
				Save
			</el-button>
		</span>
	</el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    props: ['visible'],

    data() {
        return {
            form: {
                include_nsfw_submissions:
                    Store.settings.feed.include_nsfw_submissions,
                excludeUpvotedSubmissions:
                    Store.settings.feed.excludeUpvotedSubmissions,
                excludeDownvotedSubmissions:
                    Store.settings.feed.excludeDownvotedSubmissions,
                submissionsFilter: Store.settings.feed.submissionsFilter,
                submissionsType: Store.settings.feed.submissionsType,
                excludeBookmarkedSubmissions:
                    Store.settings.feed.excludeBookmarkedSubmissions
            },

            filters: [
                { value: 'all', description: 'Submissions from all channels' },
                {
                    value: 'subscribed',
                    description:
                        'Only submissions from channels I am subscribed to'
                },
                {
                    value: 'moderating',
                    description:
                        'Only submissions from channels I am moderating'
                },
                {
                    value: 'bookmarked',
                    description:
                        'Only submissions from channels I have bookmarked'
                },
                {
                    value: 'by-bookmarked-users',
                    description: 'Only submissions from users I have bookmarked'
                }
            ]
        };
    },

    computed: {
        changed() {
            if (
                Store.settings.feed.include_nsfw_submissions !=
                    this.form.include_nsfw_submissions ||
                Store.settings.feed.excludeUpvotedSubmissions !=
                    this.form.excludeUpvotedSubmissions ||
                Store.settings.feed.excludeDownvotedSubmissions !=
                    this.form.excludeDownvotedSubmissions ||
                Store.settings.feed.submissionsFilter !=
                    this.form.submissionsFilter ||
                Store.settings.feed.submissionsType !=
                    this.form.submissionsType ||
                Store.settings.feed.excludeBookmarkedSubmissions !=
                    this.form.excludeBookmarkedSubmissions
            ) {
                return true;
            }

            return false;
        }
    },

    beforeDestroy() {
        if (window.location.hash == '#feedSettings') {
            history.go(-1);
        }
    },

    created() {
        window.location.hash = 'feedSettings';
    },

    methods: {
        save() {
            Store.settings.feed.include_nsfw_submissions = this.form.include_nsfw_submissions;
            Store.settings.feed.excludeUpvotedSubmissions = this.form.excludeUpvotedSubmissions;
            Store.settings.feed.excludeDownvotedSubmissions = this.form.excludeDownvotedSubmissions;
            Store.settings.feed.submissionsFilter = this.form.submissionsFilter;
            Store.settings.feed.submissionsType = this.form.submissionsType;
            Store.settings.feed.excludeBookmarkedSubmissions = this.form.excludeBookmarkedSubmissions;

            this.$eventHub.$emit('refresh-home');
            this.close();
        },

        close() {
            this.$emit('update:visible', false);
        }
    }
};
</script>
