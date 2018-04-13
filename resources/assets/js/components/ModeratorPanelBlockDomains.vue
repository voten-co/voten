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

		<el-form label-position="top"
		         label-width="10px">
			<el-form-item label="Domain address">
				<el-input placeholder="http://example.com"
				          name="domain"
				          v-model="domain">
				</el-input>

				<el-alert v-for="e in errors.domain"
				          :title="e"
				          type="error"
				          :key="e"></el-alert>
			</el-form-item>

			<el-form-item label="Reason(optional)">
				<el-input type="textarea"
				          placeholder="What is wrong with this website? (markdown syntax is supported)"
				          name="description"
				          :rows="4"
				          v-model="description">
				</el-input>
			</el-form-item>

			<el-form-item>
				<el-button round
				           size="medium"
				           type="danger"
				           v-if="domain"
				           @click="store"
				           :loading="sending">
					Block
				</el-button>
			</el-form-item>
		</el-form>

		<h3 class="dotted-title"
		    v-if="blockedDomains.length">
			<span>
				All Blocked Domains
			</span>
		</h3>

		<blocked-domain v-for="blocked in blockedDomains"
		                :list="blocked"
		                :key="blocked.id"
		                @unblock="destroy"></blocked-domain>
	</section>
</template>

<script>
import BlockedDomain from '../components/BlockedDomain.vue';

export default {
    components: { BlockedDomain },

    data() {
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
        this.get();
    },

    methods: {
        store() {
            if (!this.domain) return;

            this.sending = true;
            this.blockErrors = [];

            axios
                .post(`/channels/${Store.page.channel.temp.id}/blocked-domains`, {
                    domain: this.domain,
                    description: this.description
                })
                .then(response => {
                    this.domain = '';
                    this.description = '';
                    this.errors = [];
                    this.blockedDomains.unshift(response.data.data);
                    this.sending = false;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                    this.sending = false;
                });
        },

        /**
         * Fetches the list of already blocked domains on this channel.
         *
         * @return void
         */
        get() {
            app.$Progress.finish();
            app.$Progress.start();
            this.loading = true;

            axios
                .get(`/channels/${Store.page.channel.temp.id}/blocked-domains`)
                .then(response => {
                    this.blockedDomains = response.data.data;
                    this.loading = false;
                    app.$Progress.finish();
                })
                .catch(() => {
                    this.loading = false;
                    app.$Progress.fail();
                });
        },

        /**
         * Unblock the domain (destroy the blockedDomain record).
         *
         * @return void
         */
        destroy(blocked_domain) {
            axios
                .delete(`/channels/${Store.page.channel.temp.id}/blocked-domains/${blocked_domain}`)
                .then(response => {
                    this.blockedDomains = this.blockedDomains.filter(item => item.domain != blocked_domain);
                });
        }
    }
};
</script>
