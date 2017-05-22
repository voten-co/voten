<template>
	<transition name="fade">
		<section class="bookmarked-item user-select" v-show="visible">
		<div class="avatar">
			<router-link :to="'/c/' + list.name">
				<img :src="list.avatar" :alt="list.name">
			</router-link>
		</div>

		<div class="flex1">
			<h2>
				<router-link :to="'/c/' + list.name">
					<i class="v-icon v-channel" aria-hidden="true"></i>{{ list.name }}
				</router-link>

				<i class="v-icon h-yellow pointer"
				:class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'" @click="bookmark"></i>
			</h2>

			<p>
				{{ list.description }}
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

		mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip()
			})
		},

	    methods: {
	    	/**
            *  Whether or not user has bookmarked the submission
            *
            *  @return Boolean
            */
            setBookmarked () { if ( Store.categoryBookmarks.indexOf(this.list.id) != -1 ) this.bookmarked = true },

            /**
            *  Toggles the category into bookmarks
            */
        	bookmark (category) {
        		this.bookmarked = !this.bookmarked

				axios.post('/bookmark-category', {
					id: this.list.id
				}).then((response) => {
					if (Store.categoryBookmarks.indexOf(this.list.id) != -1){
	                	var index = Store.categoryBookmarks.indexOf(this.list.id)
	                	Store.categoryBookmarks.splice(index, 1)

	                	return
	                }
					Store.categoryBookmarks.push(this.list.id)
				})
        	},
	    },
    };
</script>
