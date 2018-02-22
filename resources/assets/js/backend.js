require('./bootstrap');

new Vue({
    mounted() {
        this.$nextTick(function() {
            this.loadSemanticTooltip();
        });
    },

    methods: {
        /**
         * Loads Semantic UI's Tooltip component
         *
         * @return void
         */
        loadSemanticTooltip() {
            $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover' });
        }
    }
}).$mount('#backend');
