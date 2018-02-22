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
                .post('/destroy-moderator', {
                    username: this.list.username,
                    channel_name: this.$route.params.name
                })
                .then(() => {
                    this.$emit('delete-moderator');
                });
        }
    }
};
</script>
