<template>
    <div id="submissions" class="home-submissions">
        <div class="flex-space user-select">
            <h2 class="v-bold">
                Suggested Channels({{ list.length }}):
            </h2>

            <el-button round type="primary" @click="form = !form" size="medium">
                New
            </el-button>
        </div>

        <div v-show="form" class="form-wrapper user-select">
            <div class="form-group">
                <el-select
                        v-model="channel_name"
                        filterable
                        remote
                        placeholder="Search by name..."
                        :remote-method="getChannels"
                        loading-text="Loading..."
                        :loading="loading">
                    <el-option
                            v-for="item in channels"
                            :key="item"
                            :label="item"
                            :value="item">
                    </el-option>
                </el-select>
            </div>

            <div class="form-group">
                <label for="group" class="form-label">Group:</label>
                <input type="text" class="form-control" name="group" v-model="group" id="group"
                        placeholder="Group...">
            </div>

            <div class="form-group">
                <label for="index" class="form-label">Index:</label>
                <input type="number" class="form-control" name="index" v-model="z_index" id="index"
                        placeholder="Index...">
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
                        <router-link :to="'/c/' + item.channel.name">
                            <b>#{{ item.channel.name }}</b>
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
                        {{ item.channel.subscribers_count }}
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
</template>

<script>
export default {
    data() {
        return {
            form: false,
            loading: false,
            channel_name: null,
            group: '',
            z_index: 0,
            list: [],
            Store,
            channels: []
        };
    },

    created() {
        this.setDefaultChannels();
        this.getSuggesteds();
    },

    methods: {
        destroy(id) {
            axios
                .delete(`/admin/suggesteds/${id}`)
                .then((response) => {
                    this.list = this.list.filter(function(item) {
                        return item.id != id;
                    });
                });
        },

        getSuggesteds() {
            axios.get('/admin/suggesteds').then((response) => {
                this.list = response.data;
            });
        },

        getChannels: _.debounce(function(query) {
            if (!query) return;

            this.loading = true;

            axios
                .get('/search', {
                    params: {
                        type: 'Channels',
                        keyword: query
                    }
                })
                .then((response) => {
                    this.channels = _.map(response.data.data, 'name');
                    this.loading = false;
                });
        }, 600),

        /**
         * Sets the default value for suggestCats (uses user's already subscriber channels)
         *
         * @return void
         */
        setDefaultChannels() {
            let array = [];

            Store.state.subscribedChannels.forEach(function(element, index) {
                array.push(element.name);
            });

            this.channels = array;
        },

        updateSelected(newSelected) {
            this.channel_name = newSelected;
        },

        submit() {
            axios
                .post('/admin/suggesteds', {
                    channel_name: this.channel_name,
                    group: this.group,
                    z_index: this.z_index
                })
                .then((response) => {
                    this.channel_name = null;
                    this.group = '';
                    this.list.unshift(response.data);
                });
        }
    }
};
</script>
