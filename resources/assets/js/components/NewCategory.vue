<template>
    <section class="container margin-top-5 col-7 user-select" id="new-channel">
        <h1 class="align-center">
            Create your own real-time community
        </h1>

        <div class="v-status v-status--error" v-if="customError">
            {{ customError }}
        </div>

        <div class="form-group">
            <input type="text" class="form-control v-input-big" placeholder="Name..." id="name" v-model="name">

            <small class="text-muted" v-if="!name">Name must be lowercase, with no spaces. They're also not editable so make up your mind before choosing! Examples: gaming, news, pics, modernwarfare2</small>
            <small class="text-muted go-red" v-for="e in errors.name">{{ e }}</small>
        </div>

        <div class="form-group">
            <textarea name="description" rows="3" id="description" class="form-control v-input-big"
            v-model="description" placeholder="A few word to describe your channel..."></textarea>

            <small class="text-muted" v-if="!description">The description field helps users find your channel. The first few words matter the most!</small>
            <small class="text-muted go-red" v-for="e in errors.description">{{ e }}</small>
        </div>

        <div class="form-group">
            <button type="submit" class="v-button v-button--green btn-block" @click="submit" :disabled="!validates">Create</button>
        </div>
    </section>
</template>

<script>
export default {

    data: function () {
        return {
            name: '',
            description: '',
            nsfw: false,
            errors: [],
            customError: '',
            Store
        }
    },

	mounted: function () {
		this.$nextTick(function () {
			this.$root.autoResize()
		})
	},

	computed: {
		/**
		 * Validates the inputs
		 *
		 * @return Boolean
		 */
		validates () {
			return this.name && this.description
		},
	},


    methods: {
        submit () {
            axios.post( '/channel', {
                name: this.name,
                description: this.description,
                nsfw: this.nsfw
            } ).then((response) => {
                this.errors = []

                // let's add the categoriy_id to the user's moderatingAt and administratorAt
                Store.moderatingAt.push(response.data.id)
                Store.administratorAt.push(response.data.id)
                Store.moderatingCategories.push(response.data)
                Store.subscribedCategories.push(response.data)

                this.$router.push('/c/' + response.data.name + '/mod/settings?created=1')
            }, (response) => {
                if (response.status == 500) {
                    this.customError = response.data
                    this.errors = []
                    return
                }

                this.errors = response.data
            })
        },
    }
}
</script>
