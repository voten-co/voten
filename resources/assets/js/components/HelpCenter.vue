<template>
	<section class="container margin-top-5 col-7">
		<div class="align-center margin-bottom-1">
			<router-link to="/help">
				<help-icon width="150" height="150"></help-icon>
			</router-link>
		</div>

		<h1 class="margin-top-bottom-1 align-center user-select">
			Got a question? Let's see if we have an answer for it!
		</h1>

		<div class="ui massive icon input flex-search margin-top-bottom-1">
			<input type="text" placeholder="Search questions..." v-model="filter" v-on:input="search(filter)">
			<i class="v-icon v-search search icon"></i>
		</div>

		<loading v-show="loading"></loading>

		<div class="no-content-wrapper user-select" v-if="noContent && filter.trim().length">
			<i class="v-icon v-sad oops-icon" aria-hidden="true"></i>

			<h1>
				No results were found for your search. Ask
				<router-link to="/c/VotenSupport">#VotenSupport</router-link>
				for an answer.
			</h1>
		</div>

		<ul v-if="filter.trim()">
			<help-item :list="item" v-for="item in items" :key="item.id"></help-item>
		</ul>

		<div v-if="!filter.trim()">
			<h2 class="user-select">
				Most common questions:
			</h2>

			<ul>
				<help-item :list="item" v-for="item in commonQuestions" :key="item.id"></help-item>
			</ul>


			<h2 class="user-select">
				Recently added questions:
			</h2>

			<ul>
				<help-item :list="item" v-for="item in recentQuestions" :key="item.id"></help-item>
			</ul>
		</div>
	</section>
</template>

<script>
    import HelpItem from './HelpItem.vue';
    import HelpIcon from './Icons/Help.vue';
    import Loading from './Loading.vue';

    export default {

        data: function () {
            return {
                filter: '',
                items: [],
                commonQuestions: [],
                recentQuestions: [],
				loading: true,
                noContent: false
            }
        },

        components: {
            HelpItem,
            HelpIcon,
            Loading
        },

        created: function () {
            this.getItems();
        },

        methods: {
        	getItems: function () {
                // if landed on help center
                if (preload.recentQuestions && preload.commonQuestions) {
                    this.recentQuestions = preload.recentQuestions;
                    this.commonQuestions = preload.commonQuestions;
                    this.loading = false;
                    delete preload.recentQuestions;
                    delete preload.commonQuestions;
                    return;
                }

        		axios.get('/help/common-questions').then((response) => {
                    this.commonQuestions = response.data;
                });

                axios.get('/help/recent-questions').then((response) => {
                    this.recentQuestions = response.data;
                });

                this.loading = false;
        	},

            clear() {
        	    this.loading = true;
        	    this.items = [];
                this.noContent = false;
			},

            search: _.debounce(function () {
                if (!this.filter.trim()) return;

                this.clear();

                axios.get('/help-index', {
                    params: {
                        filter: this.filter
                    }
                }).then((response) => {
                    this.items = response.data;

                    this.loading = false;

                    if (this.items.length < 1) {
                        this.noContent = true;
					}
                });
            }, 600),
        },
    }
</script>
