<template>
	<div class="padding-bottom-10 flex1 user-select"
	     id="submissions">
		<div class="left-sidebar-box">
			<div class="side-tabs">
				<router-link :to="{ name: 'moderator-panel-reported-submissions' }"
				             active-class="is-active">
					Reported Submissions
				</router-link>

				<router-link :to="{ name: 'moderator-panel-reported-comments' }"
				             active-class="is-active">
					Reported Comments
				</router-link>

				<router-link :to="{ name: 'moderator-panel-ban-users' }"
				             active-class="is-active">
					Ban Users
				</router-link>

				<router-link :to="{ name: 'moderator-panel-block-domains' }"
				             active-class="is-active">
					Block Domains
				</router-link>

				<hr v-if="isAdministrator">

				<router-link :to="{ name: 'channel-settings' }"
				             active-class="is-active"
				             v-if="isAdministrator">
					Settings
				</router-link>

				<router-link :to="{ name: 'moderator-panel-manage-moderators' }"
				             active-class="is-active"
				             v-if="isAdministrator">
					Manage Moderators
				</router-link>

				<router-link :to="{ name: 'moderator-panel-rules' }"
				             active-class="is-active"
				             v-if="isAdministrator">
					Rules
				</router-link>
			</div>

			<section class="flex1">
				<el-alert :title="newCreatedMessage"
				          v-if="justCreated"
				          class="margin-bottom-1"
				          type="success">
				</el-alert>

				<div class="content">
					<transition name="slide-fade"
					            mode="out-in">
						<router-view></router-view>
					</transition>
				</div>
			</section>
		</div>
	</div>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    data() {
        return {
            newCreatedMessage: `Congratulations on creating your new channel.
                We're gonna have a party later but for now let's set a few settings to make sure your 
                channel looks as awesome as you are.`
        };
    },

    computed: {
        /**
         * Has the user just created this channel?
         *
         * @return boolean
         */
        justCreated() {
            return this.$route.query.created == 1;
        },

        isAdministrator() {
            return (
                Store.state.administratorAt.indexOf(
                    Store.page.channel.temp.id
                ) != -1
            );
        }
    },

    beforeRouteEnter(to, from, next) {
        if (
            typeof Store.page.channel.temp.name != 'undefined' &&
            Store.page.channel.temp.name == to.params.name
        ) {
            next();
        }

        if (
            typeof Store.page.channel.temp.name != 'undefined' &&
            Store.page.channel.temp.name != to.params.name
        ) {
            Store.page.channel.clear();
        }

        if (typeof app != 'undefined') {
            app.$Progress.start();
        }

        Store.page.channel.getChannel(to.params.name).then(() => {
            next((vm) => {
                vm.$Progress.finish();
            });
        });
    },

    beforeRouteUpdate(to, from, next) {
        if (to.hash !== from.hash) return;

        if (Store.page.channel.temp.name == to.params.name) {
            next();
            return;
        }

        Store.page.channel.clear();

        this.$Progress.start();

        Store.page.channel.getChannel(to.params.name).then(() => {
            this.$Progress.finish();
            next();
        });
    }
};
</script>
