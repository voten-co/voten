<template>
    <button class="v-menu-button" :class="{'v-menu-button--checked' : checked}" @click="toggle"
    data-toggle="tooltip" data-placement="right" title="Toggle Sidebar">
        <span></span>
    </button>
</template>

<script>
	export default {
	    props: ['checked'],

		mounted () {
			this.$nextTick(function () {
				this.$root.loadSemanticTooltip();
			});
		},

	    methods: {
	    	/**
	    	 * Toggles the sidebar
	    	 *
	    	 * @return void
	    	 */
	    	toggle() {
	    		this.checked = ! this.checked
	    		this.$eventHub.$emit('toggle-sidebar')
	    	}
	    }
	}
</script>
