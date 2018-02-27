<template>
	<div class="home-wrapper">
		<admin-panel-header></admin-panel-header>

		<router-view></router-view>
	</div>
</template>

<script>
import AdminPanelHeader from '../components/AdminPanelHeader.vue';

export default {
    components: {
        AdminPanelHeader
    },

    data() {
        return {
            statistics: []
        };
    },

    beforeRouteEnter(to, from, next) {
        axios.post('/admin/check').then((response) => {
            if (response.data == true) {
                next();
            }

            next(false);
        });
    }
};
</script>
