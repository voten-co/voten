<template>
    <button :class="activeClass" @click="subscribe" v-text="content"></button>
</template>

<script>
export default {
    data: function () {
        return {
            Store
        }
    },

    props: [
        'subscribed-class', 
        'unsubscribed-class'
    ], 

    computed: {
    	activeClass() {
    	    return this.subscribed ? this.subscribedClass : this.unsubscribedClass;
    	},

    	content () {
    		return this.subscribed ? 'Unsubscribe' : 'Subscribe';
    	},

        subscribed: {
            get() {
                return Store.state.subscribedAt.indexOf(Store.page.category.temp.id) !== -1 ? true : false;
            },

            set() {
                if (Store.state.subscribedAt.indexOf(Store.page.category.temp.id) !== -1) {
                    Store.page.category.temp.subscribers --;

                    let removeItem = Store.page.category.temp.id;
                    Store.state.subscribedCategories = Store.state.subscribedCategories.filter(category => category.id != removeItem);

                    let index = Store.state.subscribedAt.indexOf(Store.page.category.temp.id);
                    Store.state.subscribedAt.splice(index, 1);

                    return;
                }

                Store.state.subscribedCategories.push(Store.page.category.temp);
                Store.state.subscribedAt.push(Store.page.category.temp.id);
                Store.page.category.temp.subscribers ++;
            }
        },
    },

    methods: {
        subscribe: _.debounce(function () {
            this.subscribed = !this.subscribed;

            axios.post('/subscribe', {
            	category_id: Store.page.category.temp.id
            }).catch(() => {
                this.subscribed = !this.subscribed;
            });
        }, 200, { leading: true, trailing: false }),
    }
}
</script>
