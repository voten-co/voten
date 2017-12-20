<template>
    <section class="user-select">
        <p>
            Other than subscribing to channels, there are more filters available to make sure you get the
            content that suits you the best.
        </p>

        <el-form label-position="top" label-width="10px" :model="form">
            <div class="form-toggle">
                <span>Exclude my upvoted submissions:</span>
                <el-switch v-model="form.excludeUpvotedSubmissions"></el-switch>
            </div>

            <div class="form-toggle no-border">
                <span>Exclude my downvoted submissions:</span>
                <el-switch v-model="form.excludeDownvotedSubmissions"></el-switch>
            </div>
            
            <el-form-item label="Submissions Type:">
                <!-- <el-radio-group v-model="form.submissionsTypes" size="small">
                  <el-radio-button label="All" value="all"></el-radio-button>
                  <el-radio-button label="Link"></el-radio-button>
                  <el-radio-button label="Text"></el-radio-button>
                  <el-radio-button label="Image"></el-radio-button>
                  <el-radio-button label="GIF" value="gif"></el-radio-button>
                </el-radio-group> -->

                <el-checkbox-group v-model="form.submissionsTypes" size="small">
                  <el-checkbox-button v-for="type in submissionsTypes" :label="type" :key="type">
                      {{ type }}
                  </el-checkbox-button>
                </el-checkbox-group>
            </el-form-item>

            <el-form-item v-if="changed">
                <el-button type="success" @click="save" size="medium">
                    Save
                </el-button>
            </el-form-item>
        </el-form>
    </section>
</template>

<script>
    import Helpers from '../mixins/Helpers'; 

    export default {
        mixins: [Helpers], 

        data() {
            return {
                form: {
                    excludeUpvotedSubmissions: Store.settings.feed.excludeUpvotedSubmissions,
                    excludeDownvotedSubmissions: Store.settings.feed.excludeDownvotedSubmissions,
                    submissionsFilter: Store.settings.feed.submissionsFilter,
                    submissionsTypes: Store.settings.feed.submissionsTypes,
                }, 

                submissionsTypes: [
                    'All', 'GIF', 'Image', 'Link', 'Text'
                ]
            }
        },

        computed: {
            changed() {
                if (
                    Store.settings.feed.excludeUpvotedSubmissions != this.form.excludeUpvotedSubmissions ||
                    Store.settings.feed.excludeDownvotedSubmissions != this.form.excludeDownvotedSubmissions || 
                    Store.settings.feed.submissionsFilter != this.form.submissionsFilter || 
                    Store.settings.feed.submissionsTypes != this.form.submissionsTypes 
                ) {
                    return true;
                }

                return false;
            },
        },

        methods: {
            save() {
                Store.settings.feed.excludeUpvotedSubmissions = this.form.excludeUpvotedSubmissions;
                Store.settings.feed.excludeDownvotedSubmissions = this.form.excludeDownvotedSubmissions;
                Store.settings.feed.submissionsFilter = this.form.submissionsFilter;
                Store.settings.feed.submissionsTypes = this.form.submissionsTypes;
            }
        }
    };
</script>
