<template>
	<li>
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
    export default {

        props: ['notification'],

        computed: {
        	date () {
                return moment(this.notification.created_at).utc(moment().format("Z")).fromNow()
            },
        }
    }
</script>
