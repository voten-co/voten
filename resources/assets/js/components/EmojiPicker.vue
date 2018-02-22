<template>
	<div class="emojiPicker-wrapper">
		<el-input placeholder="Search Emojis..."
		          prefix-icon="el-icon-search"
		          size="small"
		          v-model="searched"
		          clearable
		          class="margin-bottom-1"></el-input>

		<div class="emoji-list">
			<div class="align-left"
			     v-if="filteredHistory.length">
				<div class="flex-space margin-bottom-1">
					<h3 class="emoji-list-title">
						Frequently Used
					</h3>

					<h4 class="emoji-list-clear"
					    @click="clear">
						Clear
					</h4>
				</div>

				<section>
					<span v-for="emoji in filteredHistory"
					      :key="emoji.shortname"
					      @click="pick(emoji)"
					      class="item">
						<img class="emojione"
						     :alt="emoji.code_decimal"
						     :title="emoji.shortname"
						     :src="'https://cdn.jsdelivr.net/emojione/assets/png/' + emoji.unicode + '.png?v=2.2.7'">
					</span>
				</section>
			</div>

			<hr v-show="filteredHistory.length">

			<section>
				<span v-for="emoji in filteredEmojiList"
				      :key="emoji.shortname"
				      @click="pick(emoji)"
				      class="item">
					<img class="emojione"
					     :alt="emoji.code_decimal"
					     :title="emoji.shortname"
					     :src="'https://cdn.jsdelivr.net/emojione/assets/png/' + emoji.unicode + '.png?v=2.2.7'">
				</span>
			</section>
		</div>
	</div>
</template>

<script>
import InputHelpers from '../mixins/InputHelpers';

export default {
    mixins: [InputHelpers],

    data() {
        return {
            list: [],
            history: [],
            searched: ''
        };
    },

    props: ['textareaid'],

    computed: {
        filteredHistory() {
            let self = this;

            return this.history.filter(
                (item) =>
                    item.name.indexOf(self.searched.toLowerCase()) !== -1 ||
                    item.shortname.indexOf(self.searched.toLowerCase()) !== -1
            );
        },

        filteredEmojiList() {
            let self = this;

            return this.list.filter(
                (item) =>
                    item.name.indexOf(self.searched.toLowerCase()) !== -1 ||
                    item.shortname.indexOf(self.searched.toLowerCase()) !== -1
            );
        }
    },

    created() {
        this.getEmojiList();
        this.getHistory();
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

        clear() {
            this.history = [];
            Vue.forgetLS('emoji-history');
        },

        pick(e) {
            let cursorPosition = this.getCursorPositionById(this.textareaid);

            this.$emit('pick', e.shortname, cursorPosition, 0);

            this.searched = '';

            // if the history exists in the localStorage then update it
            if (this.history.length) {
                this.history = this.history.filter(
                    (item) => item.shortname !== e.shortname
                );

                this.history.unshift(e);

                Vue.putLS('emoji-history', this.history);

                return;
            }

            // otherwise create it for the first time
            this.history.unshift(e);

            Vue.putLS('emoji-history', this.history);
        }
    }
};
</script>
