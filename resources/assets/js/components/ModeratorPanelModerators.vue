<template>
    <section>
        <h3 class="dotted-title">
			<span>
				Add New Moderator
			</span>
		</h3>

        <div class="form-group">
            <multiselect :value="username" :options="users" @input="updateSelected"
            @search-change="getUsers" :placeholder="'Search by username...'" :loading="loading"
            ></multiselect>
        </div>

        <div class="form-group">
            <multiselect :value="role" :options="roles" @input="updateRole"
                :placeholder="'Select Role...'"
            ></multiselect>
        </div>

        <div class="form-group">
            <button type="button" class="v-button v-button--green" :disabled="!role || !username" @click="addModerator">Add</button>
        </div>


        <h3 class="dotted-title">
			<span>
				All Moderators
			</span>
		</h3>

        <moderator v-for="(mod, index) in mods" :list="mod" :key="mod.id"
        @delete-moderator="mods.splice(index, 1)"></moderator>
    </section>
</template>

<script>
    import Multiselect from 'vue-multiselect'
    import Moderator from '../components/Moderator.vue'

    export default {
        components: { Moderator, Multiselect },

        mixins: [],

        data: function () {
            return {
                username: null,
                users: [],
                loading: false,
                roles: ['administrator', 'moderator'],
                role: 'moderator',
                mods: [],
            }
        },

        props: {
            //
        },

        computed: {
            //
        },

        created () {
            this.getMods()
        },

        mounted () {
            //
        },

        methods: {
            getMods(){
                axios.post('/moderators', {
                    category_name: this.$route.params.name
                }).then((response) => {
                    this.mods = response.data
                })
            },


            getUsers: _.debounce(function (query) {
                if (!query) return

                this.loading = true

                axios.get('/users', {
                	params: {
	                    username: query,
            			category: this.$route.params.name
                	}
                }).then((response) => {
                    this.users = response.data
                    this.loading = false
                })
            }, 600),


            updateSelected (newSelected) {
                this.username = newSelected
            },

            updateRole (newSelected) {
                this.role = newSelected
            },

            addModerator(){
                axios.post('/add-moderator', {
                    category_name: this.$route.params.name,
                    username: this.username,
                    role: this.role
                }).then((response) => {
                    this.username = null
                    this.role = 'moderator'

                    this.getMods()
                })
            }
        },

        beforeRouteEnter(to, from, next){
            if (Store.category.name == to.params.name) {
                // loaded
                if (Store.administratorAt.indexOf(Store.category.id) != -1) {
                    next()
                }
            } else {
                // not loaded but let's continue (the server-side is still protecting us!)
                next()
            }
        },
    };
</script>
