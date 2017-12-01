<template>
    <section class="bookmarked-items padding-1" :class="{'flex-center' : nothingFound}"
             v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore"
    >
        <section class="box-typical comments" id="comments-section" v-if="comments.length">
            <div class="box-typical-inner ui threaded comments">
                <div v-for="c in comments" :key="c.id" class="v-comment-not-full">
                    <comment :list="c" :comments-order="'created_at'"></comment>
                </div>
            </div>
        </section>

        <no-content v-if="nothingFound" :text="'No bookmarked comments yet'"></no-content>

        <loading v-show="loading"></loading>

        <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
    </section>
</template>

<script>
    import Loading from '../components/Loading.vue';
    import Comment from '../components/Comment.vue';
    import NoContent from '../components/NoContent.vue';
    import NoMoreItems from '../components/NoMoreItems.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        mixins: [Helpers],

        components: {
            Loading,
            Comment,
            NoContent,
            NoMoreItems
        },

        data() {
            return {
                NoMoreItems: false,
                loading: true,
                nothingFound: false,
                comments: [],
                page: 0
            }
        },

        created () {
            this.getComments()
        },


        watch: {
            '$route': function () {
                this.clearContent();
                this.getComments();
            }
        },

        computed: {
            cantLoadMore() {
                return this.loading || this.NoMoreItems || this.nothingFound;
            },
        },

        methods: {
            loadMore () {
                this.getComments();
            },

            clearContent () {
                this.nothingFound = false
                this.users = []
                this.loading = true
            },

            getComments () {
                this.loading = true
                this.page++

                axios.get('/bookmarked-comments', {
                    params: {
                        page: this.page
                    }
                }).then((response) => {
                    this.comments = [...this.comments, ...response.data.data]

                    if (response.data.next_page_url == null) {
                        this.NoMoreItems = true
                    }

                    if (this.comments.length == 0) {
                        this.nothingFound = true
                    }

                    this.loading = false
                })
            }
        }
    };
</script>
