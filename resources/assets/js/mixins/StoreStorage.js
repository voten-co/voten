export default {
    methods: {
        /**
         * Preloads few Store data using the HTML5 local storage. This'll get more updates in the future.
         * Let's begin by preloading sidebar categories.
         *
         * @return void
         */
        preloadStore() {
            Store.subscribedCategories = Vue.ls.get('subscribedCategories', []);
        },

        /**
         * Filles the Store
         *
         * @return void
         */
        fillBasicStore() {
            if (auth.isGuest == true) return;

            // make sure sideFilter is set
            if (this.isSetLS('sidebar-filter')) {
                Store.sidebarFilter = this.getLS('sidebar-filter');
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
                Store.submissionUpVotes = response.data.submissionUpvotes;
                Store.submissionDownVotes = response.data.submissionDownvotes;
                Store.commentUpVotes = response.data.commentUpvotes;
                Store.commentDownVotes = response.data.commentDownvotes;
                Store.submissionBookmarks = response.data.bookmarkedSubmissions;
                Store.commentBookmarks = response.data.bookmarkedComments;
                Store.categoryBookmarks = response.data.bookmarkedCategories;
                Store.userBookmarks = response.data.bookmarkedUsers;
                Store.subscribedCategories = response.data.subscribedCategories;
                Store.moderatingCategories = response.data.moderatingCategories;
                Store.blockedUsers = response.data.blockedUsers;

                response.data.moderatingCategories.forEach(function (element, index) {
                    Store.moderatingAt.push(element.id);
                });

                response.data.subscribedCategories.forEach(function (element, index) {
                    Store.subscribedAt.push(element.id);
                });

                response.data.moderatingCategoriesRecords.forEach(function (element, index) {
                    if (element.role == "administrator") {
                        Store.administratorAt.push(element.category_id);
                    } else if (element.role == "moderator") {
                        Store.moderatorAt.push(element.category_id);
                    }
                });

                Store.initialFilled = true;
            })
        },

        /**
         * Fires a cross window event by filling the 'event' in LocalStorage.
         *
         * @param event
         */
        crossWindowEvent(event) {
            Vue.ls.set('event', event, 60 * 60 * 1000);
        },

        /**
         * Event in LocalStorage is filled, so it picks up on it, fires it, and then cleans it.
         *
         * @return void
         */
        updateEvent() {
            // Fire only if there's anything to fire
            if (Vue.ls.get('event') == '') return;

            // Fire event
            this.$eventHub.$emit(Vue.ls.get('event'));

            // Empty event storage
            Vue.ls.set('event', '', 60 * 60 * 1000);
        },

        pullStore() {
            Store = Vue.ls.get('Store');
            console.log('store pulled');
        },

        pushStore() {
            // console.log(Store);
            Vue.ls.set('Store', Store, 60 * 60 * 1000);
            console.log('store pushed');
        },

        syncStore() {
            return;
        }
    },

    created() {
        this.$eventHub.$on('sync-store', this.syncStore);
        this.$eventHub.$on('push-store', this.pushStore);
        this.$eventHub.$on('pull-store', this.pullStore);

        document.addEventListener("visibilitychange", function() {
            // let that = this;

            if (document.visibilityState == 'hidden') {
                //
            }

            if (document.visibilityState == 'visible') {
                // this.pullStore();
                let tempStore = Vue.ls.get('Store');

                if (tempStore != null) {
                    Store.submissionUpVotes = tempStore.submissionUpVotes;
                    // Store = tempStore;
                    console.log('store pulled');
                }
            }
        });
    },

    /**
     * Listen for localStorage changes. This makes it possible for us to synce all open tabs
     * together so we won't have data missing in one tab.
     */
    mounted() {
        Vue.ls.on('event', this.updateEvent);
    },

    watch: {
        // this should be _debaunced
        // 'Store'() {
        //     console.log('store changed');
        //
        //     this.pushStore();
        // },

        // 'hasFocus'() {
        //     console.log('focus changed');
        //
        //     if (this.hasFocus === true) {
        //         console.log('focus == true');
        //         this.pullStore();
        //     }
        // }
    },
    //
    // computed: {
    //     hasFocus() {
    //         return document.hasFocus();
    //     }
    // }
};