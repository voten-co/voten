<template>
    <div class="photo-viewer-wrapper user-select">
        <div class="photo-viewer-header">
            <div class="flex-display">
                <div class="info desktop-only">
                    <h3>
                        {{ str_limit(list.title, 40) }}
                    </h3>

                    <small class="go-gray">
                        Submitted by
                        <router-link :to="'/' + '@' + list.owner.username">@{{ list.owner.username }}</router-link>
                        to
                        <router-link :to="'/c/' + list.channel_name">#{{ list.channel_name }}</router-link>
                        -
                        <router-link :to="'/c/' + list.channel_name + '/' + list.slug">
                            {{ date }}
                        </router-link>
                    </small>
                </div>

                <div class="voting-wrapper">
                    <a class="fa-stack" @click="$emit('bookmark')" v-tooltip.bottom="{content: bookmarked ? 'Unbookmark' : 'Bookmark'}">
    					<i class="v-icon h-yellow" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
    				</a>

                    <a class="fa-stack align-right" @click="$emit('upvote')" v-tooltip.bottom="{content: 'Upvote'}">
                        <i class="v-icon v-up-fat" :class="upvoted ? 'go-primary' : 'go-gray'"></i>
                    </a>

                    <div class="detail">
                        {{ points }} Points
                    </div>

                    <a class="fa-stack align-right" @click="$emit('downvote')" v-tooltip.bottom="{content: 'Downvote'}">
                        <i class="v-icon v-down-fat" :class="downvoted ? 'go-red' : 'go-gray'"></i>
                    </a>
                </div>
            </div>

            <div>
                <i class="v-icon pointer v-cancel margin-right-1" aria-hidden="true" @click="$emit('close')" v-tooltip.left="{content: 'Close (esc)'}"></i>
            </div>
        </div>

        <div class="photo-wrapper">
            <div @click.stop.prevent="" v-html="list.data.embed" class="video-player-wrapper"></div>
        </div>
    </div>
</template>

<style>
    .photo-wrapper iframe{
        width: 70em;
        max-width: 100%;
        height: 36em;
        max-height: 48%;
    }
</style>


<script>
    import Helpers from '../mixins/Helpers'

    export default {
        mixins: [Helpers],

        props: [
            'points', 'upvoted', 'downvoted', 'bookmarked', 'list'
        ],

        computed: {
            date () {
                return moment(this.list.created_at).utc(moment().format("Z")).fromNow();
            },
        },

        created () {
            window.addEventListener('keyup', this.keyup);
        },

        methods: {
            /**
             * Catches the event fired for the pressed key, and runs the neccessary methods.
             *
             * @param keyup event
             * @return void
             */
            keyup(event){
                // esc
                if (event.keyCode == 27) {
                    this.$emit('close');
                }
            },

        }
    };
</script>
