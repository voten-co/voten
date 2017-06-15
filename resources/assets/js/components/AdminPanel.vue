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

            },
        },

        beforeRouteEnter(to, from, next){
        	axios.post('/big-daddy').then((response) => {
                if (response.data == true) {
                	next();
                }

                next(false);
            })
        },
    };
</script>
