<template>
    <transition name="fade">
        <div class="no-more-to-load user-select" v-show="visible">
            <h3 v-text="text"></h3>
        </div>
	</transition>
</template>

<script>
    export default {
        data: function () {
            return {
                visible: true
            }
        },

        props: ['text']
    };
</script>

<style>
    .no-more-to-load {
        text-align: center;
        color: #777;
        padding-bottom: 1em;
        opacity: .4;
    }
</style>
