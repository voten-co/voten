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
                    <span v-if="channelsCount">({{ channelsCount }})</span> -->
                </span>
            </div>
    
            <el-input :placeholder="filterForHumans + ' (' + channelsCount + ')'" prefix-icon="el-icon-search" size="small" v-model="subscribedFilter" class="search margin-bottom-1" clearable name="subscribedFilter" autocorrect="off" autocapitalize="off" spellcheck="false">
            </el-input>
        </div>
    
        <aside class="menu">
            <div class="no-subscription" v-if="!sortedSubscribeds.length">
                <i class="v-icon v-channel" aria-hidden="true"></i> No channels to display
            </div>
    
            <ul class="menu-list" v-else>
                <li v-for="channel in sortedSubscribeds" :key="channel.id">
                    <router-link :to="'/c/' + channel.name" active-class="active">
                        <img class="square" :src="channel.avatar" :alt="channel.name" v-if="showChannelAvatars">
                        <span v-else>#</span>
    
                        <span class="v-channels-text">{{ channel.name }}</span>
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
    
            // 'channelsLimit': function() {
            //     Vue.putLS('sidebar-channels-limit', this.channelsLimit);
            // }
        },
    
        // created() {
        //     if (Vue.isSetLS('sidebar-channels-limit')) {
        //         this.channelsLimit = Vue.getLS('sidebar-channels-limit');
        //     }
        // },
    
        computed: {
            channels() {
                if (this.filter == 'bookmarked') {
                    return Store.state.bookmarkedChannels; 
                }
                
                if (this.filter == 'moderating') {
                    return Store.state.moderatingChannels; 
                }
                
                // subscribed
                return Store.state.subscribedChannels; 
            }, 

            channelsCount() {
                return this.channels.length;
            },

            showChannelAvatars() {
                return Store.settings.rightSidebar.showChannelAvatars;
            },
    
            channelsLimit() {
                return Store.settings.rightSidebar.channelsLimit;
            },
    
            theme() {
                return 'theme-' + this.str_slug(Store.settings.rightSidebar.color);
            },
    
            filter() {
                return Store.settings.rightSidebar.channelsFilter;
            },
    
            filterForHumans() {
                if (this.filter == 'subscribed') return "Subscribed Channels";
    
                if (this.filter == 'bookmarked') return "Bookmarked Channels";
    
                if (this.filter == 'moderating') return "Moderating Channels";
            },
    
            sortedSubscribeds() {
                let self = this;
    
                return _.orderBy(
                    self.channels
                        .filter(channel => channel.name.toLowerCase().indexOf(self.subscribedFilter.toLowerCase()) !== -1), 'subscribers', 'desc')
                        .slice(0, (self.channelsLimit > 2 ? self.channelsLimit : 2)
                ); 
            },
        }
    }
</script>
