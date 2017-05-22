export default {
    methods: {
        /**
         * Filles the Store
         *
         * @return void
         */
        fillBasicStore() {
        	if (auth.isGuest == true) {
        		return
        	}

            axios.get('/fill-basic-store').then((response) => {

                Store.submissionUpVotes = response.data.submissionUpvotes
                Store.submissionDownVotes = response.data.submissionDownvotes
                Store.commentUpVotes = response.data.commentUpvotes
                Store.commentDownVotes = response.data.commentDownvotes
                Store.submissionBookmarks = response.data.bookmarkedSubmissions
                Store.commentBookmarks = response.data.bookmarkedComments
                Store.categoryBookmarks = response.data.bookmarkedCategories
                Store.userBookmarks = response.data.bookmarkedUsers
                Store.subscribedCategories = response.data.subscribedCategories
                Store.moderatingCategories = response.data.moderatingCategories
                Store.blockedUsers = response.data.blockedUsers

                response.data.moderatingCategories.forEach(function(element, index){
                    Store.moderatingAt.push(element.id)
                })

                response.data.moderatingCategoriesRecords.forEach(function(element, index) {
                    if (element.role == "administrator") {
                        Store.administratorAt.push(element.category_id)
                    } else if (element.role == "moderator") {
                        Store.moderatorAt.push(element.category_id)
                    }
                })

                Store.loading = false
            })
        },
    }
};
