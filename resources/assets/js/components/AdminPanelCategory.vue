<template>
	<div class="container margin-top-1">
        <div class="col-7">
          	<h1 class="v-bold user-select">Latest Created #Channels:</h1>

          	<div class="v-box">
				<table class="table">
				  	<thead>
					    <tr>
							<th>Avatar</th>
							<th>Name</th>
							<th>Description</th>
							<th>Created at</th>
					    </tr>
				  	</thead>
				  	<tbody>
				  		<tr v-for="category in categories">
					      	<td>
						      	<router-link :to="'/c/' + category.name">
						      		<img :src="category.avatar" :alt="category.name">
						      	</router-link>
					      	</td>

					      	<td>
						      	<router-link :to="'/c/' + category.name">
					      			{{ category.name }}
				      			</router-link>
					      	</td>

					      	<td>
					      		{{ category.description }}
					      	</td>

					      	<td>
					      		{{ date(category.created_at) }}
					      	</td>
					    </tr>
				  	</tbody>
				</table>
			</div>
	    </div>
	</div>
</template>

<script>
    export default {
        data: function () {
            return {
            	NoMoreItems: false,
            	nothingFound: false,
				loading: false,
                categories: [],
				page: 0
            }
        },

        created () {
            this.getCategories()
            this.$eventHub.$on('scrolled-to-bottom', this.loadMore)
        },

        methods: {
        	loadMore () {
				if ( Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems && this.$route.name == 'admin-panel-categories' ) {
					this.getCategories()
				}
			},

            getCategories () {
            	this.page ++
	            this.loading = true

            	axios.post('/admin/channels', {
            		page: this.page
            	}).then((response) => {
					this.categories = [...this.categories, ...response.data.data]

					if (!this.categories.length) {
						this.nothingFound = true
					}

					if (response.data.next_page_url == null) {
						this.NoMoreItems = true
					}

					this.loading = false
            	})
            },

            date (time) {
				if (moment(time).format('DD/MM/YYYY') == moment(new Date()).format('DD/MM/YYYY')) {
					return moment().utc(time).format("LT")
				}

				return moment(time).utc(moment().format("MMM Do")).format("MMM Do")
        	},
        }
    };
</script>

<style>
	table img {
		max-width: 50px;
	    border-radius: 2px;
	}
</style>
