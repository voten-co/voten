<template>
	<div class="form-wrapper quick-box user-select"
	     v-show="!nothingFound">
		<div class="flex-space guide">
			<div class="left">
				Emojis:
			</div>

			<div class="right">
				Press
				<kbd>&#8593;</kbd> and
				<kbd>&#8595;</kbd> to navigate,
				<kbd>enter</kbd> to select, and
				<kbd>esc</kbd> to dismiss.
			</div>
		</div>

		<div v-for="(value, index) in filteredEmojiList"
		     class="item"
		     :id="'emoji-' + index"
		     :class="{'selected': selectedIndex == index}"
		     :key="value.shortname"
		     @click="pick(value)">
			<img :src="'https://cdn.jsdelivr.net/emojione/assets/png/' + value.unicode + '.png?v=2.2.7'"
			     :alt="value.code_decimal"
			     :title="value.shortname">

			<span v-text="value.shortname"
			      class="name"></span>
		</div>
	</div>
</template>

<script>
import InputHelpers from '../mixins/InputHelpers';

export default {
    data() {
        return {
            list: [],
            history: [],
            selectedIndex: 0
        };
    },

    mixins: [InputHelpers],

    props: ['message', 'starter', 'textareaid'],

    computed: {
        nothingFound() {
            return this.filteredEmojiList.length === 0;
        },

        filteredEmojiList() {
            let self = this;

            if (!self.searched.length && this.history.length > 0) {
                return this.history.slice(0, 5);
            }

            return this.list
                .filter(
                    (item) =>
                        item.shortname.indexOf(
                            self.searched.toLowerCase().trim()
                        ) !== -1
                )
                .slice(0, 5);
        },

        /**
         * The string written after the starter character (:). This is used for filtering items.
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
        this.getEmojiList();
        this.getHistory();

        this.$eventHub.$on('keyup:up', this.keyupUp);
        this.$eventHub.$on('keyup:down', this.keyupDown);
        this.$eventHub.$on('keyup:enter', this.keyupEnter);
    },

    beforeDestroy() {
        this.$eventHub.$off('keyup:up', this.keyupUp);
        this.$eventHub.$off('keyup:down', this.keyupDown);
        this.$eventHub.$off('keyup:enter', this.keyupEnter);
    },

    methods: {
        getEmojiList() {
            if (Vue.isSetLS('emoji-list')) {
                this.list = Vue.getLS('emoji-list');
                return;
            }

            axios.get('/emojis').then((response) => {
                let temp = [];

                response.data.forEach((element, index, self) => {
                    temp.push(element);
                });

                this.list = temp;

                Vue.putLS('emoji-list', temp);
            });
        },

        getHistory() {
            if (Vue.isSetLS('emoji-history')) {
                this.history = Vue.getLS('emoji-history');
            }
        },

        keyupUp() {
            this.selectedIndex--;

            if (this.selectedIndex < 0) {
                this.selectedIndex = this.filteredEmojiList.length - 1;
            }
        },

        keyupDown() {
            this.selectedIndex++;

            if (this.selectedIndex > this.filteredEmojiList.length - 1) {
                this.selectedIndex = 0;
            }
        },

        keyupEnter() {
            this.pick(this.filteredEmojiList[this.selectedIndex]);
        },

        close() {
            this.$emit('close');
        },

        pick(e) {
            this.$emit('pick', e.shortname, this.starter, this.searched.length);

            // if the history exists in the localStorage then update it
            if (this.history.length) {
                this.history = this.history.filter(
                    (item) => item.shortname !== e.shortname
                );

                this.history.unshift(e);

                Vue.putLS('emoji-history', this.history);

                this.close();

                return;
            }

            // otherwise create it for the first time
            this.history.unshift(e);

            Vue.putLS('emoji-history', this.history);

            this.close();
        }
    }
};
</script>
