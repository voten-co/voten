<template>
<section class="banned-user-wrapper">
    <div class="banned-user">
        <div class="left"
        data-toggle="tooltip" data-placement="top" :title="list.submission.title">
            <router-link :to="'/c/' + list.submission.category_name + '/' + list.submission.slug">
                {{ str_limit(list.submission.title, 20) }}
            </router-link>
        </div>

        <div class="detail"
        data-toggle="tooltip" data-placement="top" :title="list.subject">
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
                        data-toggle="tooltip" data-placement="top" title="Description"></i>

            <i class="pointer v-icon go-gray v-delete h-red" @click="$emit('disapprove-submission', list.submission.id)"
                        :class="list.submission.deleted_at ? 'display-hidden' : ''"
                        data-toggle="tooltip" data-placement="top" title="Delete Submission"></i>

            <i class="pointer v-icon go-gray v-approve h-green" @click="$emit('approve-submission', list.submission.id)"
                        :class="list.submission.approved_at ? 'display-hidden' : ''"
                        data-toggle="tooltip" data-placement="top" title="Approve Submission"></i>
        </div>
    </div>

    <div class="banned-user-description" v-if="showDescription">
        <markdown :text="list.description"></markdown>
    </div>
</section>
</template>


<script>
// import Submission from '../components/Submission.vue'
import Markdown from '../components/Markdown.vue'
import Helpers from '../mixins/Helpers'

export default {
    components: {
        // Submission,
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
            this.$root.loadSemanticTooltip()
        })
    },
};
</script>
