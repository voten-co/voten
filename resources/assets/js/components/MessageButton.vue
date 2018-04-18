<template>
	<el-button round
	           type="success"
               :loading="loading"
	           size="mini"
	           @click="sendMessage">
		Message
	</el-button>
</template>


<script>
export default {
    props: ['id'],

    data() {
        return {
            subscribed: false,
            contact: [],
            loading: false
        };
    },

    methods: {
        /**
         * Fires the send message event and sends the contact
         *
         * @return void
         */
        sendMessage() {
            this.loading = true;

            axios
                .get(`/users/${this.id}`)
                .then(response => {
                    this.contact = response.data.data;
                    this.loading = false;
                    this.$eventHub.$emit('start-conversation', this.contact);                    
                })
                .catch(error => {
                    this.loading = false;
                });
        }
    }
};
</script>
