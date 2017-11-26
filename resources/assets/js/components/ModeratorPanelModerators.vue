<template>
    <section>
        <h3 class="dotted-title">
			<span>
				Add New Moderator
			</span>
        </h3>

        <el-form label-position="top" label-width="10px">
            <el-form-item label="Username">
                <el-select
                        v-model="username"
                        filterable
                        remote
                        placeholder="Search by username..."
                        :remote-method="getUsers"
                        loading-text="Loading..."
                        :loading="loading">
                    <el-option
                            v-for="item in users"
                            :key="item"
                            :label="item"
                            :value="item">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="Role">
                <el-radio-group v-model="role">
                    <el-radio label="administrator" border></el-radio>
                    <el-radio label="moderator" border></el-radio>
                </el-radio-group>
            </el-form-item>

            <el-form-item>
                <el-button type="success" size="medium" v-if="role && username" @click="addModerator" :loading="sending">
                    Add
                </el-button>
            </el-form-item>
        </el-form>

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
    import Moderator from '../components/Moderator.vue';

    export default {
        components: { Moderator },

        mixins: [],

        data: function () {
            return {
                username: null,
                users: [],
                loading: false,
                sending: false,
                role: 'moderator',
                mods: [],
            }
        },

        created () {
            this.getMods()
        },

        methods: {
            getMods() {
                this.users = [];

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

            addModerator() {
                this.sending = true;

                axios.post('/add-moderator', {
                    category_name: this.$route.params.name,
                    username: this.username,
                    role: this.role
                }).then(() => {
                    this.username = null
                    this.role = 'moderator'

                    this.getMods();
                    this.sending = false;
                }).catch(() => {
                    this.sending = false;
                });
            }
        },

        beforeRouteEnter(to, from, next){
            if (Store.page.category.name == to.params.name) {
                // loaded
                if (Store.state.administratorAt.indexOf(Store.page.category.id) != -1) {
                    next()
                }
            } else {
                // not loaded but let's continue (the server-side is still protecting us!)
                next()
            }
        },
    };
</script>
