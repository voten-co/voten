<template>
	<div class="container margin-top-1">
        <div class="col-7">
          	<h1 class="v-bold user-select">Feedbacks:</h1>

          	<div class="v-box">
          		<p v-if="!feedbacks.length">
          			Seems like nobody has given a shit
          		</p>

				<table class="table" v-if="feedbacks.length">
				  	<thead>
					    <tr>
							<th>Subject</th>
							<th>User</th>
							<th>Description</th>
							<th>Submitted At</th>
							<th>Action</th>
					    </tr>
				  	</thead>
				  	<tbody>
				  		<tr v-for="f in feedbacks">
					      	<td>
					      		<b>{{ f.subject }}</b>
					      	</td>

					      	<td>
					      		<router-link :to="'/@' + f.owner.username">
					      			{{ f.owner.username }}
					      		</router-link>
					      	</td>

					      	<td>
					      		{{ f.description }}
					      	</td>

					      	<td>
					      		<!-- {{ $feedback->created_at->diffForHumans() }} -->
					      		{{ date(f.created_at) }}
					      	</td>

					      	<td>
					      		<div class="display-flex user-select">
					      			<i class="v-icon v-trash pointer h-red" @click="destroy(f.id)"></i>
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
    export default {
        data: function () {
            return {
                feedbacks: []
            }
        },

        created () {
            this.getFeedbacks()
        },

        methods: {
        	destroy (feedback_id) {
        		axios.post('/feedback/delete', {
        			id: feedback_id
        		})

        		this.feedbacks = this.feedbacks.filter(function (item) {
				  	return item.id != feedback_id
				})
        	},

        	date (time) {
				if (moment(time).format('DD/MM/YYYY') == moment(new Date()).format('DD/MM/YYYY')) {
					return moment().utc(time).format("LT")
				}

				return moment(time).utc(moment().format("MMM Do")).format("MMM Do")
        	},

            getFeedbacks () {
            	axios.post('/big-daddy/feedbacks').then((response) => {
            		this.feedbacks = response.data
            	})
            }
        }
    };
</script>
