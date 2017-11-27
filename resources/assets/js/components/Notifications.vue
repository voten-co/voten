<template>
    <div class="vo-modal" id="notifications">
        <header class="user-select">
            <div class="flex-space">
                <!-- Modal Title -->
                <div class="vo-modal-title"><h1 class="title">Notifications</h1></div>

                <!-- Close Button -->
                <el-tooltip content="Close (esc)" placement="bottom-end" transition="false" :open-delay="500">
                    <div class="v-close" @click="close">
                        <i class="v-icon block-before v-cancel" aria-hidden="true"></i>
                    </div>
                </el-tooltip>
            </div>
        </header>

        <div class="middle background-white"
             :class="{'flex-center' : !Store.state.notifications || ! Store.state.notifications.length}">
            <div class="col-7">
                <div class="user-select v-nth-box" v-if=" !Store.state.notifications || ! Store.state.notifications.length">
                    <notification-icon width="250" height="250" class="margin-bottom-3"></notification-icon>

                    <h3 class="no-notifications">
                        No notifications here yet
                    </h3>
                </div>

                <ul class="v-contact-list user-select">
                    <notification v-for="n in uniqueList" :notification="n" :key="n.id"></notification>
                </ul>

                <div class="align-center">
                    <el-button type="primary"
                               class="v-button-big margin-top-bottom-3"
                               @click="loadReadNotifications" v-show="loadMoreButton">
                        Load More
                    </el-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Notification from '../components/Notification.vue';
    import Helpers from '../mixins/Helpers';
    import NotificationIcon from './Icons/NotificationIcon.vue';


    export default {
        mixins: [Helpers],

        components: { Notification, NotificationIcon },

        data() {
            return {
                page: 1,
                loadMoreButton: false,
            }
        },

        created() {
            this.getNotifications();
            this.listen();
        },

        computed: {
            /**
             * Due to the issue with duplicate notifiactions (cuz the present ones have diffrent
             * timestamps) we need a different approch to make sure the list is always unique.
             * This ugly coded methods does it! Maybe move this to the Helpers.js mixin?!
             *
             * @return array
             */
            uniqueList() {
                let unique = [];
                let temp = [];

                if (Store.state.notifications) {
                    Store.state.notifications.forEach(function (element, index, self) {
                        if (temp.indexOf(element.id) === -1) {
                            unique.push(element);
                            temp.push(element.id);
                        }
                    })
                }

                return unique;
            }
        },


        methods: {
            close() {
                this.$eventHub.$emit('close')
            },

            /**
             * Loads all the unread notifications of the Auth user.
             *
             * @return void
             */
            getNotifications() {
                axios.get('/notifications').then((response) => {
                    if (response.data.length > 0) {
                        Store.state.notifications = response.data;
                    }

                    this.loadReadNotifications();
                });
            },

            /**
             * loads read notifications of the Auth user.
             *
             * @return void
             */
            loadReadNotifications() {
                this.loadMoreButton = false

                axios.post('/all-notifications', { page: this.page }).then((response) => {
                    Store.state.notifications.push(...response.data.data);

                    this.page++;

                    if (response.data.next_page_url) {
                        this.loadMoreButton = true;
                    }
                })
            },

            /**
             * listen for broadcasted notifications
             *
             * @return void
             */
            listen() {
                Echo.private('App.User.' + auth.id)
                    .notification((n) => {
                        // lable it
                        n.broadcasted = true;

                        Store.state.notifications.unshift(n)

                        // give user the new recieved access (so a refresh won't be needed)
                        if (n.type == 'App\\Notifications\\BecameModerator') {
                            if (n.data.role == "moderator") {
                                Store.state.moderatorAt.push(n.data.category.id)
                            } else if (n.data.role == "administrator") {
                                Store.state.administratorAt.push(n.data.category.id)
                            }

                            Store.state.moderatingAt.push(n.data.category.id)
                            Store.state.moderatingCategories.push(n.data.category)
                        }

                        // Sending web notifications to user's OS (only if browser tab is not active)
                        if (document.hidden == true) {
                            let body = n.data.body
                            let link = n.data.url
                            let avatar = n.data.avatar

                            let title = 'Now Notification'

                            if (n.type == 'App\\Notifications\\CommentReplied') {
                                title = 'New Reply'
                            } else if (n.type == 'App\\Notifications\\SubmissionReplied') {
                                title = 'New Comment'
                            } else if (n.type == 'App\\Notifications\\BecameModerator') {
                                title = 'Now Moderating'
                            } else if (n.type == 'App\\Notifications\\CommentReported') {
                                title = 'New Report'
                            } else if (n.type == 'App\\Notifications\\SubmissionReported') {
                                title = 'New Report'
                            }

                            const data = {
                                title: title,
                                body: body,
                                url: link,
                                icon: avatar
                            }

                            this.$eventHub.$emit('push-notification', data)
                        }
                    })
            },
        },
    }
</script>
