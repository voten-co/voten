<template>
    <section class="margin-top-1 v-box">
        <p>
            This is the last solution for dealing with spammers
        </p>

        <div class="form-group">
            <multiselect :value="username" :options="users" @input="updateSelected"
            @search-change="getUsers" :placeholder="'Search by username...'" :loading="loading"
            ></multiselect>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Reason(optional):</label>

            <textarea class="form-control" rows="3" v-model="description" id="description" placeholder="Why did the user wrong? (markdown syntax is supported)"></textarea>
        </div>

        <div class="form-group">
            <label for="duration" class="form-label">For how many days(leave 0 for permanent):</label>
            <input type="number" class="form-control" placeholder="For how many days(enter 0 for permanent)..."
            min="0" max="999" name="duration" v-model="duration" id="duration">
        </div>

        <div class="form-group">
            <button type="button" class="v-button v-button--red" :disabled="!username" @click="banUser">Ban</button>
        </div>


        <h1 class="dotted-title" v-if="bannedUsers.length">
			<span>
				All Banned Users
			</span>
		</h1>

        <banned-user v-for="banned in bannedUsers" :list="banned" :key="banned.id" @unban="unban"></banned-user>
    </section>
</template>


<script>
    import Multiselect from 'vue-multiselect'
    import BannedUser from '../components/BannedUser.vue'

    export default {
        components: {
            Multiselect,
            BannedUser
        },

        data: function () {
            return {
                loading: false,
                username: null,
                description: '',
                duration: 3,
                users: [],
                bannedUsers: [],
                Store
            }
        },

        created () {
            this.getBannedUsers()
        },

        mounted: function() {
            this.$nextTick(function() {
                this.$root.autoResize()
            })
        },

        methods: {
            updateSelected (newSelected) {
                this.username = newSelected
            },

            getUsers: _.debounce(function (query) {
                if (!query) return

                this.loading = true

                axios.post( '/admin/search-users', {
                    username: query
                } ).then((response) => {
                    this.users = response.data
                    this.loading = false
                })
            }, 600),

            banUser(){
                axios.post( '/ban-user', {
                    username: this.username,
                    description: this.description,
                    category: 'all',
                    duration: this.duration
                } ).then((response) => {
                    // add the banned user to the this.bannedUsers array
                    this.username = ''
                    this.description = ''
                    this.duration = 0

                    this.bannedUsers.unshift(response.data)
                })
            },

            /**
             * Unbans the user (destroy the ban record).
             *
             * @return void
             */
            unban (user_id) {
                 axios.post('/ban-user/destroy', {
                     user_id,
                     category: 'all'
                 }).then((response) => {
                    this.bannedUsers = this.bannedUsers.filter(function (item) {
                      	return item.user_id != user_id
                    })
                 })
            },

            /**
             * Fetches the list of already banned users on this category.
             *
             * @return void
             */
             getBannedUsers () {
                 axios.post('/banned-users', {
                     category: 'all'
                 }).then((response) => {
                     this.bannedUsers = response.data
                 })
            },
        },
    };
</script>
