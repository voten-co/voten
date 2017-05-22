export default {
    components: {},

    data: function () {
        return {
            Store
        }
    },

    props: {
        //
    },

    computed: {
        //
    },

    created () {
        //
    },

    mounted () {
        //
    },

    methods: {
        /**
         * simulates Laravel's str_limit in JS
         *
         * @param string str
         * @param integer length
         * @return string
         */
        str_limit(str, length)
        {
            if (str.length > length)
                return str = str.substring(0, length) + '...'
            return str
        },

        /**
         * determines if the user is typing in either an input or textarea
         *
         * @return boolean
         */
        isTyping (event)
        {
            return event.target.tagName.toLowerCase() === 'textarea' || event.target.tagName.toLowerCase() === 'input'
        },
    }
};
