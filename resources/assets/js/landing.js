require('./bootstrap')

if (document.querySelector('#landing')) {
	new Vue({
	    mounted: function() {
	        this.$nextTick(function() {
	            this.loadCheckBox();
	        })
	    },

	    methods: {
	        /**
	         * Loads the Semantic UI's CheckBox component
	         *
	         * @return void
	         */
	        loadCheckBox() {
	            $('.ui.checkbox').checkbox();
	        },
	    },
	}).$mount('#landing');
}
