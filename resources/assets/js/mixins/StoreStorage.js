export default {
    methods: {
        /**
         * Preloads few Store data using the HTML5 local storage. This'll get more updates in the future.
         * Let's begin by preloading sidebar categories.
         *
         * @return void
         */
        preloadStore() {
            Store.state.subscribedCategories = Vue.getLS('subscribedCategories');
        },

        /**
         * Filles the Store
         *
         * @return void
         */
        fillBasicStore() {
            if (auth.isGuest == true) return;

            // make sure sideFilter is set
            if (Vue.isSetLS('sidebar-filter')) {
                Store.sidebarFilter = Vue.getLS('sidebar-filter');
            } else {
                Store.sidebarFilter = 'subscribed-channels';
            }

            // preLoad few Store values. This is used to avoid need for loading. Sure it might be fast enough now,
            // but it's not instant! This makes it instant. Also, we need to make sure the preloaded data is
            // fresh, and that's why we're still doing the ajax request to update it. Performance baby!
            this.preloadStore();

            axios.get('/fill-basic-store', {
                params: {
                    sidebar_filter: Store.sidebarFilter
                }
            }).then((response) => {
                Store.state.submissions.upVotes = response.data.submissionUpvotes;
                Store.state.submissions.downVotes = response.data.submissionDownvotes;
                Store.state.comments.upVotes = response.data.commentUpvotes;
                Store.state.comments.downVotes = response.data.commentDownvotes;
                Store.state.bookmarks.submissions = response.data.bookmarkedSubmissions;
                Store.state.bookmarks.comments = response.data.bookmarkedComments;
                Store.state.bookmarks.categories = response.data.bookmarkedCategories;
                Store.state.bookmarks.users = response.data.bookmarkedUsers;
                Store.state.subscribedCategories = response.data.subscribedCategories;
                Store.state.moderatingCategories = response.data.moderatingCategories;
                Store.state.blocks.users = response.data.blockedUsers;

                response.data.moderatingCategories.forEach(function (element, index) {
                    Store.state.moderatingAt.push(element.id);
                });

                response.data.subscribedCategories.forEach(function (element, index) {
                    Store.state.subscribedAt.push(element.id);
                });

                response.data.moderatingCategoriesRecords.forEach(function (element, index) {
                    if (element.role == "administrator") {
                        Store.state.administratorAt.push(element.category_id);
                    } else if (element.role == "moderator") {
                        Store.state.moderatorAt.push(element.category_id);
                    }
                });

                Store.initialFilled = true;
            })
        },

        /**
         * Pulls 'store-state' from LocalStorage and put it in the Store.state.
         * In other words, loads the Store from the LocalStorage.
         *
         * @return void
         */
        pullStore() {
            Store.state = Vue.getLS('store-state');

            console.log('store pulled');
        },

        /**
         * Pushes Store.state into LocalStorage's 'store-state'.
         * In other words, saves the Store into the LocalStorage.
         *
         * @return void
         */
        pushStore() {
            Vue.putLS('store-state', Store.state);
            console.log('store pushed');
        }
    },

    created() {
        this.$eventHub.$on('push-store', this.pushStore);
        this.$eventHub.$on('pull-store', this.pullStore);

        document.addEventListener("visibilitychange", function () {
            if (document.visibilityState == 'hidden') {
                //
            }

            if (document.visibilityState == 'visible') {
                let tempStore = Vue.getLS('store-state');

                if (tempStore != null) {
                    Store.state = tempStore;
                    // Store = tempStore;
                    console.log('store pulled');
                }
            }
        });
    },

    watch: {
        'Store.state': {
            handler() {
                if (Store.initialFilled === false) return;

                this.$eventHub.$emit('push-store');
            },

            deep: true
        }
    },
};