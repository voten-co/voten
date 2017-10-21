<template>
	<transition name="fade">
		<div class="no-content-wrapper user-select" v-show="visible" v-once>
			<sad-icon width="160" height="160"></sad-icon>
			
	        <p v-text="text"></p>
		</div>
	</transition>
</template>


<script>
	import SadIcon from './Icons/SadIcon.vue';

    export default {
		props: ['text'],

		components: {
			SadIcon
		}, 

		data () {
			return {
				visible: true
			}
		}
    }
</script>
