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

    data() {
        return {
			isActive: null,
        	Store,
			auth
        }
	},
	
	activated() {
		this.isActive = true;
	},

	deactivated() {
		this.isActive = false;
	}, 

   	created() {
   		this.setPageTitle('#' + this.$route.params.name);
	},

    watch: {
		'$route': function () {
			if (this.isActive === false) return;

			// if (this.$route.name !== 'submission-page') {
			// 	this.$destroy();
			// }

			this.$root.getCategoryStore(this.$route.params.name);
			this.setPageTitle('#' + this.$route.params.name);
		}
	},

   	computed: {
		nsfw() {
			return Store.page.category.nsfw && !auth.nsfw;
		},
    }
}
</script>
