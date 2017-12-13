<template>
    <div class="container margin-top-1 col-7 user-select">
        <div class="margin-bottom-1">
            <h1>
                Subscribed Channels
                <span>({{ Store.state.subscribedCategories.length }})</span>:
            </h1>
        </div>

        <section>
            <subscribed-category v-for="category in categories" :list="category" :key="category.id"></subscribed-category>

            <no-content v-if="nothingFound" :text="'You have not bookmarked any channels yet'"></no-content>

            <loading v-show="loading"></loading>

            <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
        </section>
    </div>
</template>

<script>
    import Loading from '../components/Loading.vue';
    import SubscribedCategory from '../components/BookmarkedCategory.vue';
    import NoMoreItems from '../components/NoMoreItems.vue';
    import NoContent from '../components/NoContent.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        mixins: [Helpers],

        components: {
            NoContent,
            SubscribedCategory,
            Loading,
            NoMoreItems
        },

        data: function () {
            return {
                NoMoreItems: false,
                loading: true,
                nothingFound: false,
                page: 0,
                categories: []
            }
        },

        created () {
            this.$eventHub.$on('scrolled-to-bottom', this.loadMore);
            this.getCategories();
        },

        watch: {
            '$route': function () {
                this.clearContent();
                this.getCategories();
            }
        },


        methods: {
            loadMore () {
                if (Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems) {
                    this.getCategories();
                }
            },

            clearContent () {
                this.nothingFound = false;
                this.users = [];
                this.loading = true;
            },

            getCategories () {
                this.page ++;
                this.loading = true;

                axios.get('/subscribed-categories', {
                    params: {
                        page: this.page
                    }
                }).then((response) => {
                    this.categories = [...this.categories, ...response.data.data];

                    if (response.data.next_page_url == null) {
                        this.NoMoreItems = true;
                    }

                    if(this.categories.length == 0) {
                        this.nothingFound = true;
                    }

                    this.loading = false;
                })
            }
        }
    };
</script>
