<template>
    <button :class="activeClass" @click="subscribe" v-html="content"></button>
</template>

<script>
export default {
    data: function () {
        return {
            subscribed: false,
            Store
        }
    },

    created: function () {
        this.setSubscribed();
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

    watch: {
        // if the route changes, call again the method
        'Store.subscribedAt' () {
            this.setSubscribed();
        },
    },

    methods: {
        /**
         * Whether or not user has subscribed to the category
         *
         * @return void
         */
        setSubscribed() {
            if (Store.subscribedAt.indexOf(Store.category.id) != -1) {
                this.subscribed = true;
            } else {
                this.subscribed = false;
            }
        },

        /**
         * Subscribes to the category.
         *
         * @return void
         */
        subscribe() {
            this.subscribed = !this.subscribed;

            if (this.subscribed) {
            	Store.subscribedCategories.push(Store.category);
                Store.subscribedAt.push(Store.category.id);

            	Store.category.stats.subscribersCount ++;
            } else {
            	Store.category.stats.subscribersCount --;

            	let removeItem = Store.category.id;
				Store.subscribedCategories = Store.subscribedCategories.filter(function (category) {
				  	return category.id != removeItem;
				});

                let index = Store.subscribedAt.indexOf(Store.category.id);
                Store.subscribedAt.splice(index, 1);
            }

            axios.post('/subscribe', {
            	category_id: Store.category.id
            });
        }
    }
}
</script>
