<template>
<div>
	<div class="flex-space">
		<div>
			<h1>Reports</h1>
		</div>
		<div>
			<button type="button" class="v-button" :disabled="route == 'unsolved'" @click="router('unsolved')">Unsolved</button>
			<button type="button" class="v-button" :disabled="route == 'solved'" @click="router('solved')">Solved</button>
			<button type="button" class="v-button" :disabled="route == 'deleted'" @click="router('deleted')">Deleted</button>
		</div>
	</div>

	<div class="v-box">
		<table class="table">
		  	<thead>
			    <tr>
					<th>Title</th>
					<th>Subject</th>
					<th>Date</th>
					<th></th>
					<th></th>
			    </tr>
		  	</thead>
		  	<tbody>
		  		<tr v-for="item in items">
			      	<td>
			      		<a v-bind:href="'/s/' + item.submission.slug"><b>{{ item.submission.title }}</b></a> -
			      		<span class="go-gray">
			      			reported by
			      			<a v-bind:href="'/@' + item.reporter.username" class="go-gray">{{ item.reporter.username }}</a>
			      		</span>
			      	</td>

			      	<td>
			      		<div class="s-popup report-subject" v-bind:data-content="item.description ? item.description : 'No Description'">{{ item.subject }}</div>
			      	</td>

			      	<td class="font-small">{{ date(item.created_at) }}</td>
			      	<td class="is-icon">
			        	<a class="pointer h-red" @click="deleteIt(item)" v-if="!item.submission.deleted_at" v-tooltip.left="{content: 'Block'}">
			          		<i class="v-icon flaticon-dustbin"></i>
			        	</a>
			      	</td>
			      	<td class="is-icon">
			        	<a class="pointer h-green" @click="approve(item)" v-if="!item.submission.approved" v-tooltip.left="{content: 'Approve'}">
			          		<i class="flaticon-ok-mark"></i>
			        	</a>
			      	</td>
			    </tr>
		  	</tbody>
		</table>

		<p v-if="!items.length" class="align-center">
  			No report was found.
  		</p>
		<button class="v-button v-button--block">Load More</button>
	</div>
</div>
</template>

<script>
    export default {

        props: ['category'],

        data: function () {
            return {
                items: [],
                route: 'unsolved',
            }
        },

        created: function() {
        	this.getReports()
        },

		mounted: function () {
			this.$nextTick(function () {
	        	this.loadSemanticPopup();
			})
		},

        methods: {
        	/**
        	 * Loads Semantic UI's popup component
        	 *
        	 * @return void
        	 */
        	loadSemanticPopup () {
	            _.delay(function()
	            {
	                $('.s-popup').popup({
						inline: true
					});
	            }, 100 )
        	},
        	/**
        	* Switches the route also recalls the getReports()
        	*/
			router: function (r) {
				this.route = r;
				this.getReports();
			},


			/**
        	* Fetches the reported records
        	*/
			getReports: function () {
                axios.post('/api/channel-reports', {
                		category: this.category,
                		route: this.route,
                	}).then((response) => {
	                    this.items = response.data.data;
                });
            },


        	/**
            *  Diactivates the item so the user can still see it but others won't. Also removes the item from the list.
            */
        	deleteIt: function (item) {
        		var index = this.items.indexOf(item);
        		this.items.splice(index, 1);

        		axios.post('/deactive-submission', {
                		submission_id: item.submission.id,
                		report_id: item.id,
                	}).then((response) => {});
        	},


        	/**
            *  Approves the submission so it can't reported.
            */
        	approve: function (item) {
        		var index = this.items.indexOf(item);
        		this.items.splice(index, 1);

        		axios.post('/approve-submission', {
                		submission_id: item.submission.id,
                		report_id: item.id,
                	}).then((response) => {});
        	},


        	/**
            *  converts time to user-friendly format
            */
            date: function (time) {
                return moment(time).utc(moment().format("Z")).fromNow()
            },

        },

    }
</script>