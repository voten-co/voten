<template>
    <span class="typing" :class="{ 'display-hidden': !typers.length }">
        <i class="text" v-if="typers.length === 1">
            <router-link :to="'/@' + typers[0]">@{{ typers[0] }}</router-link> is typing
        </i>

        <i class="text" v-if="typers.length === 2">
            <router-link :to="'/@' + typers[0]">@{{ typers[0] }}</router-link> and <router-link :to="'/@' + typers[1]">@{{ typers[1] }}</router-link> are typing
        </i>

        <i class="text" v-if="typers.length > 2">
            <router-link :to="'/@' + typers[0]">@{{ typers[0] }}</router-link> and {{ typers.length - 1 }} others are typing
        </i>

        <span class="dots">
            <span>.</span><span>.</span><span>.</span>
        </span>
    </span>
</template>


<script>
import Helpers from '../mixins/Helpers';

export default {
    components: {},

    mixins: [Helpers],

    data() {
        return {
            EchoChannelAddress: 'submission.' + this.$route.params.slug,
            typers: []
        };
    },

    created() {
        this.listen();
        this.$eventHub.$on('finished-typing', this.finishedTyping);
    },

    beforeDestroy() {
        this.$eventHub.$off('finished-typing', this.finishedTyping);
    },

    methods: {
        listen() {
            // we can't do presence channel or/and listen for private channels, if the user is a guest
            if (this.isGuest) return;

            Echo.private(this.EchoChannelAddress)
                .listenForWhisper('typing', (user) => {
                    this.startedTyping(user.username);
                })
                .listenForWhisper('finished-typing', (user) => {
                    this.finishedTyping(user.username);
                });
        },

        startedTyping(username) {
            let index = this.typers.indexOf(username);

            if (index === -1) {
                this.typers.push(username);
            }
        },

        finishedTyping(username) {
            let index = this.typers.indexOf(username);

            if (index !== -1) {
                this.typers.splice(index, 1);
            }
        }
    }
};
</script>


<style>
@keyframes blink {
    0% {
        opacity: 0.2;
    }

    20% {
        opacity: 1;
    }

    100% {
        opacity: 0.2;
    }
}

.typing {
    margin-top: -18px;
    color: #717577;
}

.typing a {
    color: #717577;
    font-weight: bold;
}

.typing a:hover,
.typing a:focus {
    text-decoration: underline;
}

.dots {
    font-size: 30px;
}

.typing span {
    animation-name: blink;
    animation-duration: 1.4s;
    animation-iteration-count: infinite;
    animation-fill-mode: both;
}

.typing span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing span:nth-child(3) {
    animation-delay: 0.4s;
}

.typing .text {
    font-size: 12px;
}
</style>
