 <template>
    <button class="v-button v-button--green" @click="sendMessage">
        Message
    </button>
</template>


<script>
    export default {
        props: ['id'],

        data: function () {
            return {
                subscribed: false,
                contact: [],
            }
        },


        created: function () {
        	this.getUser()
        },

        methods: {
        	/**
        	 * Fetches the required user's info for starting a conversation with him/her
        	 *
        	 * @return void
        	 */
        	getUser: function () {
        		axios.post('/contact-info', {
	                user_id: this.id,
	            } ).then((response) => {
	            	this.contact = response.data;
	            });
        	},

        	/**
        	 * Fires the send message event and sends the contact
        	 *
        	 * @return void
        	 */
        	sendMessage: function () {
        		this.$eventHub.$emit('start-conversation', this.contact)
        	}
        }
    }
</script>
