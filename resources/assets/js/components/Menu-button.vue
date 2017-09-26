<template>
    <button class="v-menu-button" :class="{'v-menu-button--checked' : checked}" @click="toggle" v-tooltip.right="'Toggle Sidebar'">
        <span></span>
    </button>
</template>

<script>
	export default {
	    props: ['checked'],

	    methods: {
	    	/**
	    	 * Toggles the sidebar
	    	 *
	    	 * @return void
	    	 */
	    	toggle() {
	    		this.checked =! this.checked;
	    		this.$eventHub.$emit('toggle-sidebar');
	    	}
	    }
	}
</script>
