<template>
    <section class="banned-user-wrapper">
        <div class="banned-user">
            <div class="left">
                <router-link :to="'/@' + list.username">
                    <img :src="list.avatar" :alt="list.username">
                    {{ list.username }}
                </router-link>
            </div>

            <div class="detail">
                {{ role }}
            </div>

            <div class="actions">
                <el-tooltip content="Remove" placement="top" transition="false" :open-delay="500">
                    <i class="pointer v-icon go-gray v-delete h-red" @click="destroy" :class="!owns ? '' : 'display-hidden'"></i>
                </el-tooltip>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {
            auth
        };
    },

    props: ['list', 'role'],

    computed: {
        owns() {
            return this.list.id == auth.id;
        }
    },

    methods: {
        destroy() {
            axios
                .delete(`/channels/${Store.page.channel.temp.id}/moderators/${this.list.id}`)
                .then(() => {
                    this.$emit('delete-moderator');
                });
        }
    }
};
</script>
