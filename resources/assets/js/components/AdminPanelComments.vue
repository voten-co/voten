<template>
	<div id="submissions" class="home-submissions" 
		v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore" @scroll.passive="scrolled"
	>
		<section class="box-typical comments" v-if="comments.length">
			<div class="box-typical-inner ui threaded comments">
				<div v-for="c in uniqueList" :key="c.id" class="v-comment-not-full">
			        <comment :list="c" :comments-order="'created_at'"></comment>
			    </div>
			</div>
		</section>

		<loading v-if="loading"></loading>

		 <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</div>
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

    data: function() {
        return {
            NoMoreItems: false,
            loading: true,
            nothingFound: false,
            comments: [],
            page: 0
        };
    },

    created() {
        this.$eventHub.$on('scrolled-to-bottom', this.loadMore);
        this.getComments();
    },

    watch: {
        $route: function() {
            this.clearContent();
            this.getComments();
        }
    },

    computed: {
        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        },

        uniqueList() {
            let unique = [];
            let temp = [];

            this.comments.forEach(function(element, index, self) {
                if (temp.indexOf(element.id) === -1) {
                    unique.push(element);
                    temp.push(element.id);
                }
            });

            return unique;
        }
    },

    methods: {
        loadMore() {
            if (!this.loading && !this.NoMoreItems) {
                this.getComments();
            }
        },

        clearContent() {
            this.nothingFound = false;
            this.users = [];
            this.loading = true;
        },

        getComments() {
            this.loading = true;
            this.page++;

            axios
                .get('/admin/comments', {
                    params: {
                        page: this.page
                    }
                })
                .then((response) => {
                    this.comments = [...this.comments, ...response.data.data];

                    if (response.data.links.next == null) {
                        this.NoMoreItems = true;
                    }

                    if (this.comments.length == 0) {
                        this.nothingFound = true;
                    }

                    this.loading = false;
                });
        }
    }
};
</script>
