<template>
	<li class="help-item">
		<router-link :to="'/help/' + list.id">
			<h3 class="pointer v-bold user-select">{{ list.title }}</h3>
		</router-link>
	</li>
</template>

<style>
	.help-item a {
		color: #333;
	}
	.help-item a:hover {
		text-decoration: underline;
	}
</style>

<script>
    export default {
        props: ['list'],

        data: function () {
            return {
                show: false,
            };
        },

        methods: {
        	toggle: function () {
        		this.show = ! this.show;
        	}
        }
    }
</script>