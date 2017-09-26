<template>
    <section class="banned-user-wrapper">
        <div class="banned-user">
            <div class="left">
                <markdown :text="list.title"></markdown>
            </div>

            <div class="actions">
                <i class="pointer v-icon go-gray v-edit h-primary" @click="$emit('edit-rule', list)"
                   v-tooltip.top="{content: 'Edit'}"></i>

                <i class="pointer v-icon go-gray v-delete h-red" @click="$emit('delete-rule', list.id, list.category_id)"
                   v-tooltip.top="{content: 'Delete'}"></i>
            </div>
        </div>
    </section>
</template>

<script>
    import Markdown from '../components/Markdown.vue'

    export default {
        components: { Markdown },

        mixins: [],

        data: function () {
            return {
                showDescription: false
            }
        },

        props: ['list'],
    };
</script>
