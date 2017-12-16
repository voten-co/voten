<template>
    <div class="sidebar-right user-select" :class="theme">
        <div class="fixed-header">
            <div class="flex-space">
                <p class="menu-label">
                    <strong>My Channels:</strong>
                    <span v-if="Store.state.subscribedCategories.length">({{ Store.state.subscribedCategories.length }})</span>
                </p>

                <!--<div class="ui icon top right active-blue pointing dropdown sidebar-panel-button">-->
                    <!--<i class="v-icon v-config" v-tooltip.left="{content: 'Customize Sidebar Filter'}"></i>-->

                    <!--<div class="menu">-->
                        <!--<div class="header">-->
                            <!--Limit to-->
                        <!--</div>-->

                        <!--<div class="ui left input">-->
                            <!--<input type="number" name="search" placeholder="Limit at..." min="2" spellcheck="false" v-model="categoriesLimit">-->
                        <!--</div>-->

                        <!--<div class="header">-->
                            <!--Filter by-->
                        <!--</div>-->
                        <!--<button class="item" @click="changeFilter('subscribed-channels')" :class="{ 'active' : filter == 'subscribed-channels' }">-->
                            <!--Subscribed channels-->
                        <!--</button>-->

                        <!--<button class="item" @click="changeFilter('moderating-channels')" :class="{ 'active' : filter == 'moderating-channels' }" v-if="isModerating">-->
                            <!--Moderating channels-->
                        <!--</button>-->

                        <!--<button class="item" @click="changeFilter('bookmarked-channels')" :class="{ 'active' : filter == 'bookmarked-channels' }">-->
                            <!--Bookmarked channels-->
                        <!--</button>-->
                    <!--</div>-->
                <!--</div>-->
            </div>

            <el-input
                    :placeholder="filterForHumans + '...'"
                    prefix-icon="el-icon-search"
                    size="small"
                    v-model="subscribedFilter"
                    class="search margin-bottom-1"
                    clearable
                    name="subscribedFilter"
                    autocorrect="off" 
                    autocapitalize="off" 
                    spellcheck="false"
            >
            </el-input>
        </div>

        <aside class="menu">
            <div class="no-subscription" v-if="!sortedSubscribeds.length">
                <i class="v-icon v-sad" aria-hidden="true"></i>
                No channels to display
            </div>

            <ul class="menu-list" v-else>
                <li v-for="category in sortedSubscribeds" :key="category.id">
                    <router-link :to="'/c/' + category.name" active-class="active">
                        <img class="square" :src="category.avatar" :alt="category.name">
                        <span class="v-channels-text">{{ category.name }}</span>
                    </router-link>
                </li>
            </ul>
        </aside>
    </div>
</template>

<script>
    import Helpers from '../../mixins/Helpers';

    export default {
        mixins: [Helpers],

        data() {
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
                Vue.putLS('sidebar-categories-limit', this.categoriesLimit);
            }
        },

        created() {
            if (Vue.isSetLS('sidebar-categories-limit')) {
                this.categoriesLimit = Vue.getLS('sidebar-categories-limit');
            }
        },

        computed: {
            theme() {
                return 'theme-' + this.str_slug(auth.sidebar_color); 
            }, 

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

                return _.orderBy(Store.state.subscribedCategories.filter(function(category) {
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

                Vue.putLS('sidebar-filter', filter);

                axios.get(this.authUrl('sidebar-categories'), {
                    params: {
                        sidebar_filter: Store.sidebarFilter
                    }
                }).then((response) => {
                    Store.state.subscribedCategories = response.data;
                });
            }
        },
    }
</script>
