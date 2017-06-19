<template>
	<div class="announcement-wrapper">
		<div class="announcement" v-for="(value, index) in announcements" :key="value.id" :class="background(index)">
			<markdown :text="value.body" v-if="value.body"></markdown>

			<i class="v-icon block-before v-cancel pointer" aria-hidden="true" @click="close(value.id)"
			data-toggle="tooltip" data-placement="bottom" title="Close"></i>
		</div>
	</div>
</template>

<script>
    import LocalStorage from '../mixins/LocalStorage';
    import Markdown from '../components/Markdown.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        components: {
        	Markdown
        },

        mixins: [Helpers, LocalStorage],

        data () {
            return {
                announcements: []
            }
        },

        created () {
            this.fetch();
        },

        mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip();
			})
		},

        methods: {
        	/**
        	 * computed the correct background color class (and no! we can not use a computed property instead. take another look, you'll see why)
        	 *
        	 * @return string
        	 */
        	background(index) {
        		if (index == 0 || index == 3) {
        			return 'primary-background';
        		}

        		if (index == 1 || index == 4) {
        			return 'green-background';
        		}

        		if (index == 2 || index == 5) {
        			return 'red-background';
        		}

        	    return 'primary-background';
        	},

        	/**
        	 * Close the announcement and send a ajax request so we won't show current user the same announmcent twice.
        	 * Also set a flag for it in LocalStorage to do some checkings (this won't be needed for authinticated
        	 * users since we're doing the checking in server-side but for non-authinticated users we need it.)
        	 *
        	 * @return void
        	 */
            close(id) {
                this.announcements = this.announcements.filter(function (item) {
                  	return item.id != id;
                });

                axios.post('/announcement/seen', {
                	announcement_id: id
                });
            },

            /**
             * Fetches the announcement
             *
             * @return void
             */
            fetch() {
                axios.get(this.authUrl('announcement')).then((response) => {
                	this.announcements = response.data;
                });
            },
        }
    };
</script>

<style>
	.announcement {
		display: flex;
	    justify-content: space-between;
	    padding: 1em 2em;
	    align-items: center;
	    border-bottom: 1px solid #d8e2e7;
	    color: #fff;
	}

	.announcement a {
		color: #fff;
	}

	.announcement .v-cancel{
		flex-basis: 65px;
	    display: flex;
	    justify-content: flex-end;
	}
</style>