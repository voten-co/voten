<style lang="scss">
.shade {
    background: #333;
    opacity: 0.85;
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1000;
}

.shade-item {
    z-index: 1001;
}

.tour {
    position: absolute;
    background: #fff;
    width: 400px;
    // min-height: 100px;
    border-radius: 4px;
    border: 2px solid #afb5c2;
    padding: 12px;
    color: #606266;
    line-height: 1.4;
    font-size: 14px;
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
    text-align: left;

    .title {
        font-weight: bold;
        margin: 0;
    }
}
</style>


<template>
<div class="shade-item tour" :style="position">
    <h3 class="title" v-text="title"></h3>

    <div v-html="body"></div>

    <div class="flex-space">
        <div>
            <strong>{{ step }}/{{ items.length }}</strong>	
        </div>

        <div>
            <el-button type="text"
                    size="mini"
                    @click="skip"
                    v-if="!areWeDoneYet"
                    class="margin-right-1">
                Skip the tour
            </el-button>

            <el-button round type="primary"
                    @click="next"
                    v-if="!areWeDoneYet"
                    size="mini">
                Next
            </el-button>
            
            <el-button round type="success"
                    @click="skip"
                    v-if="areWeDoneYet"
                    size="mini">
                Let's do this
            </el-button>
        </div>
    </div>
</div>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    props: ['position'],

    computed: {
        step() {
            return Store.tour.step;
        },

        body() {
            return this.activeTour.body;
        },

        title() {
            return this.activeTour.title;
        },

        items() {
            return Store.tour.items;
        },

        areWeDoneYet() {
            return this.step == this.items.length;
        }
    },

    methods: {
        skip() {
            Store.tour.show = false;
            this.$router.push('/');
        },

        next() {
            if (this.step < this.items.length) {
                Store.tour.step++;

                if (this.activeTour.id == 'os-notifications') {
                    this.askNotificationPermission();
                }

                return;
            }

            this.skip();
        },

        askNotificationPermission() {
            if (this.$route.query.newbie == 1) {
                if ('Notification' in window) {
                    Notification.requestPermission();
                } else {
                    this.$notify({
                        title: 'Too bad!',
                        message:
                            'Your browser does not support desktop notifications.',
                        type: 'warning'
                    });
                }
            }
        }
    }
};
</script>
