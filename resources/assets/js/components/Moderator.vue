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

<style>
    .banned-user {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .banned-user-wrapper {
        border-bottom: 1px dashed #dcdcdc;
        padding-bottom: .3em;
        margin-bottom: .3em;
    }

    .banned-user a {
        color: #333;
    }

    .banned-user img {
        width: 25px;
        height: auto;
        border-radius: 50%;
        margin-right: .2em;
    }

    .banned-user-description {
        background: #fdfdfd;
        color: #333;
        border: 1px solid #e7e7e7;
        padding: .5em;
        border-radius: 2px;
        line-height: 2;
        overflow: auto;
        margin: .3em;
    }
</style>
