export default {
    computed: {
        currentFont() {
            return Store.settings.font;
        }
    },

    watch: {
        'Store.settings.font'() {
            this.setFont(this.currentFont);
        }
    },

    mounted() {
        this.$nextTick(function() {
            this.setFont(this.currentFont);
        });
    },

    methods: {
        setFont(font) {
            document.body.style.fontFamily = font;
        }
    }
};
