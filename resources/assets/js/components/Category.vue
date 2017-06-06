<template>
	<section>
		<category-header v-if="loaded"></category-header>

		<nsfw-warning v-if="nsfw"
			:text="'This channel contains NSFW content which can not be displayed according to your personal settings.'">
		</nsfw-warning>

		<router-view v-else></router-view>
	</section>
</template>

<script>

import CategoryHeader from '../components/CategoryHeader.vue';
import NsfwWarning from '../components/NsfwWarning.vue';
import CategorySubmissions from '../components/CategorySubmissions.vue';
import Helpers from '../mixins/Helpers';

export default {
	mixins: [Helpers],

    components: {
        CategorySubmissions,
        CategoryHeader,
		NsfwWarning
    },

    data () {
        return {
        	Store,
			auth
        }
    },

   	created () {
   		this.updateCategoryStore();
   		this.setPageTitle('#' + this.$route.params.name);
   	},

    watch: {
		'$route': function () {
			this.updateCategoryStore();
		}
	},

   	computed: {
		nsfw(){
			return Store.category.nsfw && !auth.nsfw;
		},

   		/**
		 * Are we good to go (Dsiplay header)
   		 *
   		 * @return Boolean
   		 */
        loaded () {
            return Store.category.name == this.$route.params.name;
        }
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
