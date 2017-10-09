<template>
    <div class="sidebar-right">
        <div class="fixed-header user-select">
            <div class="flex-space">
                <p class="menu-label">
                    <strong>My Channels:</strong>
                    <span v-if="Store.subscribedCategories.length">({{ Store.subscribedCategories.length }})</span>
                </p>

                <div class="ui icon top right active-blue pointing dropdown sidebar-panel-button">
                    <i class="v-icon v-config"></i>

                    <div class="menu">
                        <div class="header">
                            Limit to
                        </div>

                        <div class="ui left input">
                            <input type="number" name="search" placeholder="Limit at..." min="2" spellcheck="false" v-model="categoriesLimit">
                        </div>

                        <div class="header">
                            Filter by
                        </div>
                        <button class="item" @click="changeFilter('subscribed-channels')" :class="{ 'active' : filter == 'subscribed-channels' }">
                            Subscribed channels
                        </button>

                        <button class="item" @click="changeFilter('moderating-channels')" :class="{ 'active' : filter == 'moderating-channels' }" v-if="isModerating">
                            Moderating channels
                        </button>

                        <button class="item" @click="changeFilter('bookmarked-channels')" :class="{ 'active' : filter == 'bookmarked-channels' }">
                            Bookmarked channels
                        </button>
                    </div>
                </div>
            </div>

            <div class="ui category search side-box-search">
                <div class="ui mini icon input">
                    <input class="prompt" type="text" :placeholder="filterForHumans + '...'" v-model="subscribedFilter">
                    <i class="v-icon v-search search icon"></i>
                </div>
            </div>
        </div>

        <aside class="menu">
            <div class="no-subscription" v-if="!sortedSubscribeds.length && !Store.loading">
                <i class="v-icon v-sad" aria-hidden="true"></i>
                No channels to display
            </div>

            <ul class="menu-list" v-else>
                <li v-for="category in sortedSubscribeds">
                    <router-link :to="'/c/' + category.name" active-class="active">
                        <img class="square" v-bind:src="category.avatar" v-bind:alt="category.name">
                        <span class="v-channels-text">{{ category.name }}</span>
                    </router-link>
                </li>
            </ul>
        </aside>
    </div>
</template>

<script>
    import Helpers from '../../mixins/Helpers';
    import LocalStorage from '../../mixins/LocalStorage';

    export default {
        mixins: [Helpers, LocalStorage],

        data: function() {
            return {
                subscribedFilter: '',
                categoriesLimit: 50,
            };
        },

        watch: {
            '$route': function() {
                this.subscribedFilter = ''; 
            },

            'categoriesLimit': function() {
                this.putLS('sidebar-categories-limit', this.categoriesLimit);
            }
        },

        created() {
            if (this.isSetLS('sidebar-categories-limit')) {
                this.categoriesLimit = this.getLS('sidebar-categories-limit');
            }
        },

        computed: {
            filter() {
                return Store.sidebarFilter;
            },

            filterForHumans() {
                if (this.filter == 'subscribed-channels') return "Subscribed Channels";

                if (this.filter == 'bookmarked-channels') return "Bookmarked Channels";

                if (this.filter == 'moderating-channels') return "Moderating Channels";
            },  

            sortedSubscribeds() {
                let self = this; 

                return _.orderBy(Store.subscribedCategories.filter(function(category) {
                    return category.name.toLowerCase().indexOf(self.subscribedFilter.toLowerCase()) !== -1
                }), 'subscribers', 'desc').slice(0, (this.categoriesLimit > 2 ? this.categoriesLimit : 2))
            },
        },

        methods: {
            /**
             * changes the filter for sidebar
             *
             * @return void
             */
            changeFilter(filter) {
                if (Store.sidebarFilter == filter) return;

                Store.sidebarFilter = filter;

                this.putLS('sidebar-filter', filter);

                axios.get(this.authUrl('sidebar-categories'), {
                    params: {
                        sidebar_filter: Store.sidebarFilter
                    }
                }).then((response) => {
                    Store.subscribedCategories = response.data;
                });
            }
        }
    }
</script>
