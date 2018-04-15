<template>
    <section class="banned-user-wrapper">
        <div class="banned-user">
            <el-popover
                    placement="top-start"
                    trigger="hover"
                    transition="false"
                    :open-delay="100"
            >
                <div class="left" slot="reference">
                    <router-link :to="'/c/' + list.submission.channel_name + '/' + list.submission.slug">
                        {{ str_limit(list.submission.title, 20) }}
                    </router-link>
                </div>

                <p>
                    {{ list.submission.title }}
                </p>

                <div class="flex-right">
                    <el-button round @click="$router.push('/c/' + list.submission.channel_name + '/' + list.submission.slug)" size="mini">
                        Open
                    </el-button>
                </div>
            </el-popover>

            <el-tooltip :content="list.subject" placement="top" transition="false" :open-delay="500">
                <div class="detail">
                    {{ str_limit(list.subject, 20) }}
                </div>
            </el-tooltip>

            <el-tooltip :content="longDate" placement="top" transition="false" :open-delay="500">
                <small>
                    <router-link :to="'/@' + list.reporter.username">
                        {{ date }}
                    </router-link>
                </small>
            </el-tooltip>

            <div class="actions">
                <el-tooltip content="Description" placement="top" transition="false" :open-delay="500">
                    <i class="pointer v-icon go-gray v-attention-alt h-yellow"
                       :class="list.description ? '' : 'display-hidden'" @click="showDescription = !showDescription"
                    ></i>
                </el-tooltip>

                <el-tooltip content="Delete Submission" placement="top" transition="false" :open-delay="500">
                    <i class="pointer v-icon go-gray v-delete h-red"
                       @click="$emit('disapprove-submission', list.submission.id)"
                       :class="list.submission.solved_at ? 'display-hidden' : ''"></i>
                </el-tooltip>

                <el-tooltip content="Approve Submission" placement="top" transition="false" :open-delay="500">
                    <i class="pointer v-icon go-gray v-approve h-green"
                       @click="$emit('approve-submission', list.submission.id)"
                       :class="list.submission.approved_at ? 'display-hidden' : ''"></i>
                </el-tooltip>
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
    components: { Markdown },

    mixins: [Helpers],

    data() {
        return {
            showDescription: false
        };
    },

    props: ['list'],

    computed: {
        date() {
            return this.parsDiffForHumans(this.list.created_at); 
        },

        /**
         * Calculates the long date to display for hover over date.
         *
         * @return String
         */
        longDate() {
            return this.parseFullDate(this.list.created_at);
        }
    }
};
</script>
