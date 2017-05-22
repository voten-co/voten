<template>
	<div class="margin-top-1">
		<div class="v-box">
			<table class="table">
				<thead>
					<tr>
						<th>Avatar</th>
						<th>Username</th>
						<th>Location</th>
						<th>Registered At</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="user in users">
						<td>
							<router-link :to="'/@' + user.username">
								<img :src="user.avatar" :alt="user.name">
							</router-link>
						</td>

						<td>
							<router-link :to="'/@' + user.username">
								{{ user.username }}
							</router-link>
						</td>

						<td>
							{{ user.location }}
						</td>

						<td>
							{{ date(user.created_at) }}
						</td>
					</tr>
				</tbody>
			</table>
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
                users: [],
				page: 0
            }
        },

        created () {
            this.getUsers()
            this.$eventHub.$on('scrolled-to-bottom', this.loadMore)
        },

        methods: {
        	loadMore () {
				if ( Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems && this.$route.name == 'admin-panel-users' ) {
					this.getUsers()
				}
			},

            getUsers () {
            	this.page ++
	            this.loading = true

            	axios.post('/admin/users', {
            		page: this.page
            	}).then((response) => {
					this.users = [...this.users, ...response.data.data]

					if (!this.users.length) {
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
        	}
        }
    };
</script>

<style>
	table img {
		max-width: 50px;
	    border-radius: 2px;
	}
</style>
