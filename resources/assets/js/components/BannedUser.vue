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
                <i class="pointer v-icon go-gray v-attention-alt h-yellow" :class="list.description ? '' : 'display-hidden'" v-tooltip.top="{content: 'Reason'}"
                    @click="showDescription = !showDescription"></i>

                <i class="pointer v-icon go-gray v-delete h-red" @click="$emit('unban', list.user_id)" v-tooltip.top="{content: 'Unban'}"></i>

                <i class="pointer v-icon go-gray v-calendar-1 h-green" v-tooltip.top="{content: 'Unban ' + date}"></i>
            </div>
        </div>

        <div class="banned-user-description" v-if="showDescription">
            <markdown :text="list.description"></markdown>
        </div>
    </section>
</template>

<script>
    import Markdown from '../components/Markdown.vue'; 
    import Helpers from '../mixins/Helpers'; 

    export default {
        mixins: [Helpers], 

        components: { Markdown },

        data() {
            return {
                showDescription: false
            }
        },

        props: ['list'],

        computed: {
            date() {
                return this.parsDiffForHumans(this.list.unban_at);
            },
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
