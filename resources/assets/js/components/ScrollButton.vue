<template>
    <div class="scroll-button" @click="scrollToTop" v-show="showScrollButton">
        <i class="v-icon v-scroll-up"></i>
    </div>
</template>

<style>
    .scroll-button {
        position: fixed;
        bottom: 1em;
        right: 1.5em;
        cursor: pointer;
        border: 1px solid #d5d5d5;
        border-radius: 50%;
        background: #fff;
        z-index: 100;
    }

    .scroll-button .v-icon {
        font-size: 25px;
        color: #a5a5a5;
    }
</style>

<script>
    export default {
        data () {
            return {
                showScrollButton: false
            }
        },

        created () {
            this.$eventHub.$on('scrolled-a-bit', this.hide);
            this.$eventHub.$on('scrolled-a-lot', this.show);
        },

        methods: {
            show() {
               this.showScrollButton = true;
            },

            hide() {
               this.showScrollButton = false;
            },

            scrollToTop() {
                document.getElementById('v-content').scrollTop = 0;
            }
        }
    };
</script>
