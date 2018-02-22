<template>
	<div class="padding-bottom-10 flex1" :class="{'flex-center' : nothingFound}" id="comments-index" 
		v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore" @scroll.passive="scrolled"
	>
		<section class="box-typical comments" id="comments-section" v-if="comments.length">
			<div class="box-typical-inner ui threaded comments">
				<div v-for="c in comments" class="v-comment-not-full" :key="c.id">
					<comment :list="c" :comments-order="'created_at'"></comment>
				</div>
			</div>
		</section>
	
		<no-content v-if="nothingFound" :text="'This user has not commented on anything'"></no-content>
	
		<loading v-if="loading && page > 1"></loading>
	
		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</div>
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
        NoMoreItems() {
            return Store.page.user.comments.NoMoreItems;
        },

        comments() {
            return Store.page.user.comments.comments;
        },

        loading() {
            return Store.page.user.comments.loading;
        },

        page() {
            return Store.page.user.comments.page;
        },

        nothingFound() {
            return Store.page.user.comments.nothingFound;
        },

        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        }
    },

    created() {
        this.setPageTitle('Comments | @' + this.$route.params.username);
    },

    beforeRouteEnter(to, from, next) {
        Store.page.user.comments.clear();

        if (Store.page.user.comments.page === 0) {
            if (typeof app != 'undefined') {
                app.$Progress.start();
            }

            Promise.all([
                Store.page.user.comments.getComments(to.params.username),
                Store.page.user.getUser(to.params.username)
            ]).then(() => {
                next((vm) => {
                    vm.$Progress.finish();
                });
            });
        } else {
            next((vm) => {
                vm.$Progress.finish();
            });
        }
    },

    beforeRouteUpdate(to, from, next) {
        if (to.hash !== from.hash) return;

        Store.page.user.comments.clear();

        this.$Progress.start();

        Promise.all([
            Store.page.user.comments.getComments(to.params.username),
            Store.page.user.getUser(to.params.username, false)
        ]).then((values) => {
            Store.page.user.setUser(values[1]);
            this.setPageTitle('Comments | @' + to.params.username);
            this.$Progress.finish();
            next();
        });
    },

    methods: {
        loadMore() {
            Store.page.user.comments.getComments(this.$route.params.username);
        }
    }
};
</script>
