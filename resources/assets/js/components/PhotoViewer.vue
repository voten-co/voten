<template>
    <div class="photo-viewer-wrapper user-select">
        <div class="photo-viewer-header">
            <div class="flex-display">
                <div class="info desktop-only">
                    <h3>
                        {{ str_limit(list.title, 40) }}
                    </h3>

                    <small class="go-gray">
                        Submitted by
                        <router-link :to="'/' + '@' + list.owner.username">@{{ list.owner.username }}</router-link>
                        to
                        <router-link :to="'/c/' + list.category_name">#{{ list.category_name }}</router-link>
                        -
                        <router-link :to="'/c/' + list.category_name + '/' + list.slug">
                            {{ date }}
                        </router-link>
                    </small>
                </div>

                <div class="voting-wrapper">
                    <a class="fa-stack" @click="$emit('bookmark')" v-tooltip.bottom="{content: bookmarked ? 'Unbookmark' : 'Bookmark'}">
    					<i class="v-icon h-yellow" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
    				</a>

                    <a class="fa-stack align-right" @click="$emit('upvote')" v-tooltip.bottom="{content: 'Upvote'}">
                        <i class="v-icon v-up-fat" :class="upvoted ? 'go-primary' : 'go-gray'"></i>
                    </a>

                    <div class="detail">
                        {{ points }} Points
                    </div>

                    <a class="fa-stack align-right" @click="$emit('downvote')" v-tooltip.bottom="{content: 'Downvote'}">
                        <i class="v-icon v-down-fat" :class="downvoted ? 'go-red' : 'go-gray'"></i>
                    </a>
                </div>

                <div class="counter-wrapper" v-if="isAlbum">
                    {{ this.counter + 1 }} / {{ photos.length }}
                </div>
            </div>

            <div>
                <i class="v-icon pointer v-cancel margin-right-1" aria-hidden="true" @click="$emit('close')" v-tooltip.bottom="{content: 'Close (esc)'}"></i>
            </div>
        </div>

        <div class="photo-wrapper" v-if="!isAlbum">
            <img :src="list.data.path" :alt="list.title" @click="$emit('close')">
        </div>

        <div id="loading-wrapper" v-if="loading && isAlbum">
            <moon-loader :loading="loading && isAlbum" :size="'50px'" :color="'#777'" class="form-loader"></moon-loader>
        </div>

        <div class="photo-album-wrapper" v-if="isAlbum && !loading">
            <i class="v-icon pointer v-previous gallery-button desktop-only" aria-hidden="true" @click="previous" :class="counter > 0 ? '' : 'display-hidden'"></i>

            <img :src="currentPhoto" :alt="list.title" @click="$emit('close')">

            <i class="v-icon pointer v-next gallery-button desktop-only" aria-hidden="true" @click="next" :class="counter < (photos.length - 1) ? '' : 'display-hidden'"></i>
        </div>

        <div class="flex-space" v-if="!loading && isAlbum">
            <button class="v-button" :class="counter > 0 ? '' : 'display-hidden'" @click="previous">
                Previous
            </button>

            <button class="v-button" :class="counter < (photos.length - 1) ? '' : 'display-hidden'" @click="next">
                Next
            </button>
        </div>
    </div>
</template>

<style media="screen">
    .photo-viewer-wrapper #loading-wrapper {
        position: relative;
        height: 100%;
    }

    .opacity-fade {
        opacity: 0.6;
    }

    .photo-viewer-wrapper .form-loader {
        position: absolute;
        top: 42%;
        left: 50%;
        z-index: 100;
    }

    .form-loader .v-moon1 {
        height: 60px !important;
        width: 60px !important;
    }

    .form-loader .v-moon2 {
        height: 5.28571px !important;
        width: 5.28571px !important;
        border-radius: 100% !important;
        top: 25.8571px !important;
        opacity: 1 !important;
        background-color: #333 !important;
    }

    .form-loader .v-moon3 {
        height: 60px !important;
        width: 60px !important;
        opacity: 0.2 !important;
        border: 5.71429px solid #000 !important;
    }
</style>


<script>

    import MoonLoader from '../components/MoonLoader.vue'

    import Helpers from '../mixins/Helpers'

    export default {
        components: {MoonLoader},

        mixins: [Helpers],

        props: [
            'points', 'upvoted', 'downvoted', 'bookmarked', 'list', 'photoindex'
        ],

        data: function () {
            return {
                loading: false,
                photos: [],
                currentPhoto: null,
                counter: 0
            }
        },

        computed: {
            isAlbum(){
                return this.list.data.album
            },

            date () {
                return moment(this.list.created_at).utc(moment().format("Z")).fromNow()
            },
        },

        created () {
            this.setCounter()

            window.addEventListener('keyup', this.keyup)

            if (this.isAlbum) {
                this.getPhotos()
            }
        },

        methods: {
            setCounter(){
                if (this.photoindex) this.counter = this.photoindex
            },

            /**
             * Catches the event fired for the pressed key, and runs the neccessary methods.
             *
             * @param keyup event
             * @return void
             */
            keyup(event){
                // right
                if (event.keyCode == 39) {
                    this.next()
                }

                // left
                if (event.keyCode == 37) {
                    this.previous()
                }

                // esc
                if (event.keyCode == 27) {
                    this.$emit('close')
                }
            },

            getPhotos: function () {
                this.loading = true

        		axios.get('/submission-photos', {
        			params: {
        				id: this.list.id
        			}
        		} ).then((response) => {
                    this.photos = response.data
                    this.currentPhoto = this.photos[this.counter].path
                    this.loading = false
                });
        	},

            previous(){
                if (this.counter > 0) {
                    this.counter --
                    this.currentPhoto = this.photos[this.counter].path
                }
            },

            next(){
                if (this.counter < (this.photos.length - 1)) {
                    this.counter ++
                    this.currentPhoto = this.photos[this.counter].path
                }
            }
        }
    };
</script>
