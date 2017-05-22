<template>
	<section class="container margin-top-1 col-7">
		<no-content  :text="'Sorry, that page doesn’t exist!'"></no-content>
	</section>
</template>


<script>
	import NoContent from '../components/NoContent.vue'

	export default {
	    components: {
	    	NoContent
	    }
	};
</script>
