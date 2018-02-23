<template>
	<section class="container margin-top-1 col-7 flex1" id="not-found">
		<div class="no-content-wrapper user-select">
			<i class="v-icon v-sad oops-icon" aria-hidden="true"></i>
			<h1>{{ text }}</h1>
		</div>
	</section>
</template>

<script>
export default {
    props: ['text']
};
</script>

<style>
#not-found {
    margin-top: 10%;
}
</style>
