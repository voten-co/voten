<template>
	<li class="go-black">
	    <router-link :to="'/c/' + list.name">
	    	<div class="category-search-item-avatar">
		        <img v-bind:src="list.avatar" v-bind:alt="list.name" />
		    </div>

		    <div class="v-contact">
		        <h3>#{{ list.name }}<span class="v-contact-username"> ({{ list.subscribers }} Subscribers)</span></h3>

		        <p>
		            {{ list.description }}
		        </p>
		    </div>
	    </router-link>
	</li>
</template>

<script>
	export default {
		props: ['list'],
	}
</script>
