<template>
    <section>
        <h3 class="dotted-title">
			<span>
				Block Domains
			</span>
        </h3>

        <p>
            In case there are domain addresses that you think are not appropriate for your channel you can block them here.
        </p>

        <el-form label-position="top" label-width="10px">
            <el-form-item label="Domain address">
                <el-input
                        placeholder="http://example.com"
                        name="domain"
                        v-model="domain">
                </el-input>

                <el-alert v-for="e in errors.domain" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <el-form-item label="Reason(optional)">
                <el-input
                        type="textarea"
                        placeholder="What is wrong with this website? (markdown syntax is supported)"
                        name="description"
                        :rows="4"
                        v-model="description">
                </el-input>
            </el-form-item>

            <el-form-item>
                <el-button round size="medium" type="danger" v-if="domain" @click="blockDomain" :loading="sending">Block
                </el-button>
            </el-form-item>
        </el-form>


        <h3 class="dotted-title" v-if="blockedDomains.length">
			<span>
				All Blocked Domains
			</span>
        </h3>

        <blocked-domain v-for="blocked in blockedDomains" :list="blocked" :key="blocked.id"
                        @unblock="unblock"></blocked-domain>
    </section>
</template>

<script>
import BlockedDomain from '../components/BlockedDomain.vue';

export default {
    components: { BlockedDomain },

    data: function() {
        return {
            loading: false,
            sending: false,
            errors: [],
            domain: null,
            description: '',
            blockedDomains: []
        };
    },

    created() {
        this.getBlockedDomains();
    },

    methods: {
        blockDomain() {
            if (!this.domain) return;

            this.sending = true;
            this.blockErrors = [];

            axios
                .post('/channels/domains/block', {
                    domain: this.domain,
                    description: this.description,
                    channel_id: Store.page.channel.temp.id
                })
                .then((response) => {
                    this.domain = '';
                    this.description = '';
                    this.errors = [];
                    this.blockedDomains.unshift(response.data.data);
                    this.sending = false;
                })
                .catch((error) => {
                    this.errors = error.response.data.errors;
                    this.sending = false;
                });
        },

        /**
         * Fetches the list of already blocked domains on this channel.
         *
         * @return void
         */
        getBlockedDomains() {
            this.loading = true;

            axios
                .get('/channels/domains/block', {
                    params: {
                        channel_id: Store.page.channel.temp.id
                    }
                })
                .then((response) => {
                    this.blockedDomains = response.data.data;
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        },

        /**
         * Unblock the domain (destroy the blockedDomain record).
         *
         * @return void
         */
        unblock(domain) {
            axios
                .delete('/channels/domains/block', {
                    params: {
                        domain: domain,
                        channel_id: Store.page.channel.temp.id
                    }
                })
                .then(() => {
                    this.blockedDomains = this.blockedDomains.filter(function(
                        item
                    ) {
                        return item.domain != domain;
                    });
                });
        }
    },

    beforeRouteEnter(to, from, next) {
        if (Store.page.channel.temp.name == to.params.name) {
            // loaded
            if (
                Store.state.moderatingAt.indexOf(Store.page.channel.temp.id) !=
                -1
            ) {
                next();
            }
        } else {
            // not loaded but let's continue (the server-side is still protecting us!)
            next();
        }
    }
};
</script>
