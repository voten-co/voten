<template>
    <div class="sidebar-right user-select" :class="theme">
        <el-dialog title="Customize Sidebar" :visible.sync="showSettings" :width="isMobile ? '99%' : '35%'">
            <settings></settings>
        </el-dialog>
    
        <div class="fixed-header">
            <div class="flex-space">
                <span class="menu-label">
                    <button class="feed-panel-button margin-right-half" @click="showSettings = true">
                        <i class="el-icon-setting"></i>
                    </button>
        
                    <!-- <strong>My Channels:</strong>
                    <span v-if="categoriesCount">({{ categoriesCount }})</span> -->
                </span>
            </div>
    
            <el-input :placeholder="filterForHumans + ' (' + categoriesCount + ')'" prefix-icon="el-icon-search" size="small" v-model="subscribedFilter" class="search margin-bottom-1" clearable name="subscribedFilter" autocorrect="off" autocapitalize="off" spellcheck="false">
            </el-input>
        </div>
    
        <aside class="menu">
            <div class="no-subscription" v-if="!sortedSubscribeds.length">
                <i class="v-icon v-channel" aria-hidden="true"></i> No channels to display
            </div>
    
            <ul class="menu-list" v-else>
                <li v-for="category in sortedSubscribeds" :key="category.id">
                    <router-link :to="'/c/' + category.name" active-class="active">
                        <img class="square" :src="category.avatar" :alt="category.name" v-if="showCategoryAvatars">
                        <span v-else>#</span>
    
                        <span class="v-channels-text">{{ category.name }}</span>
                    </router-link>
                </li>
            </ul>
        </aside>
    </div>
</template>

<script>
    import Helpers from '../../mixins/Helpers';
    import Settings from '../RightSidebarSettings.vue';
    
    export default {
        mixins: [Helpers],
    
        components: {
            Settings
        },
    
        data() {
            return {
                subscribedFilter: '',
                showSettings: false,
            };
        },
    
        watch: {
            '$route': function() {
                this.subscribedFilter = '';
            },
    
            // 'categoriesLimit': function() {
            //     Vue.putLS('sidebar-categories-limit', this.categoriesLimit);
            // }
        },
    
        // created() {
        //     if (Vue.isSetLS('sidebar-categories-limit')) {
        //         this.categoriesLimit = Vue.getLS('sidebar-categories-limit');
        //     }
        // },
    
        computed: {
            categories() {
                if (this.filter == 'bookmarked') {
                    return Store.state.bookmarkedCategories; 
                }
                
                if (this.filter == 'moderating') {
                    return Store.state.moderatingCategories; 
                }
                
                // subscribed
                return Store.state.subscribedCategories; 
            }, 

            categoriesCount() {
                return this.categories.length;
            },

            showCategoryAvatars() {
                return Store.settings.rightSidebar.showCategoryAvatars;
            },
    
            categoriesLimit() {
                return Store.settings.rightSidebar.categoriesLimit;
            },
    
            theme() {
                return 'theme-' + this.str_slug(Store.settings.rightSidebar.color);
            },
    
            filter() {
                return Store.settings.rightSidebar.categoriesFilter;
            },
    
            filterForHumans() {
                if (this.filter == 'subscribed') return "Subscribed Channels";
    
                if (this.filter == 'bookmarked') return "Bookmarked Channels";
    
                if (this.filter == 'moderating') return "Moderating Channels";
            },
    
            sortedSubscribeds() {
                let self = this;
    
                return _.orderBy(
                    self.categories
                        .filter(category => category.name.toLowerCase().indexOf(self.subscribedFilter.toLowerCase()) !== -1), 'subscribers', 'desc')
                        .slice(0, (self.categoriesLimit > 2 ? self.categoriesLimit : 2)
                ); 
            },
        }
    }
</script>
