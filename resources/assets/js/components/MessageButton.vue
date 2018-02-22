<template>
    <el-button round type="success" size="mini" @click="sendMessage">
        Message
    </el-button>
</template>


<script>
export default {
    props: ['id'],

    data() {
        return {
            subscribed: false,
            contact: []
        };
    },

    created() {
        this.getUser();
    },

    methods: {
        /**
         * Fetches the required user's info for starting a conversation with him/her
         *
         * @return void
         */
        getUser() {
            axios
                .get('/users', {
                    params: {
                        id: this.id
                    }
                })
                .then((response) => {
                    this.contact = response.data.data;
                });
        },

        /**
         * Fires the send message event and sends the contact
         *
         * @return void
         */
        sendMessage() {
            this.$eventHub.$emit('start-conversation', this.contact);
        }
    }
};
</script>
