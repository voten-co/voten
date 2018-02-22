<template>
	<li class="go-black">
	    <router-link :to="'/@' + list.username">
	    	<div class="user-search-item-avatar">
		        <img v-bind:src="list.avatar" v-bind:alt="list.username" />
		    </div>

		    <div class="flex1">
		        <h3>@{{ list.username }} <span class="v-contact-username">{{ list.name }}</span></h3>
		        <p>
		            {{ list.bio }}
		        </p>
		    </div>
		    <div class="channel-search-item-arrow">
		    	<i class="v-icon flaticon-right-arrow-2"></i>
		    </div>
	    </router-link>
	</li>
</template>

<script>
export default {
    props: ['list']
};
</script>
