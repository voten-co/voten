export default {
  methods: {
    /* --------------------------------------------------------------------- */
    /* ---------------------------- Local Storage -------------------------- */
    /* --------------------------------------------------------------------- */

    /**
         * put the data into the localStorage
         *
         * @param string key
         * @param mixed value
         * @return void
         */
    putLS(key, value) {
      localStorage.setItem(auth.username + ":" + key, JSON.stringify(value));
    },

    /**
         * fetches the data from the localStorage
         *
         * @param string key
         * @return object
         */
    getLS(key) {
      let result = JSON.parse(localStorage.getItem(auth.username + ":" + key));

      if (result === null) {
        return [];
      }

      return result;
    },

    /**
         * is the value set into the local storage
         *
         * @param string key
         * @return boolean
         */
    isSetLS(key) {
      let prefixedKey = auth.username + ":" + key;

      if (prefixedKey in localStorage) {
        return true;
      }

      return false;
    },

    forgetLS(key) {
      let prefixedKey = auth.username + ":" + key;

      localStorage.removeItem(prefixedKey);
    },

    /* --------------------------------------------------------------------- */
    /* --------------------------- Session Storage ------------------------- */
    /* --------------------------------------------------------------------- */

    /**
         * put the data into the sessionStorage
         *
         * @param string key
         * @param mixed value
         * @return void
         */
    putSS(key, value) {
      sessionStorage.setItem(auth.username + ":" + key, JSON.stringify(value));
    },

    /**
         * fetches the data from the sessionStorage
         *
         * @param string key
         * @return object
         */
    getSS(key) {
      let result = JSON.parse(
        sessionStorage.getItem(auth.username + ":" + key)
      );

      if (result === null) {
        return [];
      }

      return result;
    },

    /**
         * is the value set into the local storage
         *
         * @param string key
         * @return boolean
         */
    isSetSS(key) {
      let prefixedKey = auth.username + ":" + key;

      if (prefixedKey in sessionStorage) {
        return true;
      }

      return false;
    },

    forgetSS(key) {
      let prefixedKey = auth.username + ":" + key;

      sessionStorage.removeItem(prefixedKey);
    }
  }
};
