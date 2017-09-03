<template>
    <section class="container margin-top-5 col-7" id="help">
        <div class="align-center margin-bottom-3">
            <router-link to="/help">
                <help-icon width="150" height="150"></help-icon>
            </router-link>
        </div>

        <router-link :to="'/help/' + list.id">
            <h1 v-text="list.title" v-if="loaded" class="title"></h1>
        </router-link>

        <markdown :text="list.body" v-if="loaded" class="margin-top-1"></markdown>

        <div class="flex-space simple-box user-select" v-if="!voted">
            <h3>
                Was this article helpful?
            </h3>

            <div>
                <button class="v-button v-button--green" @click="upVote">
                    Yes
                </button>

                <button class="v-button v-button--red" @click="downVote">
                    No
                </button>
            </div>
        </div>

        <div class="simple-box user-select" v-if="voted">
            <h3>
                Thanks for helping us improve Voten.
            </h3>
        </div>
    </section>
</template>

<style>
    #help .title {
        color: #333;
        font-weight: 600;
        font-size: 2em;
        margin: 0;
    }

    .simple-box {
        background: #e9ecf0;
        padding: .5em 2em;
        border-radius: 2px;
        margin-top: 2em;
        border-bottom: 2px solid #dde1e8;
    }
</style>

<script>
    import Markdown from '../components/Markdown.vue';
    import HelpIcon from '../components/Icons/Help.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        mixins: [Helpers],

        components: {
            Markdown,
            HelpIcon
        },

        data () {
            return {
                loaded: false,
                list: [],
                voted: false
            }
        },

        created () {
            this.fetch();
        },

        methods: {
            upVote() {
                this.voted = true;

                axios.post('/upvote-help', {
                    help_id: this.list.id,
                });
            },

            downVote() {
                this.voted = true;

                axios.post('/downvote-help', {
                    help_id: this.list.id,
                });
            },

            fetch() {
                // if landed on a help page
                if (preload.help) {
                    this.list = preload.help;
                    this.loaded = true;
                    delete preload.help;
                    this.setPageTitle(this.list.title);
                    return;
                }

                axios.get('/get-help', {
                    params: {
                        id: this.$route.params.id
                    }
                }).then((response) => {
                    this.list = response.data;
                    this.loaded = true;
                    this.setPageTitle(this.list.title);
                });
            }
        }
    };
</script>
