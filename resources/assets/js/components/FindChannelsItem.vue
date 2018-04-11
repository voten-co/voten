<template>
	<section class="bookmarked-item user-select find-channels">
		<div class="avatar">
			<router-link :to="'/c/' + list.name">
				<img :src="list.avatar" :alt="list.name">
			</router-link>
		</div>

		<div class="flex1">
			<h2>
				<router-link :to="'/c/' + list.name">
					{{ list.name }}
				</router-link>
			</h2>

			<p>
				{{ list.description }}
			</p>
		</div>

		<div class="action">
			<button class="v-button v-button-outline--green" @click="subscribe">
				Subscribe
			</button>
		</div>
	</section>
</template>

<script>
export default {
    data: function() {
        return {
            Store
        };
    },

    props: ['list'],

    methods: {
        subscribe() {
            this.$emit('subscribed');

            Store.state.subscribedChannels.push(this.list);

            axios.post(`/channels/${this.list.id}/subscribe`);
        }
    }
};
</script>
