<template>
	<div class="home-wrapper" id="channel">
		<channel-header></channel-header>

		<nsfw-warning v-if="notSafeForWorkWarning" :text="text"></nsfw-warning>

		<router-view v-else></router-view>
	</div>
</template>

<script>
import ChannelHeader from '../components/ChannelHeader.vue';
import NsfwWarning from '../components/NsfwWarning.vue';
import ChannelSubmissions from '../components/ChannelSubmissions.vue';
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    components: {
        ChannelSubmissions,
        ChannelHeader,
        NsfwWarning
    },

    computed: {
        notSafeForWorkWarning() {
            return (
                Store.page.channel.temp.nsfw &&
                !Store.settings.feed.include_nsfw_submissions
            );
        },

        text() {
            return this.isLoggedIn
                ? 'This channel contains NSFW content which can not be displayed according to your personal settings.'
                : 'This channel contains NSFW content which can not be displayed to guests.';
        }
    }
};
</script>
