<template>
	<div class="container margin-top-1">
	    <div class="col-7">
          	<div class="flex-space user-select">
          		<h1 class="v-bold">Helps({{ items.length }}):</h1>

          		<button class="v-button v-button--primary" @click="newForm">
          			New
          		</button>
          	</div>

          	<div v-show="form" class="form-wrapper user-select">
          		<div class="form-group">
                  	<label for="title" class="form-label">Question:</label>
                  	<input type="text" class="form-control" name="title" v-model="title" id="title" placeholder="Question...">
                </div>

				<div class="form-group">
				  	<label for="body" class="form-label">Answer:<small class="go-gray"> (Markdown syntax is supported)</small></label>
				  	<textarea class="form-control" rows="3" name="body" v-model="body" id="body" placeholder="Answer..."></textarea>
				</div>

                <button class="v-button v-button--green" @click="submit" v-if="type == 'create'">
                  	Create
                </button>

                <button class="v-button v-button--green" @click="submitPatch" v-if="type == 'edit'">
                  	Edit
                </button>
          	</div>

          	<div v-show="form && body" class="form-wrapper">
                <markdown :text="body"></markdown>
          	</div>

          	<div class="v-box">
				<table class="table">
				  	<thead>
					    <tr>
							<th>Question</th>
							<th>Answer</th>
							<th>Action</th>
					    </tr>
				  	</thead>

				  	<tbody>
			  			<tr v-for="item in items">
					      	<td>
					      		<b>{{ item.title }}</b>
					      	</td>

					      	<td>
					      		{{ item.body }}
					      	</td>

					      	<td>
					      		<div class="display-flex">
					      			<i class="v-icon v-edit h-green pointer" @click="patch(item)"></i>

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
    import Markdown from '../components/Markdown.vue'

    export default {
        components: { Markdown },

        data: function () {
            return {
            	title: '',
            	body: '',
            	id: '',

            	type: 'create',

            	form: false,
                items: []
            }
        },

        created () {
            this.getItems()
        },

		mounted: function () {
			this.$nextTick(function () {
				this.$root.autoResize()
			})
		},

        methods: {
            getItems () {
            	axios.post('/help-index').then((response) => {
            		this.items = response.data
            	})
            },

            destroy (id) {
            	axios.post('/delete-help', { id: id })

        		this.items = this.items.filter(function (item) {
				  	return item.id != id
				})
            },

            newForm () {
            	this.clear()
            	this.form = !this.form
            	this.type = 'create'
            },

            clear () {
            	this.title = ''
            	this.body = ''
            	this.id = ''
            },

            submit () {
            	if (!this.title || !this.body) {
            		return
            	}

            	axios.post('/new-help', {
            		title: this.title,
            		body: this.body
            	}).then((response) => {
            		this.items.push(response.data)
            	})

            	this.title = ''
            	this.body = ''
            },

            patch (item) {
            	this.title = item.title
            	this.body = item.body
            	this.id = item.id

            	this.form = true
            	this.type = 'edit'
            },

            submitPatch () {
            	if (!this.title || !this.body || !this.id) {
            		return
            	}

            	axios.post('/edit-help', {
            		id: this.id,
            		title: this.title,
            		body: this.body
            	}).then((response) => {
            		var id = response.data.id

		            function findObject(ob) {
		                return ob.id === id
		            }

		            this.items.find(findObject).title = response.data.title
		            this.items.find(findObject).body = response.data.body
            	})

            	this.title = ''
            	this.body = ''
            	this.id = ''
            	this.form = false
            }
        }
    };
</script>

<style>
	.form-wrapper {
	    padding: 1em;
		margin: 1em 0 2em 0;
	    border: 2px dashed #dcdcdc;
	    border-radius: 2px;
	}
</style>
