<template>
	<transition name="fade">
		<section class="user-select category-suggestion-wrapper" v-if="visible">
			<div class="flex-space">
				<h3 class="category-suggestion-wrapper-title">Recommended channel:</h3>

				<button class="v-button v-button--green v-button-small" @click="subscribe">
					Subscribe
				</button>
			</div>

			<div class="category-suggestion">
				<div class="avatar">
					<router-link :to="'/c/' + category.name">
						<img :src="category.avatar" :alt="category.name">
					</router-link>
				</div>

				<div class="flex1">
					<h2 class="word-break">
						<router-link :to="'/c/' + category.name">
							<i class="v-icon v-channel" aria-hidden="true"></i>{{ category.name }}
						</router-link>
					</h2>

					<p>
						{{ category.description }}
					</p>
				</div>
			</div>
		</section>
	</transition>
</template>

<script>
	import Helpers from '../mixins/Helpers';

    export default {
    	mixins: [Helpers],

        data: function () {
            return {
            	visible: false,
                category: [],
                Store
            }
        },

        created () {
            this.getCategory();
        },

        methods: {
            getCategory() {
            	axios.get(this.authUrl('suggested-category')).then((response) => {
					if (response.data != null) {
						this.visible = true
						this.category = response.data
					}
            	});
            },

            subscribe() {
            	if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

            	Store.subscribedCategories.push(this.category);

            	axios.post('/subscribe', {
	            	category_id: this.category.id
	            });

	            this.visible = false;
            }
        }
    };
</script>
