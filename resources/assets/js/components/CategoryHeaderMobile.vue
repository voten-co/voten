<template>
	<div class="category-header-mobile" v-bind:style="{ background: coverBackground }">
		<router-link :to="'/c/' + Store.category.name">
			<img :src="Store.category.avatar" :alt="Store.category.name">

			<h2>
				{{ '#' + Store.category.name }}
			</h2>
		</router-link>
	</div>
</template>

<style>
	.category-header-mobile {
		margin-bottom: 1em;
		width: 100%;
		border-bottom: 2px solid;
    	border-color: rgba(117, 148, 127, 0.24);
    	padding: 1em .5em;
    	color: #f8f8f8;
    	text-align: center;
	}

	.category-header-mobile a {
		color: #f8f8f8;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.category-header-mobile h2 {
		margin: 0;
	}

	.category-header-mobile img {
	    width: 40px;
	    height: 40px;
    	border-radius: 2px;
    	margin-right: .5em;
	}
</style>

<script>
    export default {
        data () {
            return {
                Store
            }
        },

        computed: {
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
	        		return '#333'
	        	}
	        }
        },
    };
</script>
