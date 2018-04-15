<template>
	<section class="banned-user-wrapper">
		<div class="banned-user">
			<el-popover placement="top-start"
			            trigger="hover"
			            transition="false"
			            :open-delay="100">
				<div class="left"
				     slot="reference">
                      {{ str_limit(list.comment.content.text, 30) }}
				</div>

				<p>
					{{ list.comment.content.text }}
				</p>

				<div class="flex-right">
					<el-button round
					           @click="openOrigin"
					           size="mini">
						Open
					</el-button>
				</div>
			</el-popover>

			<el-tooltip :content="list.subject"
			            placement="top"
			            transition="false"
			            :open-delay="500">
				<div class="detail">
					{{ str_limit(list.subject, 20) }}
				</div>
			</el-tooltip>

			<small>
				<router-link :to="'/@' + list.reporter.username">
					{{ date }}
				</router-link>
			</small>

			<div class="actions">
				<el-tooltip content="Description"
				            placement="top"
				            transition="false"
				            :open-delay="500">
					<i class="pointer v-icon go-gray v-attention-alt h-yellow"
					   :class="list.description ? '' : 'display-hidden'"
					   @click="showDescription = !showDescription"></i>
				</el-tooltip>

				<el-tooltip content="Delete Comment"
				            placement="top"
				            transition="false"
				            :open-delay="500">
					<i class="pointer v-icon go-gray v-delete h-red"
					   @click="$emit('disapprove-comment', list.comment.id)"
					   :class="list.solved_at ? 'display-hidden' : ''"></i>
				</el-tooltip>

				<el-tooltip content="Approve Comment"
				            placement="top"
				            transition="false"
				            :open-delay="500">
					<i class="pointer v-icon go-gray v-approve h-green"
					   @click="$emit('approve-comment', list.comment.id)"
					   :class="list.comment.approved_at ? 'display-hidden' : ''"></i>
				</el-tooltip>
			</div>
		</div>

		<div class="banned-user-description"
		     v-if="showDescription">
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
        }
    }, 

    methods: {
        openOrigin() {
            app.$Progress.start();

            axios
                .get(`/submissions/${this.list.comment.submission_id}`)
                .then(response => {
                    this.$router.push(
                        this.submissionUrl(response.data.data)
                    );

                    app.$Progress.finish();
                })
                .catch(error => {
                    app.$Progress.fail();
                });
        },
    }
};
</script>
