const LocalStorage = {
  install(Vue, options) {
    /**
     * put the data into the localStorage
     *
     * @param string key
     * @param mixed value
     * @return void
     */
    (Vue.putLS = function(key, value) {
      localStorage.setItem(key, JSON.stringify(value));
    }),
      /**
       * fetches the data from the localStorage
       *
       * @param string key
       * @return object
       */
      (Vue.getLS = function(key) {
        let result = JSON.parse(localStorage.getItem(key));

        if (result === null) {
          return [];
        }

        return result;
      }),
      /**
       * is the value set into the local storage
       *
       * @param string key
       * @return boolean
       */
      (Vue.isSetLS = function(key) {
        return key in localStorage ? true : false;
      }),
      /**
       * Clear all the data in LocalStorage.
       *
       * @return void
       */
      (Vue.clearLS = function() {
        localStorage.clear();
      }),
      (Vue.forgetLS = function(key) {
        localStorage.removeItem(key);
      });
  }
};

export default LocalStorage;
