<template>
    <section class="banned-user-wrapper">
        <div class="banned-user">
            <div class="left" v-tooltip.top="{content: list.submission.title}">
                <router-link :to="'/c/' + list.submission.category_name + '/' + list.submission.slug">
                    {{ str_limit(list.submission.title, 20) }}
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

                <i class="pointer v-icon go-gray v-delete h-red" @click="$emit('disapprove-submission', list.submission.id)"
                            v-tooltip.top="{content: 'Delete Submission'}"
                            :class="list.submission.deleted_at ? 'display-hidden' : ''"></i>

                <i class="pointer v-icon go-gray v-approve h-green" @click="$emit('approve-submission', list.submission.id)"
                            v-tooltip.top="{content: 'Approve Submission'}"
                            :class="list.submission.approved_at ? 'display-hidden' : ''"></i>
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
            return moment(this.list.created_at).utc(moment().format("Z")).fromNow();
        },
    },
};
</script>
