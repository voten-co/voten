<template>
    <section class="banned-user-wrapper">
        <div class="banned-user">
            <div class="left">
                <markdown :text="list.title"></markdown>
            </div>

            <div class="actions">
                <i class="pointer v-icon go-gray v-edit h-primary" @click="$emit('edit-rule', list)"
                    data-toggle="tooltip" data-placement="top" title="Edit"></i>

                <i class="pointer v-icon go-gray v-delete h-red" @click="$emit('delete-rule', list.id, list.category_id)"
                    data-toggle="tooltip" data-placement="top" title="Delete"></i>
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

        mounted: function() {
            this.$nextTick(function() {
                this.$root.loadSemanticTooltip()
            })
        },
    };
</script>

<style>
    .banned-user {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .banned-user-wrapper {
        border-bottom: 1px dashed #dcdcdc;
        padding-bottom: .3em;
        margin-bottom: .3em;
    }

    #moderator-panel-rules .banned-user-wrapper .actions {
        flex-basis: 130px;
        display: flex;
        justify-content: flex-end;
    }
</style>
