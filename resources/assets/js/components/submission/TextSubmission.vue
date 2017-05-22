<template>
	<div class="link-list-info">
		<router-link :to="'/c/' + submission.category_name + '/' + submission.slug" class="flex-space v-ultra-bold">
			{{ submission.title }}
		</router-link>

		<div v-if="full">
			<hr v-if="submission.data.text">

			<markdown :text="submission.data.text" v-if="submission.data.text"></markdown>
		</div>
	</div>
</template>

<script>
    import Markdown from '../../components/Markdown.vue';

    export default {
        // props: ['nsfw', 'submission'],

        props: {
        	nsfw: {}, submission: {},
            full: {
                type: Boolean,
                default: false,
            },
        },

        components: {
            Markdown,
        },

    }
</script>
