<template>
	<div>
		<user-header v-if="loaded"></user-header>

		<router-view></router-view>
	</div>
</template>

<script>
import UserHeader from '../components/UserHeader.vue'

export default {
    components: {
    	UserHeader
    },

    data () {
    	return {
			isActive: null, 
    		Store
    	}
    },

    created () {
    	this.updateUserStore()
	},
	
	activated() {
		this.isActive = true;
	}, 
	deactivated() {
		this.isActive = false;
	}, 

    watch: {
    	'$route': function () {
			if (this.isActive === true) {
				this.updateUserStore(); 
			}
    	}
    },

    methods: {
    	/**
    	 * Checks if the Store.user is filled with right info, if it's not fetches the right ones
    	 *
    	 * @return void
    	 */
    	updateUserStore () {
        	this.$root.getUserStore()
    	}
    },

    computed: {
    	loaded () {
    		return Store.user.username == this.$route.params.username
    	}
    }
}
</script>
