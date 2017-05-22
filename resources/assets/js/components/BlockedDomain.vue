<template>
    <section class="banned-user-wrapper">
        <div class="banned-user">
            <div class="left">
                <a :href="'http://' + list.domain" target="_target">
                    {{ list.domain }}
                </a>
            </div>

            <div class="actions">
                <i class="pointer v-icon go-gray v-attention-alt h-yellow" :class="list.description ? '' : 'display-hidden'"
                    @click="showDescription = !showDescription"
                    data-toggle="tooltip" data-placement="top" title="Reason for being blocked"></i>

                <i class="pointer v-icon go-gray v-delete h-red" @click="$emit('unblock', list.domain)"
                    data-toggle="tooltip" data-placement="top" title="Unblock"></i>
            </div>
        </div>

        <div class="banned-user-description" v-if="showDescription">
            <markdown :text="list.description"></markdown>
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


        computed: {
            //
        },

        created () {
            //
        },

        mounted: function() {
            this.$nextTick(function() {
                this.$root.loadSemanticTooltip()
            })
        },

        methods: {
            //
        }
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

    .banned-user a {
        color: #333;
    }

    .banned-user img {
        width: 25px;
        height: auto;
        border-radius: 50%;
        margin-right: .2em;
    }

    .banned-user-description {
        background: #fdfdfd;
        color: #333;
        border: 1px solid #e7e7e7;
        padding: .5em;
        border-radius: 2px;
        line-height: 2;
        overflow: auto;
        margin: .3em;
    }
</style>
