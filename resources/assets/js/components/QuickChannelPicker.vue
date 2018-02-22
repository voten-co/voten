<template>
	<div class="form-wrapper quick-box user-select"
	     v-show="!nothingFound">
		<div class="flex-space guide">
			<div class="left">
				Channels:
				<i class="el-icon-loading"
				   v-if="loading"></i>
			</div>

			<div class="right">
				Press
				<kbd>&#8593;</kbd> and
				<kbd>&#8595;</kbd> to navigate,
				<kbd>enter</kbd> to select, and
				<kbd>esc</kbd> to dismiss.
			</div>
		</div>

		<div v-for="(value, index) in filteredList"
		     class="item"
		     :id="'channel-' + index"
		     :class="{'selected': selectedIndex == index}"
		     :key="value.name"
		     @click="pick(value)">
			<img :src="value.avatar"
			     :alt="'#' + value.name"
			     :title="'#' + value.name">

			<span v-text="'#' + value.name"
			      class="name"></span>
		</div>
	</div>
</template>

<script>
import InputHelpers from '../mixins/InputHelpers';
import Helpers from '../mixins/Helpers';

export default {
    data() {
        return {
            list: [],
            selectedIndex: 0,
            loading: false
        };
    },

    mixins: [InputHelpers, Helpers],

    props: ['message', 'starter', 'textareaid'],

    computed: {
        nothingFound() {
            return this.filteredList.length === 0;
        },

        suggestions() {
            return Store.state.subscribedChannels.concat(
                Store.state.bookmarkedChannels
            );
        },

        filteredList() {
            let self = this;

            return _.uniqBy(this.suggestions, 'name')
                .filter(
                    (item) =>
                        item.name
                            .toLowerCase()
                            .indexOf(self.searched.toLowerCase().trim()) !== -1
                )
                .slice(0, 5);
        },

        /**
         * The string written after the starter character (#). This is used for filtering items.
         *
         * @return string
         */
        searched() {
            let cursorPosition = this.getCursorPositionById(this.textareaid);

            return this.message.substr(
                this.starter + 1,
                cursorPosition - this.starter - 1
            );
        }
    },

    created() {
        this.$eventHub.$on('keyup:up', this.keyupUp);
        this.$eventHub.$on('keyup:down', this.keyupDown);
        this.$eventHub.$on('keyup:enter', this.keyupEnter);
    },

    beforeDestroy() {
        this.$eventHub.$off('keyup:up', this.keyupUp);
        this.$eventHub.$off('keyup:down', this.keyupDown);
        this.$eventHub.$off('keyup:enter', this.keyupEnter);
    },

    watch: {
        searched() {
            this.search();
        }
    },

    methods: {
        search: _.debounce(() => {
            if (!this.searched.trim()) return;
            this.loading = true;

            axios
                .get('/search', {
                    params: {
                        type: 'Channels',
                        keyword: this.searched
                    }
                })
                .then((response) => {
                    this.list = response.data.data;
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = true;
                });
        }, 600),

        keyupUp() {
            this.selectedIndex--;

            if (this.selectedIndex < 0) {
                this.selectedIndex = this.filteredList.length - 1;
            }
        },

        keyupDown() {
            this.selectedIndex++;

            if (this.selectedIndex > this.filteredList.length - 1) {
                this.selectedIndex = 0;
            }
        },

        keyupEnter() {
            this.pick(this.filteredList[this.selectedIndex]);
        },

        close() {
            this.$emit('close');
        },

        pick(channel) {
            this.$emit(
                'pick',
                '#' + channel.name,
                this.starter,
                this.searched.length
            );

            this.close();
        }
    }
};
</script>
