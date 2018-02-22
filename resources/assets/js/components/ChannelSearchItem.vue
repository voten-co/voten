<template>
	<li class="go-black">
	    <router-link :to="'/c/' + list.name">
	    	<div class="channel-search-item-avatar">
		        <img v-bind:src="list.avatar" v-bind:alt="list.name" />
		    </div>

		    <div class="v-contact">
		        <h3>#{{ list.name }}<span class="v-contact-username"> ({{ list.subscribers_count }} Subscribers)</span></h3>

		        <p>
		            {{ list.description }}
		        </p>
		    </div>
	    </router-link>
	</li>
</template>

<script>
export default {
    props: ['list']
};
</script>
