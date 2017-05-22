<template>
    <section class="banned-user-wrapper">
        <div class="banned-user">
            <div class="left">
                <router-link :to="'/@' + list.user.username">
                    <img :src="list.user.avatar" :alt="list.user.username">
                    {{ list.user.username }}
                </router-link>
            </div>

            <div class="actions">
                <i class="pointer v-icon go-gray v-calendar-1 h-green"
                    data-toggle="tooltip" data-placement="top" :title="'Unban ' + date"></i>

                <i class="pointer v-icon go-gray v-delete h-red" @click="$emit('unban', list.user_id)"
                    data-toggle="tooltip" data-placement="top" title="Unban"></i>

                <i class="pointer v-icon go-gray v-attention-alt h-yellow" :class="list.description ? '' : 'display-hidden'"
                    @click="showDescription = !showDescription"
                    data-toggle="tooltip" data-placement="top" title="Reason for being banned"></i>
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

        data: function () {
            return {
                showDescription: false
            }
        },

        props: ['list'],

        computed: {
            date () {
                return moment(this.list.unban_at).utc(moment().format("Z")).fromNow()
            },
        },

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
