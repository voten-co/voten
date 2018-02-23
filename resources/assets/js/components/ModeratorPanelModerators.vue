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
                        :remote-method="search"
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
                <el-button round type="success" size="medium" v-if="role && username" @click="addModerator" :loading="sending">
                    Add
                </el-button>
            </el-form-item>
        </el-form>

        <h3 class="dotted-title">
			<span>
				All Moderators
			</span>
        </h3>

        <moderator v-for="(item, index) in mods" :list="item.user" :role="item.role" :key="item.user.id"
                   @delete-moderator="mods.splice(index, 1)"></moderator>
    </section>
</template>

<script>
import Moderator from '../components/Moderator.vue';

export default {
    components: { Moderator },

    data() {
        return {
            username: null,
            users: [],
            loading: false,
            sending: false,
            role: 'moderator',
            mods: []
        };
    },

    created() {
        this.getMods();
    },

    methods: {
        getMods() {
            this.users = [];

            axios
                .get('/moderators', {
                    params: {
                        channel_name: this.$route.params.name
                    }
                })
                .then((response) => {
                    this.mods = response.data.data;
                });
        },

        search: _.debounce(function(query) {
            if (!query.trim()) return;
            this.loading = true;

            axios
                .get('/search', {
                    params: {
                        type: 'Users',
                        keyword: query
                    }
                })
                .then((response) => {
                    this.users = _.map(response.data.data, 'username');
                    this.loading = false;
                })
                .catch((error) => {
                    this.loading = false;
                });
        }, 600),

        addModerator() {
            this.sending = true;

            axios
                .post('/moderators', {
                    channel_id: Store.page.channel.temp.id,
                    username: this.username,
                    role: this.role
                })
                .then(() => {
                    this.username = null;
                    this.role = 'moderator';

                    this.getMods();
                    this.sending = false;
                })
                .catch(() => {
                    this.sending = false;
                });
        }
    },

    beforeRouteEnter(to, from, next) {
        if (Store.page.channel.temp.name == to.params.name) {
            // loaded
            if (
                Store.state.administratorAt.indexOf(
                    Store.page.channel.temp.id
                ) != -1
            ) {
                next();
            }
        } else {
            // not loaded but let's continue (the server-side is still protecting us!)
            next();
        }
    }
};
</script>
