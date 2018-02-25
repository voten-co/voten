<template>
	<div class="home-wrapper">	
		<div class="align-center user-select padding-sides-1" v-if="isNewbie">
			<h2 v-if="isNewbie && !reachedMinimum">
				Please subscribe to
				<b>{{ 3 - subscribedChannelsCount }}</b> more channels to continue
			</h2>

			<transition name="fade">
				<div class="text-or-button padding-1" v-if="isNewbie && reachedMinimum">
					<el-button round 
						@click="$router.push({name: 'home', query: { newbie: 1, sidebar: 1 }})"
						type="primary"
					>
						Start Voting <i class="el-icon-arrow-right el-icon-right"></i>
					</el-button>
				</div>
			</transition>

			<el-input :placeholder="'Search by #name or description...'" 
				v-model="searchFilter" class="input-with-select" clearable @input="search(searchFilter)"
				:prefix-icon="loading && searchFilter ? 'el-icon-loading' : 'el-icon-search'" v-if="isNewbie">
			</el-input>
		</div>

		<div class="find-channels-filters-wrapper user-select" v-if="! isNewbie">
			<el-input :placeholder="'Search by #name or description...'" v-model="searchFilter" class="input-with-select" clearable @input="search(searchFilter)"
			    :prefix-icon="loading && searchFilter ? 'el-icon-loading' : 'el-icon-search'">
			</el-input>

			<div class="flex-space margin-top-1">
				<div>
					<small class="margin-right-1">
						Sort by:
					</small>

					<el-radio-group v-model="orderBy" size="mini" @change="changeOrder">
						<el-radio-button label="None"></el-radio-button>
						<el-radio-button label="Subscribers"></el-radio-button>
						<el-radio-button label="Activity"></el-radio-button>
						<el-radio-button label="Newest"></el-radio-button>
						<el-radio-button label="Oldest"></el-radio-button>
					</el-radio-group>					
				</div>

				<el-checkbox v-model="excludeSubscribeds" @change="excludeSubscribedsChanged">
					Exclude subscribed
				</el-checkbox>
			</div>
		</div>

		<section class="home-submissions" v-infinite-scroll="loadMore" infinite-scroll-disabled="cantLoadMore">
			<div class="index-channels">
				<bookmarked-channel v-for="item in items" :key="item.id" :list="item"></bookmarked-channel>				
			</div>

			<loading v-show="loading"></loading>		
			<no-content v-if="noContent" :text="'No results were found with current filters.'"></no-content>
			<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !noContent"></no-more-items>
		</section>
	</div>
</template>


<script>
import BookmarkedChannel from '../components/BookmarkedChannel.vue';
import NoContent from '../components/NoContent.vue';
import NoMoreItems from '../components/NoMoreItems.vue';
import Loading from '../components/Loading.vue';

export default {
    components: {
        BookmarkedChannel,
        NoContent,
        NoMoreItems,
        Loading
    },

    data() {
        return {
            Store,
            auth,
            NoMoreItems: false,
            loading: true,
            items: [],
            page: 0,
            searchFilter: '',
            excludeSubscribeds: true,
            orderBy: 'None'
        };
    },

    computed: {
        cantLoadMore() {
            return this.loading || this.NoMoreItems || this.nothingFound;
        },

        noContent() {
            return !this.items.length && !this.loading ? true : false;
        },

        reachedMinimum() {
            return Store.state.subscribedChannels.length > 2;
        },

        subscribedChannelsCount() {
            return Store.state.subscribedChannels.length;
        },

        /**
         * Has the user just registered?
         *
         * @return Boolean
         */
        isNewbie() {
            return this.$route.query.newbie == 1;
        },

        /**
         * Is user allowed to leave this route?
         *
         * @return Boolean
         */
        canLeave() {
            if (this.isNewbie) {
                return this.reachedMinimum;
            }

            return true;
        }
    },

    created() {
        this.getChannels();
    },

    methods: {
        excludeSubscribedsChanged() {
            this.clear();
            this.searchFilter ? this.search() : this.getChannels();
        },

        changeOrder(order) {
            this.clear();
            this.searchFilter = '';
            this.getChannels();
        },

        loadMore() {
            if (
                !this.loading &&
                !this.NoMoreItems &&
                !this.searchFilter.trim()
            ) {
                this.getChannels();
            }
        },

        getChannels() {
            this.loading = true;
            this.page++;

            axios
                .get('/channels/discover', {
                    params: {
                        page: this.page,
                        order_by: this.orderBy,
                        exclude_subscribeds: this.excludeSubscribeds
                    }
                })
                .then((response) => {
                    this.items = [...this.items, ...response.data.data];

                    if (response.data.links.next == null)
                        this.NoMoreItems = true;

                    this.loading = false;
                })
                .catch((error) => {
                    this.loading = false;
                });
        },

        clear() {
            this.items = [];
            this.page = 0;
            this.NoMoreItems = false;
        },

        search: _.debounce(function() {
            if (!this.searchFilter.trim()) {
                this.clear();
                this.getChannels();

                return;
            }

            this.clear();
            this.orderBy = '';
            this.loading = true;

            axios
                .get('/channels/discover', {
                    params: {
                        filter: this.searchFilter,
                        exclude_subscribeds: this.excludeSubscribeds
                    }
                })
                .then((response) => {
                    this.items = response.data.data;

                    if (response.data.links.next == null)
                        this.NoMoreItems = true;

                    this.loading = false;
                });
        }, 600)
    },

    beforeRouteLeave(to, from, next) {
        if (!this.canLeave) {
            next(false);
        } else {
            next();
        }
    }
};
</script>

<style>
.find-channels-filters-wrapper {
    padding: 1em;
    border-bottom: 2px solid #5587d7;
}
</style>
