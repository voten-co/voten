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
				isActive: null, 
                Store
            }
		},

		activated() {
			this.isActive = true;
		},
		deactivated() {
			this.isActive = false;
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
				return Store.category.name; 
			}, 

			avatar() {
				return Store.category.avatar; 
			}, 

			coverBackground () {
	        	if (Store.category.color == 'Red') {
	        		return '#9a4e4e'
	        	} else if (Store.category.color == 'Blue') {
	        		return '#5487d4'
	        	} else if (Store.category.color == 'Dark Blue') {
	        		return '#2f3b49'
	        	} else if (Store.category.color == 'Dark Green') {
	        		return '#507e75'
	        	} else if (Store.category.color == 'Bright Green') {
	        		return 'rgb(117, 148, 127)'
	        	} else if (Store.category.color == 'Purple') {
	        		return '#4d4261'
	        	} else if (Store.category.color == 'Orange') {
	        		return '#ffaf40'
	        	} else if (Store.category.color == 'Pink') {
	        		return '#ec7daa'
	        	} else { // userStore.color == 'Black'
	        		return '#424242'
	        	}
	        }
		},
		
		methods: {
			/**
	    	 * Checks wheather or not the Store.category needs to be filled or updated, and if yes simply does it
	    	 *
	    	 * @return void
	    	 */
			updateCategoryStore() {
				if (Store.category.name == undefined || Store.category.name != this.$route.params.name) {
					this.$root.getCategoryStore(this.$route.params.name);
					this.category = this.$route.params.name;
				}
			},

			goBack() {
				history.go(-1); 
			}
		}
    };
</script>
