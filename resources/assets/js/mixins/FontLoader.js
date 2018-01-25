export default {
  computed: {
    currentFont() {
      return auth.font;
    }
  },

  watch: {
    "auth.font"() {
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
