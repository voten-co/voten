<template>
    <section>
        <h3 class="dotted-title">
            <span>
                Account
            </span>
        </h3>

        <el-form label-position="top" label-width="10px" :model="form">
            <el-form-item label="Font">
                <el-select v-model="form.font" placeholder="Font..." filterable>
                    <el-option
                            v-for="item in fonts"
                            :key="item"
                            :label="item"
                            :value="item">
                    </el-option>
                </el-select>
            </el-form-item>

            <h3 class="dotted-title">
                <span>
                    Notify me when
                </span>
            </h3>

            <div class="form-toggle">
                My submissions get comments:
                <el-switch v-model="form.notify_submissions_replied"></el-switch>
            </div>

            <div class="form-toggle">
                My comments get replies:
                <el-switch v-model="form.notify_comments_replied"></el-switch>
            </div>

            <div class="form-toggle no-border">
                My username gets mentioned:
                <el-switch v-model="form.notify_mentions"></el-switch>
            </div>

            <!-- submit -->
            <el-form-item v-if="changed">
                <el-button round type="success" @click="save" :loading="sending" size="medium">Save</el-button>
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
            sending: false,
            errors: [],

            fonts: [
                'Josefin Sans',
                'Lato',
                'Source Sans Pro',
                'Ubuntu',
                'Open Sans',
                'Dosis',
                'Reem Kufi',
                'Athiti',
                'Molengo',
                'Catamaran',
                'Roboto',
                'Eczar',
                'Titillium Web',
                'Varela Round',
                'Bree Serif',
                'Alegreya Sans',
                'Sorts Mill Goudy',
                'Patrick Hand',
                'Dancing Script',
                'Satisfy',
                'Montserrat',
                'Gloria Hallelujah',
                'Courgette',
                'Indie Flower',
                'Handlee',
                'Arvo'
            ],

            form: {
                font: clientsideSettings.font,
                notify_submissions_replied:
                    auth.server_side_settings.notifications
                        .notify_submissions_replied,
                notify_comments_replied:
                    auth.server_side_settings.notifications
                        .notify_comments_replied,
                notify_mentions:
                    auth.server_side_settings.notifications.notify_mentions
            }
        };
    },

    computed: {
        changed() {
            if (
                Store.settings.font != this.form.font ||
                auth.server_side_settings.notifications
                    .notify_submissions_replied !=
                    this.form.notify_submissions_replied ||
                auth.server_side_settings.notifications.notify_mentions !=
                    this.form.notify_mentions ||
                auth.server_side_settings.notifications
                    .notify_comments_replied !=
                    this.form.notify_comments_replied
            ) {
                return true;
            }

            return false;
        }
    },

    methods: {
        /**
         * Stores the changes in the database. (using the recently changed values)
         *
         * @return void
         */
        save() {
            this.sending = true;

            let changedFont = Store.settings.font !== this.form.font;

            axios
                .patch('/users/account', {
                    notify_submissions_replied: this.form
                        .notify_submissions_replied,
                    notify_comments_replied: this.form.notify_comments_replied,
                    notify_mentions: this.form.notify_mentions
                })
                .then(() => {
                    this.errors = [];

                    Store.settings.font = this.form.font;
                    auth.server_side_settings.notifications.notify_submissions_replied = this.form.notify_submissions_replied;
                    auth.server_side_settings.notifications.notify_comments_replied = this.form.notify_comments_replied;
                    auth.server_side_settings.notifications.notify_mentions = this.form.notify_mentions;

                    if (changedFont) {
                        this.loadWebFont();
                    }

                    this.sending = false;
                })
                .catch((error) => {
                    this.sending = false;

                    this.errors = error.response.data.errors;
                });
        }
    }
};
</script>
