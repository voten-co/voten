<template>
	<div class="category-header-small" v-bind:style="{ background: coverBackground }">
		<button class="no-padding" type="button" @click="goBack">
			<i class="v-left"></i>
		</button>

		<router-link :to="'/c/' + name">
			<img :src="avatar" :alt="name">

			<h2>
				{{ '#' + name }}
			</h2>
		</router-link>

		<subscribe subscribed-class="unsubscribe" unsubscribed-class="subscribe"></subscribe>
	</div>
</template>


<script>
	import Subscribe from '../components/SubscribeButton.vue';

    export default {
        data () {
            return {
                Store
            }
		},

		created() {
			this.updateCategoryStore();
		}, 

		watch: {
			'$route'() {
				if (this.$route.name !== 'submission-page') return;
	
				this.updateCategoryStore();
			}
		},
		
		components: {Subscribe}, 

        computed: {
			name() {
				return Store.page.category.temp.name;
			}, 

			avatar() {
				return Store.page.category.temp.avatar;
			}, 

			coverBackground () {
	        	if (Store.page.category.temp.color == 'Red') {
	        		return '#9a4e4e'
	        	} else if (Store.page.category.temp.color == 'Blue') {
	        		return '#5487d4'
	        	} else if (Store.page.category.temp.color == 'Dark Blue') {
	        		return '#2f3b49'
	        	} else if (Store.page.category.temp.color == 'Dark Green') {
	        		return '#507e75'
	        	} else if (Store.page.category.temp.color == 'Bright Green') {
	        		return 'rgb(117, 148, 127)'
	        	} else if (Store.page.category.temp.color == 'Purple') {
	        		return '#4d4261'
	        	} else if (Store.page.category.temp.color == 'Orange') {
	        		return '#ffaf40'
	        	} else if (Store.page.category.temp.color == 'Pink') {
	        		return '#ec7daa'
	        	} else { // userStore.color == 'Black'
	        		return '#424242'
	        	}
	        }
		},
		
		methods: {
			/**
	    	 * Checks wheather or not the Store.page.category.temp needs to be filled or updated, and if yes simply does it
	    	 *
	    	 * @return void
	    	 */
			updateCategoryStore() {
				this.$root.getCategoryStore(this.$route.params.name);
				this.category = this.$route.params.name;
			},

			goBack() {
				history.go(-1); 
			}
		}
    };
</script>
