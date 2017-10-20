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

    props: [
        'subscribed-class', 
        'unsubscribed-class'
    ], 

    created: function () {
        this.setSubscribed();
    },

    computed: {
    	activeClass() {
    		if (this.subscribed) {
    			return this.subscribedClass;
    		}

    		if (!this.subscribed) {
    			return this.unsubscribedClass;
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

            	Store.category.subscribers ++;
            } else {
            	Store.category.subscribers --;

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
