<template>
    <div class="emojiPicker-wrapper">
        <div class="ui icon input flex-search">
            <input type="text" placeholder="Search Emojis..." v-model="searched">
            <i class="v-icon v-search search icon"></i>
        </div>

        <div class="emoji-list">
            <div class="align-left" v-if="filteredHistory.length">
                <div class="flex-space margin-bottom-1">
                    <h3 class="emoji-list-title">
                        Frequently Used
                    </h3>

                    <h4 class="emoji-list-clear" @click="clear">
                        Clear
                    </h4>
                </div>

                <img v-for="e in filteredHistory" class="emojione" :alt="e.code_decimal" :title="e.shortname" @click="pick(e)"
                :src="'https://cdn.jsdelivr.net/emojione/assets/png/' + e.unicode + '.png?v=2.2.7'">
            </div>

            <hr v-show="filteredHistory.length">

            <img v-for="emoji in filteredEmojiList" class="emojione" :alt="emoji.code_decimal" :title="emoji.shortname" @click="pick(emoji)"
            :src="'https://cdn.jsdelivr.net/emojione/assets/png/' + emoji.unicode + '.png?v=2.2.7'">
        </div>
    </div>
</template>

<script>
    import LocalStorage from '../mixins/LocalStorage'

    export default {
        components: {},

        mixins: [LocalStorage],

        data () {
            return {
                list: [],
                history: [],
                searched: ''
            }
        },

        props: {
            //
        },

        computed: {
            filteredHistory() {
                let self = this

                return this.history.filter(function (item) {
					return item.name.indexOf(self.searched.toLowerCase()) !== -1 || item.shortname.indexOf(self.searched.toLowerCase()) !== -1
				})
            },

            filteredEmojiList() {
                let self = this

                return this.list.filter(function (item) {
					return item.name.indexOf(self.searched.toLowerCase()) !== -1 || item.shortname.indexOf(self.searched.toLowerCase()) !== -1
				})
            },
        },

        created () {
            this.getEmojiList()
            this.getHistory()
        },

        mounted () {
            //
        },

        methods: {
            getEmojiList(){
                if (this.isSetLS('emoji-list')) {
                    this.list = this.getLS('emoji-list')
                    return
                }

                axios.get('/emoji-list').then((response) => {
                    // this.list = response.data

                    let temp = []
                    response.data.forEach(function(element, index, self) {
                        temp.push(element)
                    })

                    this.list = temp

                    this.putLS('emoji-list', temp)
                })
            },

            getHistory(){
                if (this.isSetLS('emoji-history')) {
                    this.history = this.getLS('emoji-history')
                }
            },

            clear() {
                this.history = []

                this.forgetLS('emoji-history')
            },

            pick(e) {
                this.$emit('emoji', e.shortname)

                this.searched = ''


                // if the history exists in the localStorage then update it
                if (this.history.length) {
                    this.history = this.history.filter(function(item) {
                        return item.shortname !== e.shortname
                    })

                    this.history.unshift(e)

                    this.putLS('emoji-history', this.history)

                    return
                }

                // otherwise create it for the first time
                this.history.unshift(e)

                this.putLS('emoji-history', this.history)
            }
        }
    };
</script>
