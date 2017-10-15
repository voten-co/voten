<template>
	<div class="container margin-top-1 col-7 user-select">
		<div class="margin-bottom-1 align-center" v-if="!isNewbie">
			<h1>Find Channels</h1>
		</div>

		<div class="margin-top-bottom-1 align-center" v-if="isNewbie">
			<h2>
				Welcome to Voten, {{ auth.username }}
			</h2>

			<h1 v-if="isNewbie && !reachedMinimum">
				Please subscribe to <b>{{ 3 - subscribedCategoriesCount }}</b> more channels
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

		<div class="find-channels-filters-wrapper">
			<div class="ui massive icon input flex-search margin-top-bottom-1">
				<input type="text" placeholder="Search by #name or description..." v-model="searchFilter" v-on:input="search(searchFilter)">
				<i class="v-icon v-search search icon"></i>
			</div>

			<div class="flex-space">
				<div>
					<ul class="flat-nav">
						<li class="item" :class="{ 'active': orderBy == 'subscribers' }" @click="changeOrder('subscribers')">
							Subscribers
						</li>

						<li class="item" :class="{ 'active': orderBy == 'new' }" @click="changeOrder('new')">
							New
						</li>

						<li class="item" :class="{ 'active': orderBy == 'activity' }" @click="changeOrder('activity')">
							Activity
						</li>
					</ul>
				</div>

				<toggle-button v-model="excludeSubscribeds"
							   :width="200"
							   :height="30"
							   :color="{checked: '#5f85d1', unchecked: '#78b38a'}"
							   :labels="{checked: 'Exclude Subscribed Channels', unchecked: 'Include Subscribed Channels'}"
							   v-if="!isNewbie"
				/>
			</div>
		</div>

		<bookmarked-category v-for="(value, index) in items" :key="value.id"
			 :list="value" @subscribed="subscribed(index)"></bookmarked-category>

		<loading v-show="loading"></loading>

		<no-content v-if="noContent" :text="'No results were found with current filters.'"></no-content>

		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !noContent"></no-more-items>
	</div>
</template>


<script>
    import BookmarkedCategory from '../components/BookmarkedCategory.vue';
    import NoContent from '../components/NoContent.vue';
	import NoMoreItems from '../components/NoMoreItems.vue';
	import Loading from '../components/Loading.vue';


export default {
        components: {
            BookmarkedCategory,
			NoContent,
			NoMoreItems,
            Loading
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
				searchFilter: '',
                excludeSubscribeds: true,
                orderBy: ''
            }
        },

		computed: {
			noContent() {
				return (!this.items.length && !this.loading) ? true : false
			},

			reachedMinimum () {
				return this.counter > 2
			},

			subscribedCategoriesCount() {
			   return Store.subscribedCategories.length;
			},

			/**
			 * Has the user just registered?
			 *
			 * @return Boolean
			 */
			isNewbie () {
			     return this.$route.query.newbie == 1;
			},

			/**
			 * Is user allowed to leave this route?
			 *
			 * @return Boolean
			 */
			canLeave () {
				if (this.isNewbie) {
					return this.reachedMinimum;
				}

				return true;
			},

		},

        created () {
            this.getCategories();
			this.$eventHub.$on('scrolled-to-bottom', this.loadMore);
        },

        watch: {
            'excludeSubscribeds' () {
                this.clear();

                this.searchFilter ? this.search() : this.getCategories();
            },
        },

        methods: {
            changeOrder(order) {
                if (order == this.orderBy) return;

                this.clear();

                this.searchFilter = '';

               	this.orderBy = order;

               	this.getCategories();
			},

			subscribed(index) {
				this.counter += 1
			},

			loadMore () {
				if ( Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems && !this.searchFilter.trim() ) {
					this.getCategories();
				}
			},


            getCategories () {
				this.loading = true;
				this.page ++;

				axios.get('/find-categories', {
					params: {
						page: this.page,
						order_by: this.orderBy,
                        exclude_subscribeds: this.excludeSubscribeds
					}
				}).then((response) => {
				   	this.items = [...this.items, ...response.data.data];

				   	if (response.data.next_page_url == null) {
					   	this.NoMoreItems = true;
				   	}

				   	this.loading = false;
			   })
            },

            clear() {
                this.items = [];
                this.page = 0;
                this.NoMoreItems = false;
			},

            search: _.debounce(function () {
                if(!this.searchFilter.trim()) {
                    this.clear();
                    this.getCategories();

                    return;
				}

                this.clear();
                this.orderBy = '';
                this.loading = true;

                axios.get('/find-categories', {
                    params: {
                        filter: this.searchFilter,
                        exclude_subscribeds: this.excludeSubscribeds
                    }
                }).then((response) => {
                    this.items = response.data;

                    this.loading = false;
                });
            }, 600),
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

<style>
	.find-channels-filters-wrapper {
		margin-bottom: 2em;
		padding: 1em;
		border: 1px solid #eaeaea;
		border-radius: 4px;
		border-bottom: 2px solid #5587d7;
	}
</style>