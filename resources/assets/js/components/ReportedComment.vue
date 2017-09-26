<template>
    <section class="banned-user-wrapper">
        <div class="banned-user">
            <div class="left s-popup" v-bind:data-content="list.comment.body">
                <router-link :to="list.comment.submission != undefined ? '/c/' + list.comment.submission.category_name + '/' + list.comment.submission.slug : '/deleted-submission'">
                    {{ str_limit(list.comment.body, 20) }}
                </router-link>
            </div>

            <div class="detail" v-tooltip.top="{content: list.subject}">
                {{ str_limit(list.subject, 20) }}
            </div>

            <small>
                <router-link :to="'/@' + list.reporter.username">
                    {{ date }}
                </router-link>
            </small>

            <div class="actions">
                <i class="pointer v-icon go-gray v-attention-alt h-yellow"
                            :class="list.description ? '' : 'display-hidden'" @click="showDescription = !showDescription"
                            v-tooltip.top="{content: 'Description'}"></i>

                <i class="pointer v-icon go-gray v-delete h-red" @click="$emit('disapprove-comment', list.comment.id)"
                            v-tooltip.top="{content: 'Delete Comment'}"
                            :class="list.comment.deleted_at ? 'display-hidden' : ''"></i>

                <i class="pointer v-icon go-gray v-approve h-green" @click="$emit('approve-comment', list.comment.id)"
                            v-tooltip.top="{content: 'Approve Comment'}"
                            :class="list.comment.approved_at ? 'display-hidden' : ''"></i>
            </div>
        </div>

        <div class="banned-user-description" v-if="showDescription">
            <markdown :text="list.description"></markdown>
        </div>
    </section>
</template>


<script>
import Markdown from '../components/Markdown.vue'
import Helpers from '../mixins/Helpers'

export default {
    components: {
        Markdown
    },

    mixins: [Helpers],

    data: function() {
        return {
            showDescription: false
        }
    },

    props: ['list'],

    computed: {
        date () {
            return moment(this.list.created_at).utc(moment().format("Z")).fromNow()
        },
    },

    mounted: function() {
        this.$nextTick(function() {
            this.$root.loadSemanticPopup()
        })
    },
};
</script>
