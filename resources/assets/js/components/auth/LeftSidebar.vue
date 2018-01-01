
<template>
    <div class="sidebar-left user-select">
        <div class="top">
            <!-- Home -->
            <el-tooltip content="Home Feed (H)" placement="right" transition="false" :open-delay="500">
                <a @click.prevent="goHome" href="/" :class="{ 'active': activeRoute === 'home' }" class="item">
                    <i class="v-icon v-home" aria-hidden="true"></i>
                </a>
            </el-tooltip>

            <!-- Notifications -->
            <el-tooltip content="Notifications (N)" placement="right" transition="false" :open-delay="500">
                <a class="item" :class="{'active' : activeRoute === 'notifications'}" @click="Store.showNotifications = true">
                    <el-badge :value="unreadNotifications" :max="99">
                        <i class="el-icon-bell" aria-hidden="true"></i>
                    </el-badge>
                </a>
            </el-tooltip>

            <!-- Messages Inbox -->
            <el-tooltip content="Messages (M)" placement="right" transition="false" :open-delay="500">
                <a class="item" id="messages-btn" :class="{'active' : activeRoute === 'messages'}" @click="changeRoute('messages')">
                    <el-badge :value="unreadMessages" :max="99">
                        <i class="v-icon v-inbox" aria-hidden="true"></i>
                    </el-badge>
                </a>
            </el-tooltip>

            <!-- Bookmarks -->
            <el-tooltip content="Bookmarks (B)" placement="right" transition="false" :open-delay="500">
                <a @click.prevent="pushRouter('/bookmarks')" href="/bookmarks" class="item" :class="{'active' : activeRoute === 'bookmarks'}">
                    <i class="v-icon v-bookmark" aria-hidden="true"></i>
                </a>
            </el-tooltip>

            <!-- Search -->
            <el-tooltip content="Search (/)" placement="right" transition="false" :open-delay="500">
                <a class="item" @click="changeRoute('search')" :class="{'active' : activeRoute === 'search'}">
                    <i class="el-icon-search" aria-hidden="true"></i>
                </a>
            </el-tooltip>

            <!-- Settings -->
            <el-tooltip content="Preferences" placement="right" transition="false" :open-delay="500">
                <a class="item" @click.prevent="Store.showPreferences = true"
                   :class="{'active' : activeRoute === 'settings'}">
                    <i class="el-icon-setting" aria-hidden="true"></i>
                </a>
            </el-tooltip>

            <!-- Submit -->
            <el-tooltip content="Add Content" placement="right" transition="false" :open-delay="500">
                <a class="item" @click="$eventHub.$emit('submit')" :class="{'active' : activeRoute === 'submit'}">
                    <i class="el-icon-plus" aria-hidden="true"></i>
                </a>
            </el-tooltip>
        </div>

        <div class="bottom">
            <!-- admin buttons -->
            <el-tooltip content="Backend Dashboard" placement="right" transition="false" :open-delay="500" v-if="auth.isVotenAdminstrator">
                <a class="item" href="/backend" target="_blank">
                    <i class="el-icon-service" aria-hidden="true"></i>
                </a>
            </el-tooltip>
            
            <el-tooltip content="Big-daddy Dashboard" placement="right" transition="false" :open-delay="500" v-if="auth.isVotenAdminstrator">
                <a class="item" @click.prevent="pushRouter('/big-daddy')" href="/big-daddy">
                    <i class="el-icon-view" aria-hidden="true"></i>
                </a>
            </el-tooltip>

            <!-- Help Center -->
            <el-tooltip content="Help Center" placement="right" transition="false" :open-delay="500">
                <a class="item" href="https://help.voten.co/" target="_blank">
                    <i class="el-icon-question" aria-hidden="true"></i>
                </a>
            </el-tooltip>
        </div>
    </div>
</template>

<script>
    import Helpers from '../../mixins/Helpers';

    export default {
        mixins: [Helpers],

        computed: {
            submitURL() {
                return this.$route.params.name ? "/submit?channel=" + this.$route.params.name : "/submit";
            }
        }, 
   
        computed: {
            contentRoute() {
                return Store.contentRouter; 
            }, 

            activeRoute() {
                if (this.contentRoute === 'messages') {
                    return 'messages';
                }

                if (this.contentRoute === 'search') {
                    return 'search';
                }

                if (this.$route.name === 'home') {
                    return 'home';
                }

                if (this.$route.name === 'bookmarked-submissions' || this.$route.name === 'bookmarked-comments' || this.$route.name === 'bookmarked-users' || this.$route.name === 'bookmarked-channels') {
                    return 'bookmarks';
                }
                
                if (this.$route.name === 'submit') {
                    return 'submit';
                }
            }, 

            unreadNotifications() {
                return Store.state.notifications.filter(item => item.read_at == null).length;
            },

            unreadMessages() {
                return Store.state.contacts.filter(item => item.last_message.owner.id != auth.id && item.last_message.read_at == null).length;
            },
        }, 

        methods: {
            changeRoute(route) {
                this.$eventHub.$emit('change-route', route);
            }, 

            pushRouter(route) {
                this.$eventHub.$emit('close');
                
                this.$router.push(route);
            }, 

            goHome() {
                if (this.$route.name != 'home') {
                    Store.page.home.clear();                    
                }

                this.pushRouter('/'); 
            }, 
        }
    }
</script>

