<template>
	<section>
		<category-header></category-header>

		<nsfw-warning v-if="nsfw"
			:text="'This channel contains NSFW content which can not be displayed according to your personal settings.'">
		</nsfw-warning>

		<router-view v-else></router-view>

		<scroll-button scrollable="v-content"></scroll-button>
	</section>
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
		ScrollButton
    },

    data () {
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

   	created () {
   		this.updateCategoryStore();
   		this.setPageTitle('#' + this.$route.params.name);
   	},

    watch: {
		'$route': function () {
			if (this.isActive === false) return;

			this.updateCategoryStore();
			this.setPageTitle('#' + this.$route.params.name);
		}
	},

   	computed: {
		nsfw() {
			return Store.category.nsfw && !auth.nsfw;
		},
    },

    methods: {
    	/**
    	 * Checks wheather or not the categoryStore needs to be filled or updated, and if yes simply does it
    	 *
    	 * @return void
    	 */
    	updateCategoryStore () {
    		if (Store.category.name == undefined || Store.category.name != this.$route.params.name) {
	    		this.$root.getCategoryStore(this.$route.params.name);
    		}
    	},
    }
}
</script>
