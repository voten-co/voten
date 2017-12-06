<template>
	<div class="home-wrapper" id="category">
		<category-header></category-header>

		<nsfw-warning v-if="nsfw"
			:text="'This channel contains NSFW content which can not be displayed according to your personal settings.'">
		</nsfw-warning>

		<router-view v-else></router-view>

		<scroll-button scrollable="submissions"></scroll-button>
	</div>
</template>

<script>

import CategoryHeader from '../components/CategoryHeader.vue';
import NsfwWarning from '../components/NsfwWarning.vue';
import CategorySubmissions from '../components/CategorySubmissions.vue';
import Helpers from '../mixins/Helpers';
import ScrollButton from '../components/ScrollButton.vue';


export default {
	mixins: [Helpers],

    components: {
        CategorySubmissions,
        CategoryHeader,
		NsfwWarning, 
		ScrollButton,
    },

   	computed: {
		nsfw() {
			return Store.page.category.temp.nsfw && !auth.nsfw;
		},
    }
}
</script>
