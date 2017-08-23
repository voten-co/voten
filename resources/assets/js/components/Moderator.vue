<template>
    <section class="banned-user-wrapper">
        <div class="banned-user">
            <div class="left">
                <router-link :to="'/@' + list.username">
                    <img :src="list.avatar" :alt="list.username">
                    {{ list.username }}
                </router-link>
            </div>

            <div class="detail"
            data-toggle="tooltip" data-placement="top" :title="list.subject">
                {{ list.pivot.role }}
            </div>

            <div class="actions">
                <i class="pointer v-icon go-gray v-delete h-red" @click="destroy"
                    :class="!owns ? '' : 'display-hidden'"
                    data-toggle="tooltip" data-placement="top" title="Remove"></i>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        components: {},

        data: function () {
            return {
                auth
            }
        },

        props: ['list'],

        computed: {
            owns(){
                return this.list.id == auth.id
            }
        },

        mounted: function() {
            this.$nextTick(function() {
                this.$root.loadSemanticTooltip()
            })
        },

        methods: {
            destroy(){
                axios.post('/destroy-moderator', {
                    username: this.list.username,
                    category_name: this.$route.params.name
                }).then((response) => {
                    this.$emit('delete-moderator')
                })
            }
        }
    };
</script>
