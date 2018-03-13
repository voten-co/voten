<template>
	<section>
		<loading v-if="loading"></loading>

        <not-found v-if="showNotFound"></not-found>
	</section>
</template>

<script>
import Loading from '../components/Loading.vue';
import NotFound from '../components/NotFound.vue';

export default {
    components: { Loading, NotFound },

    data() {
        return {
            loading: true, 
            showNotFound: false, 
        };
    },

    created() {
        this.getSubmissionById();
    },

    methods: {
        getSubmissionById() {
            axios
                .get(`/submissions`, {
                    params: {
                        id: this.$route.params.id
                    }
                })
                .then((response) => {
                    this.loading = false;

                    this.$router.push(
                        '/c/' +
                            response.data.data.channel_name +
                            '/' +
                            response.data.data.slug
                    );
                })
                .catch((error) => {
                    this.loading = false;

                    if (error.response.status === 404) {
                        this.showNotFound = true; 
                    }
                });
        }
    }
};
</script>
