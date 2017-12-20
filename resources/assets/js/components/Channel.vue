<template>
	<div class="home-wrapper" id="channel">
		<channel-header></channel-header>

		<nsfw-warning v-if="nsfw"
			:text="'This channel contains NSFW content which can not be displayed according to your personal settings.'">
		</nsfw-warning>

		<router-view v-else></router-view>

		<scroll-button scrollable="submissions"></scroll-button>
	</div>
</template>

<script>

import ChannelHeader from '../components/ChannelHeader.vue';
import NsfwWarning from '../components/NsfwWarning.vue';
import ChannelSubmissions from '../components/ChannelSubmissions.vue';
import Helpers from '../mixins/Helpers';
import ScrollButton from '../components/ScrollButton.vue';


export default {
	mixins: [Helpers],

    components: {
        ChannelSubmissions,
        ChannelHeader,
		NsfwWarning, 
		ScrollButton,
    },

   	computed: {
		nsfw() {
			return Store.page.channel.temp.nsfw && !auth.nsfw;
		},
    }
}
</script>
