<template>
	<div class="container margin-top-1 col-7 user-select">
		<div class="margin-bottom-1" v-if="!isNewbie">
			<h1>Suggested #channels for you:</h1>
		</div>

		<div class="margin-top-bottom-1 align-center" v-if="isNewbie">
			<h2>
				Welcome to Voten, {{ auth.username }}
			</h2>

			<h1 v-if="isNewbie && !reachedMinimum">
				Please subscribe to <b>{{ 3 - counter }}</b> more channels
			</h1>

			<transition name="fade">
				<div class="text-or-button" v-if="isNewbie && reachedMinimum">
					<h1>Keep going</h1>

					or

					<router-link class="v-button v-button--primary" :to="{name: 'home', query: { sidebar: 1, newbie: 1 }}">
						Start Voting
					</router-link>
				</div>
			</transition>
		</div>

		<find-categories-item v-for="(value, index) in items" :key="value.id"
		:list="value.category" @subscribed="subscribed(index)"></find-categories-item>

		<no-content v-if="noContent" :text="'We are out of new #channels to suggest. Please keep calm and come back later'"></no-content>

		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !noContent"></no-more-items>
	</div>
</template>


<script>
    import FindCategoriesItem from '../components/FindCategoriesItem.vue'
    import NoContent from '../components/NoContent.vue'
	import NoMoreItems from '../components/NoMoreItems.vue'

    export default {
        components: {
        	FindCategoriesItem,
			NoContent,
			NoMoreItems
    	},


        data: function () {
            return {
				Store,
				auth,
				NoMoreItems: false,
            	loading: true,
                items: [],
				page: 0,
				counter: 0,
            }
        },


		computed: {
			noContent() {
				return (!this.items.length && !this.loading) ? true : false
			},

			reachedMinimum () {
				return this.counter > 2
			},

			/**
			 * Has the user just registered?
			 *
			 * @return Boolean
			 */
			isNewbie () {
			     return this.$route.query.newbie == 1
			},

			/**
			 * Is user allowed to leave this route?
			 *
			 * @return Boolean
			 */
			canLeave () {
				if (this.isNewbie) {
					return this.reachedMinimum
				}

				return true
			},

		},


        created () {
            this.getCategories()
			this.$eventHub.$on('scrolled-to-bottom', this.loadMore)
        },


        methods: {
			subscribed(index) {
				this.counter += 1

				this.items.splice(index, 1)
			},

			loadMore () {
				if ( Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems ) {
					this.getCategories()
				}
			},


            getCategories () {
				this.loading = true
				this.page ++

				axios.post('/find-categories', {
						page: this.page
					}).then((response) => {
					   this.items = [...this.items, ...response.data.data]

					   if (response.data.next_page_url == null) {
						   this.NoMoreItems = true
					   }

					   this.loading = false
				   })
            },
        },


		beforeRouteLeave(to, from, next) {
			if (!this.canLeave) {
				next(false)
			} else {
				next()
			}
		}
    };
</script>
