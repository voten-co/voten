<template>
	<transition name="el-fade-in-linear">
		<div class="no-content-wrapper user-select" v-show="visible">
			<submission-icon width="160" height="160"></submission-icon>
			
	        <p v-text="text"></p>
		</div>
	</transition>
</template>


<script>
	import SubmissionIcon from './Icons/SubmissionIcon.vue';

    export default {
		props: ['text'],

		components: {
			SubmissionIcon
		}, 

		data () {
			return {
				visible: true
			}
		}
    }
</script>
