<template>
    <section>
        <p>
            Other than subscribing to channels, there are more filters available to make sure you get the
            content that suits you the best.
        </p>

        <el-form label-position="top" label-width="10px" :model="form">
            <div class="form-toggle">
                <span>Display NSFW submissions: <small>(You must be 18 or older)</small></span>
                <el-switch v-model="form.nsfw"></el-switch>
            </div>

            <div class="form-toggle">
                <span>Display preview for NSFW submissions:</span>
                <el-switch v-model="form.nsfwMedia"></el-switch>
            </div>

            <div class="form-toggle">
                <span>Exclude my upvoted submissions:</span>
                <el-switch v-model="form.exclude_upvoted_submissions"></el-switch>
            </div>

            <div class="form-toggle no-border">
                <span>Exclude my downvoted submissions:</span>
                <el-switch v-model="form.exclude_downvoted_submissions"></el-switch>
            </div>

            <!-- submit -->
            <el-form-item v-if="changed">
                <el-button type="success" @click="save" :loading="sending" size="medium">Save</el-button>
            </el-form-item>
        </el-form>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                sending: false,
                auth,
                form: {
                    nsfw: auth.nsfw,
                    nsfwMedia: auth.nsfwMedia,
                    exclude_upvoted_submissions: auth.exclude_upvoted_submissions,
                    exclude_downvoted_submissions: auth.exclude_downvoted_submissions,
                }
            }
        },

        computed: {
            changed() {
                if (
                    auth.nsfw != this.form.nsfw ||
                    auth.nsfwMedia != this.form.nsfwMedia ||
                    auth.exclude_upvoted_submissions != this.form.exclude_upvoted_submissions ||
                    auth.exclude_downvoted_submissions != this.form.exclude_downvoted_submissions
                ) {
                    return true;
                }

                return false;
            },
        },

        methods: {
            save() {
                this.sending = true;

                axios.post('/update-home-feed', {
                    nsfw: this.form.nsfw,
                    nsfw_media: this.form.nsfwMedia,
                    exclude_downvoted_submissions: this.form.exclude_downvoted_submissions,
                    exclude_upvoted_submissions: this.form.exclude_upvoted_submissions,
                }).then(() => {
                    auth.nsfw = this.form.nsfw;
                    auth.nsfwMedia = this.form.nsfwMedia;
                    auth.exclude_upvoted_submissions = this.form.exclude_upvoted_submissions;
                    auth.exclude_downvoted_submissions = this.form.exclude_downvoted_submissions;

                    this.sending = false;
                }).catch(() => {
                    this.sending = false;
                });
            },
        }
    };
</script>
