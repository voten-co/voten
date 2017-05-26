<template>
<div>
	<div class="v-box">
    	<div class="ui teal message" v-if="sentInvite">
			<i class="fa fa-times close icon" aria-hidden="true"></i>
			{{ sentInvite }}
		</div>

		<h3 class="weight-500 go-green">Invite a friend:</h3>
		<p>At the moment, Voten is in beta phase, which means only invited users can join us. Here you can send an invitation email to a friend of yours.</p>
		<div class="form-group">
			<input type="text" v-model="inviteTo" class="form-control" placeholder="Email Address...">
			<small class="text-muted go-red" v-for="e in inviteErrors.email">{{ e }}</small>
		</div>
		<div class="form-group flex-space">
			<button type="button" class="v-button" @click="invite">Send Invitation</button>
			<a href="/invites">All invitations</a>
		</div>
	</div>

	<div class="v-box">
		<div class="ui teal message" v-if="bannedUser">
			<i class="fa fa-times close icon" aria-hidden="true"></i>
			{{ bannedUser }}
		</div>

		<h3 class="weight-500 go-primary">Ban User:</h3>
		<p>Found a user that is doing nothing but spam? Then let's ban him/her from submitting to your channel.</p>

		<div class="form-group">
			<input type="text" v-model="username" class="form-control" placeholder="Username..." v-bind:value="selectedUsername" v-on:input="getUsers(username)">
			<small class="text-muted go-red" v-for="e in banErrors.username">{{ e }}</small>
			<ul class="category-hashtags" v-show="users.length">
                <li v-for="user in sortedUsers"><a href="#" @click.prevent="selectUsername(user)">@{{ user }}</a></li>
            </ul>
		</div>

		<div class="form-group flex-space">
			<button type="button" class="v-button" @click="banUser">Ban User</button>
			<a href="/invites">All Banned Users</a>
		</div>
	</div>

	<div class="v-box">
		<div class="ui teal message" v-if="blockedDomain">
			<i class="fa fa-times close icon" aria-hidden="true"></i>
			{{ blockedDomain }}
		</div>

		<h3 class="weight-500 go-red">Block Domain:</h3>
		<p>You may ban domains that contain nothing but spam. This is just a tool for preventing spam in your channel. </p>
		<div class="form-group">
			<input type="text" v-model="domain" class="form-control" placeholder="URL...">
			<small class="text-muted go-red" v-for="e in blockErrors.url">{{ e }}</small>
		</div>
		<div class="form-group flex-space">
			<button type="button" class="v-button" @click="blockDomain">Block Domain</button>
			<a href="/invites">All Blocked Domains</a>
		</div>
	</div>
</div>

</template>

<script>
    export default {

    	data: function(){
    		return {
    			inviteTo: '',
    			domain: '',
    			username: '',
    			catInfo,

    			sentInvite: '',
    			inviteErrors: [],

    			blockedDomain: '',
    			blockErrors: [],

    			bannedUser: '',
    			banErrors: [],

    			users: [],
    		}
    	},

	    computed: {
	    	/**
	    	 * The sorted version of comments
	    	 *
	    	 * @return {Array} comments
	    	 */
	    	sortedUsers () {
				var self = this

				return self.users.filter(function (user) {
					return user.name.indexOf(self.username) !== -1
				}).slice(0, 20)
	    	}
	    },

        methods: {
    	  	getUsers: _.debounce(function (typed) {
                if(!typed){
                    return
                }

                axios.get('/users', {
                	params: {
                		username: typed
                	}
                }).then((response) => {
                    this.users = response.data
                })
		  	}, 600),

            selectUsername: function(selected){
                this.username = selected
                this.users = []
            },


            invite: function () {
            	if(!this.inviteTo) return;

            	this.sentInvite = '';
            	this.inviteErrors = [];

                axios.post( '/send-invite', {
                    email: this.inviteTo,
                    category: this.catInfo.category_name,
                }).then((response) => {
                    this.inviteTo = '';
                    this.inviteErrors = [];
                    this.sentInvite = response.data;
                }).catch((error) => {
                    this.inviteErrors = error.response.data;
                });
            },


                blockDomain: function () {
	            	if(!this.domain){
	            		return;
	            	}

	            	this.blockedDomain = '';
	            	this.blockErrors = [];

	                axios.post( '/block-domain', {
	                    url: this.domain,
	                    category: this.catInfo.category_name,
	                } ).then((response) => {
	                    this.domain = '';
	                    this.blockErrors = [];
	                    this.blockedDomain = response.data;
	                }).catch((error) => {
                        this.blockErrors = error.response.data;
                    });
                },


                banUser: function () {
	            	if(!this.username){
	            		return;
	            	}

	            	this.bannedUser = '';
	            	this.banErrors = [];

	                axios.post( '/ban-user', {
	                    username: this.username,
	                    category: this.catInfo.category_name,
	                } ).then((response) => {
	                    this.username = '';
	                    this.banErrors = [];
	                    this.bannedUser = response.data;
	                }).catch((error) => {
                        this.banErrors = error.response.data;
                    });
                },
        },
    }
</script>
