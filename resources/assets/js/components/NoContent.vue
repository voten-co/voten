<template>
	<transition name="fade">
		<div class="no-content-wrapper user-select" v-show="visible">
	    	<i class="v-icon v-sad oops-icon" aria-hidden="true"></i>
	        <h1>{{ text }}</h1>
		</div>
	</transition>
</template>

<script>
    export default {
		props: ['text'],

		data () {
			return {
				visible: true
			}
		}
    }
</script>
