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

        <no-content v-if="nothingFound" :text="'No bookmarked comments yet'" icon="comment"></no-content>
        <loading v-if="loading && page > 1"></loading>
        <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
    </section>
</template>

<script>
import Comment from '../components/Comment.vue';
import NoContent from '../components/NoContent.vue';
import NoMoreItems from '../components/NoMoreItems.vue';
import Loading from '../components/Loading.vue';
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    components: {
        Comment,
        NoContent,
        Loading,
        NoMoreItems
    },

    computed: {
        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        },

        NoMoreItems() {
            return Store.page.bookmarkedComments.NoMoreItems;
        },

        nothingFound() {
            return Store.page.bookmarkedComments.nothingFound;
        },

        comments() {
            return Store.page.bookmarkedComments.comments;
        },

        loading() {
            return Store.page.bookmarkedComments.loading;
        },

        page() {
            return Store.page.bookmarkedComments.page;
        }
    },

    methods: {
        loadMore() {
            Store.page.bookmarkedComments.getComments();
        }
    },

    beforeRouteEnter(to, from, next) {
        if (!Store.page.bookmarkedComments.page > 0) {
            if (typeof app != 'undefined') {
                app.$Progress.start();
            }

            Store.page.bookmarkedComments.getComments().then(() => {
                next((vm) => vm.$Progress.finish());
            });
        } else {
            next();
        }
    }
};
</script>
