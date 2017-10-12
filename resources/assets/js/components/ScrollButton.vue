<template>
    <div class="scroll-button" @click="scrollToTop" v-show="showScrollButton">
        <i class="v-icon v-scroll-up"></i>
    </div>
</template>

<style>
    .scroll-button {
        position: fixed;
        bottom: 1em;
        left: 5.5em;
        cursor: pointer;
        border: 1px solid #d5d5d5;
        border-radius: 4px;
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

        props: [
            'scrollable'
        ], 

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
                document.getElementById(this.scrollable).scrollTop = 0;
            }
        }
    };
</script>
