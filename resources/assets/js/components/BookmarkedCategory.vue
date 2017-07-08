<template>
	<transition name="fade">
		<section class="bookmarked-item user-select" v-show="visible">
			<div class="avatar">
				<router-link :to="'/c/' + list.name">
					<img :src="list.avatar" :alt="list.name">
				</router-link>
			</div>

			<div class="flex1">
				<div class="flex-space">
					<h2>
						<router-link :to="'/c/' + list.name">
							{{ list.name }}
						</router-link>
					</h2>

					<div class="flex-align-center">
						<i class="v-icon h-yellow pointer" v-if="!isNewbie"
						   :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'" @click="bookmark"
						   data-toggle="tooltip" data-placement="left" :title="bookmarked ? 'Unbookmark' : 'Bookmark'"></i>

						<button class="v-button v-button-small margin-left-1"
							:class="subscribed ? 'v-button--red' : 'v-button--green'" @click="subscribe"
							v-text="subscribed ? 'Unsubscribe' : 'Subscribe'"></button>
					</div>
				</div>

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
	        	subscribed: false,
				visible: true
            }
        },

        props: ['list'],

	    created () {
	    	this.setBookmarked();
	    	this.setSubscribed();
	    },

        watch: {
            'Store.categoryBookmarks' () {
                this.setBookmarked();
            },

            'Store.subscribedAt' () {
                this.setSubscribed();
            }
        },

		mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip();
			})
		},

        computed: {
            /**
             * Has the user just registered?
             *
             * @return Boolean
             */
            isNewbie () {
                return this.$route.query.newbie == 1;
            },
        },

        methods: {
	    	/**
             * Whether or not user has bookmarked the category
             */
            setBookmarked () {
                if (Store.categoryBookmarks.indexOf(this.list.id) != -1) {
                    this.bookmarked = true;
				}
			},

			/**
             * Whether or not user has subscribed to the category
             */
            setSubscribed() {
                if (Store.subscribedAt.indexOf(this.list.id) != -1) {
                    this.subscribed = true;
				}
			},

            /**
             * Toggles the category into bookmarks
			 *
			 * @return void
             */
        	bookmark(category) {
        		this.bookmarked = !this.bookmarked;

				axios.post('/bookmark-category', {
					id: this.list.id
				}).then((response) => {
					if (Store.categoryBookmarks.indexOf(this.list.id) != -1){
	                	let index = Store.categoryBookmarks.indexOf(this.list.id);
	                	Store.categoryBookmarks.splice(index, 1);

	                	return;
	                }

					Store.categoryBookmarks.push(this.list.id);
				})
        	},

            /**
             * Subscribes to the category.
             *
             * @return void
             */
            subscribe() {
                this.subscribed = !this.subscribed;

                if (this.subscribed)
                // is subscribing
                {
                    Store.subscribedCategories.push(this.list);
                    Store.subscribedAt.push(this.list.id);
                } else
                // is un-subscribing
				{
                    let removeItem = this.list.id;
                    Store.subscribedCategories = Store.subscribedCategories.filter(function (category) {
                        return category.id != removeItem;
                    });

                    let index = Store.subscribedAt.indexOf(this.list.id);
                    Store.subscribedAt.splice(index, 1);
                }

                axios.post('/subscribe', {
                    category_id: this.list.id
                });

                this.$emit('subscribed');
            },
	    },
    };
</script>
