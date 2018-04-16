<template>
	<div class="announcement-wrapper"
	     v-show="show">
		<transition-group name="el-zoom-in-bottom">
			<div class="announcement"
			     v-for="(value, index) in announcements"
			     :key="value.id">
				<markdown :text="value.content"
				          v-if="value.content"></markdown>

				<i class="v-icon block-before v-cancel pointer"
				   aria-hidden="true"
				   @click="close(value.id)"></i>
			</div>
		</transition-group>
	</div>
</template>

<script>
import Markdown from '../components/Markdown.vue';
import Helpers from '../mixins/Helpers';

export default {
    components: {
        Markdown
    },

    mixins: [Helpers],

    data() {
        return {
            announcements: []
        };
    },

    created() {
        this.get();
    },

    computed: {
        show() {
            return this.$route.name !== 'discover-channels';
        }
    },

    methods: {
        /**
         * Close the announcement and send a ajax request so we won't show current user the same announmcent twice.
         * Also set a flag for it in LocalStorage to do some checkings (this won't be needed for authinticated
         * users since we're doing the checking in server-side but for non-authinticated users we need it.)
         *
         * @return void
         */
        close(id) {
            this.announcements = this.announcements.filter(item => item.id !== id);

            axios.post(`/announcements/${id}/seen`);
        },

        /**
         * Fetches the announcement
         *
         * @return void
         */
        get() {
            axios.get('/announcements').then(response => {
                this.announcements = response.data.data;
            });
        }
    }
};
</script>


<style lang="scss">
.announcement {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1em 1em;
    border-bottom: 1px solid #d8e2e7;
    position: fixed;
    bottom: 1em;
    z-index: 100;
    width: 70%;
    left: 8%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    background: #fafbff;
    color: #333;
    border-radius: 2px;
    border: 1px solid #f7f7f7;
    border-top: 2px solid #5586d7;
    border-top-right-radius: 2px;
    border-bottom-right-radius: 2px;

    a {
        color: #333;
    }

    .v-cancel {
        flex-basis: 65px;
        display: flex;
        justify-content: center;
    }
}
</style>
