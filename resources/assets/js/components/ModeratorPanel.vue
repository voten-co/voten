<template>
    <div class="padding-bottom-10 flex1 user-select" id="submissions">
        <div class="v-status v-status--success" v-if="justCreated">
            Congratulations on creating #{{ $route.params.name }}.
            We're gonna have a party later but for now let's set a few settings to make sure your channel looks as awesome as you are.
        </div>

        <div class="left-sidebar-box">
            <div class="side-tabs">
                <router-link :to="{ name: 'moderator-panel-reported-submissions' }" active-class="is-active">
					Reported Submissions
				</router-link>

                <router-link :to="{ name: 'moderator-panel-reported-comments' }" active-class="is-active">
					Reported Comments
				</router-link>

                <router-link :to="{ name: 'moderator-panel-ban-users' }" active-class="is-active">
					Ban Users
				</router-link>

                <router-link :to="{ name: 'moderator-panel-block-domains' }" active-class="is-active">
					Block Domains
				</router-link>

                <router-link :to="{ name: 'moderator-panel-rules' }" active-class="is-active"
                v-if="isAdministrator">
					Rules
				</router-link>

                <router-link :to="{ name: 'moderator-panel-manage-moderators' }" active-class="is-active"
                v-if="isAdministrator">
					Manage Moderators
				</router-link>

                <router-link :to="{ name: 'category-settings' }" active-class="is-active"
                v-if="isAdministrator">
					Settings
				</router-link>
            </div>

            <div class="content">
                <transition name="slide-fade" mode="out-in">
                    <keep-alive>
                        <router-view></router-view>
                    </keep-alive>
                </transition>
            </div>
        </div>
    </div>
</template>

<script>

    export default {

        data: function () {
            return {
                Store
            }
        },

        props: {
            //
        },

        computed: {
            /**
			 * Has the user just created this category?
			 *
			 * @return Boolean
			 */
			justCreated() {
			     return this.$route.query.created == 1
			},

            isAdministrator () {
        		return Store.administratorAt.indexOf(Store.category.id) != -1
        	},
        },

        created () {
            //
        },

        mounted () {
            //
        },

        methods: {
            //
        },

        beforeRouteEnter(to, from, next){
            if (Store.category.name == to.params.name) {
                // loaded
                if (Store.moderatingAt.indexOf(Store.category.id) != -1) {
                    next()
                }
            } else {
                // not loaded but let's continue (the server-side is still protecting us!)
                next()
            }
        },
    };
</script>
