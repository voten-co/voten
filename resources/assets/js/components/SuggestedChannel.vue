<template>
	<transition name="el-fade-in-linear">
		<section class="user-select channel-suggestion-wrapper" v-if="visible">
			<div class="flex-space">
				<h3 class="channel-suggestion-wrapper-title">Recommended:</h3>

				<el-button round type="success" plain size="mini" @click="subscribe">
					Subscribe
				</el-button>
			</div>

			<div class="channel-suggestion">
				<div class="avatar">
					<router-link :to="'/c/' + channel.name">
						<img :src="channel.avatar" :alt="channel.name">
					</router-link>
				</div>

				<div class="flex1">
					<h2 class="word-break">
						<router-link :to="'/c/' + channel.name">
							<i class="v-icon v-channel" aria-hidden="true"></i>{{ channel.name }}
						</router-link>
					</h2>

					<p>
						{{ channel.description }}
					</p>
				</div>
			</div>
		</section>
	</transition>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    data() {
        return {
            visible: false,
            channel: []
        };
    },

    created() {
        this.get();
    },

    methods: {
        get() {
            axios.get('/suggested-channel').then((response) => {
                // We got nothing to suggest.
                if (_.isUndefined(response.data.data)) return;

                this.channel = response.data.data;
                this.visible = true;
            });
        },

        subscribe() {
            if (this.isGuest) {
                this.mustBeLogin();
                return;
            }

            Store.state.subscribedChannels.push(this.channel);
            Store.state.subscribedAt.push(this.channel.id);

            axios.post(`/channels/${this.channel.id}/subscribe`);

            this.visible = false;
        }
    }
};
</script>
