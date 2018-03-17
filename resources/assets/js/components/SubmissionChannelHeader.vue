<template>
	<div class="channel-header-small" v-bind:style="{ background: coverBackground }">
		<button class="no-padding lighter-on-hover" type="button" @click="goBack">
			<i class="v-left"></i>
		</button>

		<router-link :to="'/c/' + name">
			<img :src="avatar" :alt="name">

			<h2>
				{{ '#' + name }}
			</h2>
		</router-link>

		<subscribe subscribed-class="unsubscribe" unsubscribed-class="subscribe"></subscribe>
	</div>
</template>


<script>
import Subscribe from '../components/SubscribeButton.vue';

export default {
    data() {
        return {
            Store
        };
    },
    
    components: { Subscribe },

    computed: {
        name() {
            return Store.page.channel.temp.name;
        },

        avatar() {
            return Store.page.channel.temp.avatar;
        },

        coverBackground() {
            if (Store.page.channel.temp.cover_color == 'Red') {
                return '#9a4e4e';
            } else if (Store.page.channel.temp.cover_color == 'Blue') {
                return '#5487d4';
            } else if (Store.page.channel.temp.cover_color == 'Dark Blue') {
                return '#2f3b49';
            } else if (Store.page.channel.temp.cover_color == 'Dark Green') {
                return '#507e75';
            } else if (Store.page.channel.temp.cover_color == 'Bright Green') {
                return 'rgb(117, 148, 127)';
            } else if (Store.page.channel.temp.cover_color == 'Purple') {
                return '#4d4261';
            } else if (Store.page.channel.temp.cover_color == 'Orange') {
                return '#ffaf40';
            } else if (Store.page.channel.temp.cover_color == 'Pink') {
                return '#ec7daa';
            } else {
                // userStore.cover_color == 'Black'
                return '#424242';
            }
        }
    },

    methods: {
        goBack() {
            history.go(-1); 
        }
    }
};
</script>
