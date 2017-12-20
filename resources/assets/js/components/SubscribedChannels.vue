<template>
    <div class="container margin-top-1 col-7 user-select">
        <div class="margin-bottom-1">
            <h1>
                Subscribed Channels
                <span>({{ Store.state.subscribedChannels.length }})</span>:
            </h1>
        </div>

        <section>
            <subscribed-channel v-for="channel in channels" :list="channel" :key="channel.id"></subscribed-channel>

            <no-content v-if="nothingFound" :text="'You have not bookmarked any channels yet'"></no-content>

            <loading v-show="loading"></loading>

            <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
        </section>
    </div>
</template>

<script>
    import Loading from '../components/Loading.vue';
    import SubscribedChannel from '../components/BookmarkedChannel.vue';
    import NoMoreItems from '../components/NoMoreItems.vue';
    import NoContent from '../components/NoContent.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        mixins: [Helpers],

        components: {
            NoContent,
            SubscribedChannel,
            Loading,
            NoMoreItems
        },

        data: function () {
            return {
                NoMoreItems: false,
                loading: true,
                nothingFound: false,
                page: 0,
                channels: []
            }
        },

        created () {
            this.$eventHub.$on('scrolled-to-bottom', this.loadMore);
            this.getChannels();
        },

        watch: {
            '$route': function () {
                this.clearContent();
                this.getChannels();
            }
        },


        methods: {
            loadMore () {
                if (Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems) {
                    this.getChannels();
                }
            },

            clearContent () {
                this.nothingFound = false;
                this.users = [];
                this.loading = true;
            },

            getChannels () {
                this.page ++;
                this.loading = true;

                axios.get('/subscribed-channels', {
                    params: {
                        page: this.page
                    }
                }).then((response) => {
                    this.channels = [...this.channels, ...response.data.data];

                    if (response.data.next_page_url == null) {
                        this.NoMoreItems = true;
                    }

                    if(this.channels.length == 0) {
                        this.nothingFound = true;
                    }

                    this.loading = false;
                })
            }
        }
    };
</script>
