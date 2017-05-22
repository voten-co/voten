<template>
	<transition name="fade">
		<section class="user-select category-suggestion-wrapper" v-if="visible">
			<h3 class="category-suggestion-wrapper-title">Recommended channel for you:</h3>

			<div class="category-suggestion">
				<div class="avatar">
					<router-link :to="'/c/' + category.name">
						<img :src="category.avatar" :alt="category.name">
					</router-link>
				</div>

				<div class="flex1">
					<div class="flex-space">
						<h2 class="word-break">
							<router-link :to="'/c/' + category.name">
								<i class="v-icon v-channel" aria-hidden="true"></i>{{ category.name }}
							</router-link>
						</h2>

						<button class="v-button v-button--green v-button-small" @click="subscribe">
							Subscribe
						</button>
					</div>

					<p>
						{{ category.description }}
					</p>
				</div>
			</div>
		</section>
	</transition>
</template>

<script>
    export default {
        data: function () {
            return {
            	visible: false,
                category: [],
                Store
            }
        },

        created () {
            this.getCategory()
        },

        methods: {
            getCategory () {
            	axios.post('/suggested-category').then((response) => {
					if (response.data != "null") {
						this.visible = true
						this.category = response.data
					}
            	})
            },

            subscribe () {
            	Store.subscribedCategories.push(this.category)

            	axios.post('/subscribe', {
	            	category_id: this.category.id
	            })

	            this.visible = false
            }
        }
    };
</script>
