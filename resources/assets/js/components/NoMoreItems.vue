<template>
    <transition name="fade">
        <div class="no-more-to-load user-select" v-show="visible">
            <h3 v-text="text"></h3>
        </div>
	</transition>
</template>

<script>
export default {
    data: function() {
        return {
            visible: true
        };
    },

    props: ['text']
};
</script>
