<template>
    <button :class="activeClass" @click="subscribe" v-text="content"></button>
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
        'Store.state.subscribedAt' () {
            this.setSubscribed();
        },

        'Store.page.category' () {
            this.setSubscribed();
        }
    },

    methods: {
        /**
         * Whether or not user has subscribed to the category
         *
         * @return void
         */
        setSubscribed() {
            if (Store.state.subscribedAt.indexOf(Store.page.category.id) != -1) {
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
            	Store.state.subscribedCategories.push(Store.page.category);
                Store.state.subscribedAt.push(Store.page.category.id);

            	Store.page.category.subscribers ++;
            } else {
            	Store.page.category.subscribers --;

            	let removeItem = Store.page.category.id;
				Store.state.subscribedCategories = Store.state.subscribedCategories.filter(function (category) {
				  	return category.id != removeItem;
				});

                let index = Store.state.subscribedAt.indexOf(Store.page.category.id);
                Store.state.subscribedAt.splice(index, 1);
            }

            axios.post('/subscribe', {
            	category_id: Store.page.category.id
            });
        }
    }
}
</script>
