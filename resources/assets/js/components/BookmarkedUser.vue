<template>
	<transition name="fade">
		<section class="bookmarked-item user-select" v-show="visible">
		<div class="avatar">
			<router-link :to="'/@' + list.username">
				<img :src="list.avatar" :alt="list.username">
			</router-link>
		</div>

		<div class="flex1">
			<h2>
				<router-link :to="'/@' + list.username">
					<i class="v-icon v-atsign" aria-hidden="true"></i>{{ list.username }}
				</router-link>

				<i class="v-icon h-yellow pointer"
				:class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'" @click="bookmark"></i>
			</h2>

			<p>
				{{ list.bio }}
			</p>
		</div>
	</section>
	</transition>
</template>

<script>
    export default {
        data: function () {
            return {
	        	Store,
	        	bookmarked: false,
				visible: true
            }
        },

        props: ['list'],

	    created () {
	    	this.setBookmarked()
	    },

	    methods: {
	    	/**
            *  Whether or not user has bookmarked the submission
            *
            *  @return Boolean
            */
            setBookmarked () { if ( Store.userBookmarks.indexOf(this.list.id) != -1 ) this.bookmarked = true },

            /**
            *  Toggles the user into bookmarks
            */
        	bookmark (user) {
        		this.bookmarked = !this.bookmarked

				axios.post('/bookmark-user', {
					id: this.list.id
				}).then((response) => {
					if (Store.userBookmarks.indexOf(this.list.id) != -1){
	                	var index = Store.userBookmarks.indexOf(this.list.id)
	                	Store.userBookmarks.splice(index, 1)

	                	return
	                }
					Store.userBookmarks.push(this.list.id)
				})
        	},
	    },
    };
</script>
