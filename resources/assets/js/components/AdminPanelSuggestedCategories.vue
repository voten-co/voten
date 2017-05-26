<template>
    <div class="container margin-top-1">
        <div class="col-7">
            <div class="flex-space user-select">
                <h1 class="v-bold">Suggested Channels({{ list.length }}):</h1>

                <button class="v-button v-button--primary" @click="form = !form">
                    New
                </button>
            </div>

            <div v-show="form" class="form-wrapper user-select">
                <div class="form-group">
                    <multiselect :value="category_name" :options="categories" @input="updateSelected"
                    @search-change="getCategories" :placeholder="'Search by name...'" :loading="loading"
                    ></multiselect>
                </div>

                <div class="form-group">
                    <label for="group" class="form-label">Group:</label>
                    <input type="text" class="form-control" name="group" v-model="group" id="group" placeholder="Group...">
                </div>

                <div class="form-group">
                    <label for="index" class="form-label">Index:</label>
                    <input type="number" class="form-control" name="index" v-model="z_index" id="index" placeholder="Index...">
                </div>

                <button class="v-button v-button--green" @click="submit">
                    Create
                </button>
            </div>

            <div class="v-box">
				<table class="table">
				  	<thead>
					    <tr>
							<th>#channel</th>
							<th>Group</th>
							<th>Z_Index</th>
							<th>Subscribers</th>
							<th>Actions</th>
					    </tr>
				  	</thead>

				  	<tbody>
			  			<tr v-for="item in list">
					      	<td>
					      		<router-link :to="'/c/' + item.category.name">
					      		    <b>#{{ item.category.name }}</b>
					      		</router-link>
					      	</td>

					      	<td>
					      		{{ item.group }}
					      	</td>

                            <td>
					      		<span class="detail">
					      		    {{ item.z_index }}
					      		</span>
					      	</td>

                            <td>
					      		{{ item.category.subscribers }}
					      	</td>

					      	<td>
					      		<div class="display-flex">
				      				<i class="v-icon v-trash h-red pointer" @click="destroy(item.id)"></i>
					      		</div>
					      	</td>
					    </tr>
				  	</tbody>
				</table>
			</div>
        </div>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect'

    export default {
        components: {Multiselect},

        mixins: [],

        data () {
            return {
                form: false,
                loading: false,
                category_name: null,
                group: '',
                z_index: 0,
                list: [],
                Store,
                categories: [],
            }
        },

        props: {
            //
        },

        computed: {
            //
        },

        created () {
            this.setDefaultCategories()
            this.getSuggesteds()
        },

        mounted () {
            //
        },

        methods: {
            destroy(id) {
                axios.post('/admin/suggested/destroy', {
                    id
                }).then((response) => {
    				this.list = this.list.filter(function (item) {
    				  	return item.id != id
    				})
                })
            },

            getSuggesteds() {
                axios.post('/admin/suggesteds').then((response) => {
                    this.list = response.data
                })
            },

            getCategories: _.debounce(function (query) {
                if (!query) return

                this.loading = true

                axios.get( '/admin/get-categories', {
                    params: {
                    	name: query
                    }
                }).then((response) => {
                    this.categories = response.data
                    this.loading = false
                })
            }, 600),

            /**
             * Sets the default value for suggestCats (uses user's already subscriber channels)
             *
             * @return void
             */
            setDefaultCategories(){
                let array = []

                Store.subscribedCategories.forEach(function(element, index) {
                    array.push(element.name)
                })

                this.categories = array
            },

            updateSelected (newSelected) {
                this.category_name = newSelected
            },

            submit() {
                axios.post('/store-suggested-channel', {
                    category_name: this.category_name,
                    group: this.group,
                    z_index: this.z_index,
                }).then((response) => {
                    this.category_name = null
                    this.group = ''
                    this.list.unshift(response.data)
                })
            }
        }
    };
</script>
