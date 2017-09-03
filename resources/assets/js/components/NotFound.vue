<template>
	<section class="container margin-top-1 col-7">
		<no-content  :text="message"></no-content>
	</section>
</template>


<script>
	import NoContent from '../components/NoContent.vue';

	export default {
	    data() {
	        return {
	            message: "I hate to be the one breaking it to you, but you've been given a wrong address!"
			}
		},

	    components: {
	    	NoContent
	    }
	};
</script>
