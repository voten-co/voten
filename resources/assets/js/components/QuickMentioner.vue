<template>
	<div class="form-wrapper quick-box user-select"
	     v-show="!nothingFound">
		<div class="flex-space guide">
			<div class="left">
				Mention Users:
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
		     :id="'user-' + index"
		     :class="{'selected': selectedIndex == index}"
		     :key="value.username"
		     @click="pick(value)">
			<img :src="value.avatar"
			     :alt="'@' + value.username"
			     :title="'@' + value.username">

			<span v-text="'@' + value.username"
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

    props: ['message', 'starter', 'suggestions', 'textareaid'],

    computed: {
        nothingFound() {
            return this.filteredList.length === 0;
        },

        filteredList() {
            let self = this;

            return _.uniqBy(this.suggestions.concat(this.list), 'username')
                .filter(
                    (item) =>
                        item.username
                            .toLowerCase()
                            .indexOf(self.searched.toLowerCase().trim()) !== -1
                )
                .slice(0, 5);
        },

        /**
         * The string written after the starter character (@). This is used for filtering items.
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
        search: _.debounce(function() {
            if (!this.searched.trim()) return;
            this.loading = true;

            axios
                .get('/search', {
                    params: {
                        type: 'Users',
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

        pick(user) {
            this.$emit(
                'pick',
                '@' + user.username,
                this.starter,
                this.searched.length
            );

            this.close();
        }
    }
};
</script>
