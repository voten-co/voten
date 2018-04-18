<template>
    <section id="moderator-panel-rules">
        <h3 class="dotted-title">
			<span>
				Rules
			</span>
        </h3>

        <p>
            If your channel needs more rules than already written in Voten's general <a href="/tos">TOS</a>
            page, you may specify yours here.
            To keep Voten simple, we allow a maximum number of fine rules per channel. Markdown syntax is allowed.
        </p>

        <el-form label-position="top" label-width="10px">
            <el-form-item label="Rule" v-if="type == 'edit' || (type == 'create' && items.length < 5)">
                <el-input
                        type="textarea"
                        placeholder="Rule(markdown syntax is supported)..."
                        name="title"
                        :rows="4"
                        v-model="title">
                </el-input>

                <el-alert v-for="e in errors.title" :title="e" type="error" :key="e"></el-alert>
            </el-form-item>

            <el-form-item v-if="type == 'create' && items.length < 5">
                <el-button round size="medium" type="success" v-if="title" @click="createRule" :loading="sending">
                    Submit
                </el-button>
            </el-form-item>

            <el-form-item v-if="type == 'edit'">
                <el-button round size="medium" type="success" :v-if="title" @click="patch" :loading="editing">
                    Edit
                </el-button>
            </el-form-item>
        </el-form>


        <h3 class="dotted-title" v-if="items.length">
			<span>
				All Rules
			</span>
        </h3>

        <rule v-for="rule in items" :list="rule" :key="rule.id" @delete-rule="destroy" @edit-rule="editRule"></rule>
    </section>
</template>

<script>
import Rule from '../components/Rule.vue';

export default {
    components: { Rule },

    data() {
        return {
            sending: false,
            editing: false,
            errors: [],
            title: null,
            id: null,
            channel_id: null,
            items: [],
            type: 'create'
        };
    },

    created() {
        this.getItems();
    },

    methods: {
        createRule() {
            this.sending = true;

            axios
                .post(`/channels/${Store.page.channel.temp.id}/rules`, {
                    body: this.title
                })
                .then((response) => {
                    this.items.unshift(response.data.data);
                    this.clear();
                    this.sending = false;
                })
                .catch((error) => {
                    this.errors = error.response.data.errors;
                    this.sending = false;
                });
        },

        getItems() {
            this.loading = true;
            app.$Progress.finish();
            app.$Progress.start(); 

            axios
                .get(`/channels/${Store.page.channel.temp.id}/rules`, {
                    params: {
                        channel_name: this.$route.params.name
                    }
                })
                .then((response) => {
                    this.items = response.data.data;
                    this.loading = false;
                    app.$Progress.finish(); 
                })
                .catch(() => {
                    this.loading = false;
                    app.$Progress.fail(); 
                });
        },

        destroy(rule_id, channel_id) {
            axios
                .delete(`/channels/${Store.page.channel.temp.id}/rules/${rule_id}`)
                .then(() => {
                    this.items = this.items.filter(function(item) {
                        return item.id != rule_id;
                    });
                });
        },

        editRule(rule) {
            this.title = rule.body;
            this.id = rule.id;
            this.channel_id = rule.channel_id;

            this.type = 'edit';
        },

        patch() {
            this.editing = true;

            axios
                .patch(`/channels/${Store.page.channel.temp.id}/rules/${this.id}`, {
                    body: this.title,
                })
                .then(() => {
                    let id = this.id;

                    function findObject(ob) {
                        return ob.id === id;
                    }

                    this.items.find(findObject).body = this.title;

                    this.clear();
                    this.editing = false;
                })
                .catch((error) => {
                    this.errors = error.response.data.errors;
                    this.editing = false;
                });
        },

        clear() {
            this.title = null;
            this.id = null;
            this.channel_id = null;
            this.type = 'create';
            this.errors = [];
        }
    }
};
</script>
