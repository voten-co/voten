<template>
	<section class="container margin-top-1 col-7" id="not-found">
		<div class="no-content-wrapper user-select">
			<map-icon width="260" height="260"></map-icon>
			<p v-text="message"></p>
		</div>
	</section>
</template>


<script>
import MapIcon from '../components/Icons/MapIcon.vue';

export default {
    components: {
        MapIcon
    },

    data() {
        return {
            message:
                "I hate to be the one breaking it to you, but you've been given a wrong address!"
        };
    }
};
</script>

<style>
#not-found {
    margin-top: 10%;
}
</style>
