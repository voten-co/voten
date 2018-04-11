<template>
    <button :class="activeClass" @click="subscribe" v-text="content"></button>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    data() {
        return {
            Store
        };
    },

    props: ['subscribed-class', 'unsubscribed-class'],

    computed: {
        activeClass() {
            return this.subscribed
                ? this.subscribedClass
                : this.unsubscribedClass;
        },

        content() {
            return this.subscribed ? 'Unsubscribe' : 'Subscribe';
        },

        subscribed: {
            get() {
                return Store.state.subscribedAt.indexOf(
                    Store.page.channel.temp.id
                ) !== -1
                    ? true
                    : false;
            },

            set() {
                if (
                    Store.state.subscribedAt.indexOf(
                        Store.page.channel.temp.id
                    ) !== -1
                ) {
                    Store.page.channel.temp.subscribers_count--;

                    let removeItem = Store.page.channel.temp.id;
                    Store.state.subscribedChannels = Store.state.subscribedChannels.filter(
                        (channel) => channel.id != removeItem
                    );

                    let index = Store.state.subscribedAt.indexOf(
                        Store.page.channel.temp.id
                    );
                    Store.state.subscribedAt.splice(index, 1);

                    return;
                }

                Store.state.subscribedChannels.push(Store.page.channel.temp);
                Store.state.subscribedAt.push(Store.page.channel.temp.id);
                Store.page.channel.temp.subscribers_count++;
            }
        }
    },

    methods: {
        subscribe: _.debounce(
            function() {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                this.subscribed = !this.subscribed;

                axios
                    .post(`/channels/${Store.page.channel.temp.id}/subscribe`)
                    .catch(() => {
                        this.subscribed = !this.subscribed;
                    });
            },
            200,
            { leading: true, trailing: false }
        )
    }
};
</script>
