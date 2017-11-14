<template>
    <div class="vo-modal" id="search">
        <header class="user-select">
            <div class="flex-space padding-desktop-1-mobile-half">
                <div class="flex1">
                    <el-input
                            ref="searchFilter"
                            :placeholder="placeholder"
                            v-model="filter"
                            class="input-with-select"
                            @input="search(filter)"
                            :suffix-icon="loading ? 'el-icon-loading' : 'el-icon-search'"
                    >
                        <el-select
                                v-model="type"
                                slot="prepend"
                                placeholder="Select search type">
                            <el-option v-for="item in types" :label="item" :value="item" :key="item"></el-option>
                        </el-select>
                    </el-input>
                </div>

                <!-- Cancel Button -->
                <div>
                    <el-button type="text" @click="close" class="margin-left-1">
                        Cancel
                    </el-button>
                </div>
            </div>
        </header>


        <div class="middle background-white">
            <!--Algolia Logo -->
            <div class="algolia desktop-only">
                <a href="https://www.algolia.com/referrals/fb684d54/join" target="_blank" rel="nofollow">
                    <img src="/imgs/algolia-powered-by.svg" alt="Search by algolia">
                </a>
            </div>


            <div class="col-7 user-select padding-1">
                <ul class="v-contact-list" v-if="type == 'Categories'">
                    <category-search-item v-for="category in categories" :list="category"
                                          :key="category.id"></category-search-item>
                </ul>

                <h1 class="align-center" v-if="noCategories && filter.trim()">
                    No channel matched your keywords
                </h1>

                <ul class="v-search-items" v-if="type == 'Users'">
                    <user-search-item v-for="user in users" :list="user" :key="user.id"></user-search-item>
                </ul>

                <h1 class="align-center" v-if="noUsers && filter.trim()">
                    No user matched your keywords
                </h1>

                <div v-if="type == 'Submissions'">
                    <submission v-for="submission in submissions" :list="submission" :key="submission.id"></submission>

                    <h1 class="align-center" v-if="noSubmissions && filter.trim()">
                        No submission matched your keywords
                    </h1>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CategorySearchItem from '../components/CategorySearchItem.vue';
    import UserSearchItem from '../components/UserSearchItem.vue';
    import Submission from '../components/Submission.vue';
    import Helpers from '../mixins/Helpers';

    export default {
        mixins: [Helpers],

        components: {
            CategorySearchItem,
            Submission,
            UserSearchItem
        },

        data() {
            return {
                Store,
                filter: '',
                result: [],
                loading: false,
                categories: [],
                users: [],
                submissions: [],
                type: 'Categories',
                types: [
                    'Categories', 'Submissions', 'Users'
                ]
            }
        },

        mounted() {
            this.$nextTick(function () {
                this.$refs.searchFilter.$refs.input.focus();

                if (this.$route.query.search) {
                    this.filter = this.$route.query.search;
                    this.type = 'Submissions';
                    this.search();
                }
            })
        },

        computed: {
            noSubmissions() {
                return this.type == 'Submissions' && this.submissions.length == 0 && !this.loading;
            },

            noCategories() {
                return this.type == 'Categories' && this.categories.length == 0 && !this.loading;
            },

            noUsers() {
                return this.type == 'Users' && this.users.length == 0 && !this.loading;
            },

            placeholder() {
                if (this.type == 'Categories') {
                    return 'Search by #name or description...';
                }
                if (this.type == 'Users') {
                    return 'Search by @username or name...';
                }
                if (this.type == 'Submissions') {
                    return 'Search by title...';
                }

                return 'Search...';
            }
        },

        watch: {
            'type'() {
                this.search();
            },
        },

        methods: {
            search: _.debounce(function () {
                if (!this.filter.trim()) return
                this.loading = true;

                axios.get('/search', {
                    params: {
                        type: this.type,
                        searched: this.filter,
                    }
                }).then((response) => {
                    if (this.type == 'Categories') {
                        this.categories = response.data;
                    }
                    if (this.type == 'Users') {
                        this.users = response.data;
                    }
                    if (this.type == 'Submissions') {
                        this.submissions = response.data;
                    }
                    this.loading = false;
                }).catch(() => {
                    this.loading = false;
                });
            }, 600),

            close() {
                this.$eventHub.$emit('close');
            }
        }
    }
</script>

<style>
    .algolia {
        opacity: .6;
        position: fixed;
        bottom: 1em;
        right: 2em;
    }

    .input-with-select .el-input {
        width: 130px;
    }

    .input-with-select .el-input-group__prepend {
        background-color: #fff;
    }
</style>