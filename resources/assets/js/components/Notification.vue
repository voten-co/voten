<template>
	<li :class="{ 'has-unread-messages' : notification.broadcasted }" @mouseover="seen">
        <router-link :to="notification.data.url">
            <div class="v-contact-avatar">
                <img v-bind:src="notification.data.avatar" v-bind:alt="notification.data.name" />
            </div>

            <div class="v-contact">
                <p>
                    {{ notification.data.body }}
                    - <small>{{ date }}</small>
                </p>
            </div>
        </router-link>
	</li>
</template>

<script>
    import Helpers from '../mixins/Helpers';

    export default {
        props: ['notification'],

        mixins: [Helpers],

        computed: {
        	date () {
                if (this.isItToday(this.notification.created_at)) {
                    return this.parseDateForToday(this.notification.created_at)
                }

                return this.parseDate(this.notification.created_at);
            },
        },

        methods: {
        	/**
        	 * seen the notification
        	 *
        	 * @return void
        	 */
        	seen() {
        	    this.notification.broadcasted = false;
        	},
        }
    }
</script>
