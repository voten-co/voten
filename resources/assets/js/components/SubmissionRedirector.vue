<template>
	<section>
		<loading v-if="loading"></loading>
	</section>
</template>

<script>
	import Loading from '../components/Loading.vue'

    export default {
        components: {Loading},

        data: function () {
            return {
                loading: true
            }
        },

        created () {
            this.getSubmission()
        },

        methods: {
            getSubmission() {
				axios.post('/get-submission-by-id', {
				    id: this.$route.params.id
				}).then((response) => {
					this.loading = false

				    this.$router.push('/c/' + response.data.category_name + '/' + response.data.slug)
				}, (response) => {
					this.loading = false;

					if (response.status === 404) {
						this.$router.push('/deleted-submission')
					}
				})
            }
        }
    };
</script>
