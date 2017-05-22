<template>
	<section class="container margin-top-5 col-7">
		<h1 class="margin-top-bottom-1 align-center user-select">Got a question? Let's see if we have an answer for it!</h1>

		<div class="ui massive icon input flex-search margin-top-bottom-1">
			<input type="text" placeholder="Search questions..." v-model="filter">
			<i class="v-icon v-search search icon"></i>
		</div>

		<ul>
			<help-item :title="item.title" :text="item.body" v-for="item in sortedItems" :key="item.id"></help-item>
		</ul>
	</section>
</template>

<script>
    import HelpItem from '../components/HelpItem.vue';

    export default {

        data: function () {
            return {
                filter: '',
                items: [],
            }
        },

        components: {
            HelpItem: HelpItem,
        },

        created: function () {
            this.getItems();
        },

	    computed: {
	    	/**
	    	 * The sorted version of items
	    	 *
	    	 * @return {Array} items
	    	 */
	    	sortedItems () {
				var self = this
				return self.items.filter(function (item) {
					return item.title.toLowerCase().indexOf(self.filter.toLowerCase()) !== -1
				}).slice(0, 10)
	    	},
	    },

        methods: {
        	getItems: function () {
        		axios.post('/help-index').then((response) => {
                    this.items = response.data;
                });
        	}
        }
    }
</script>
