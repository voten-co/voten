<template>
	<div class="home-wrapper" id="user">
		<user-header></user-header>
	
		<router-view></router-view>
	</div>
</template>

<script>
import UserHeader from '../components/UserHeader.vue';

export default {
    components: {
        UserHeader
    },

    beforeRouteEnter(to, from, next) {
        Store.page.user.submissions.clear();
        Store.page.user.comments.clear();
        Store.page.user.upVotedSubmissions.clear();
        Store.page.user.downVotedSubmissions.clear();

        next();
    }
};
</script>
