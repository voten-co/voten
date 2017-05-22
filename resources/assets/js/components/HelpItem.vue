<template>
	<li>
		<h3 @click="toggle" class="pointer go-primary v-bold user-select">{{ title }}</h3>
		<markdown :text="text" v-if="show"></markdown>
	</li>
</template>

<script>
    import Markdown from '../components/Markdown.vue';

    export default {

        props: ['text', 'title'],

        data: function () {
            return {
                show: false,
            };
        },

        components: {
            Markdown: Markdown,
        },

        methods: {
        	toggle: function () {
        		this.show = ! this.show;
        	}
        }

    }
</script>