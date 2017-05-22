<template>
	<div>
		<admin-panel-header></admin-panel-header>

		<router-view></router-view>
	</div>
</template>

<script>
	import AdminPanelHeader from '../components/AdminPanelHeader.vue'

    export default {
        components: {
        	AdminPanelHeader
        },

        data: function () {
            return {
                statistics: []
            }
        },

        created () {
            this.getStatistics()
        },

        mounted () {
			this.$nextTick(function () {
				this.$root.loadSemanticTooltip()
			})
        },

        methods: {
            getStatistics () {
            	axios.post('/big-daddy').then((response) => {
	                this.statistics = response.data
	            })
            },
        }
    };
</script>
