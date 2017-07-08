<template>
    <button :class="activeClass" @click="subscribe" v-html="content" v-if="loaded"></button>
</template>

<script>
export default {
    data: function () {
        return {
            subscribed: false,
            loaded: false,
            Store
        }
    },

    created: function () {
        axios.get('/is-subscribed', {
        	params: {
        		category_id: Store.category.id
        	}
        }).then((response) => {
            this.subscribed = response.data;

            this.loaded = true;
        });
    },

    computed: {
    	activeClass() {
    		if (this.subscribed) {
    			return 'v-button v-button--red';
    		}

    		if (!this.subscribed) {
    			return 'v-button v-button--green';
    		}
    	},

    	content () {
    		if (!this.subscribed) {
    			return 'Subscribe';
    		}

    		return 'Unsubscribe';
    	}
    },

    methods: {
        /**
         * Subscribes to the category.
         *
         * @return void
         */
        subscribe() {
            this.subscribed = !this.subscribed;

            if (this.subscribed) {
            	Store.subscribedCategories.push(Store.category);

            	Store.category.stats.subscribersCount ++;
            } else {
            	Store.category.stats.subscribersCount --;

            	let removeItem = Store.category.id;
				Store.subscribedCategories = Store.subscribedCategories.filter(function (category) {
				  	return category.id != removeItem;
				});
            }

            axios.post('/subscribe', {
            	category_id: Store.category.id
            });
        }
    }
}
</script>
