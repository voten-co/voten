<template>
    <div>
        <div v-if="showBigThumbnail" class="align-center relative pointer" @click="$emit('play-gif')">
            <img v-bind:src="submission.data.thumbnail_path" v-bind:alt="submission.title" class="big-thumbnail" />

            <span class="play-gif">
                <i class="v-icon v-gif-1"></i>
            </span>
        </div>

        <div class="link-list-info">
            <span class="submission-img-title">
                <img v-bind:src="submission.data.thumbnail_path" v-bind:alt="submission.title"
                v-if="showSmallThumbnail" class="small-thumbnail zoom-in" @click="$emit('play-gif')"/>

                <router-link :to="'/c/' + submission.category_name + '/' + submission.slug" class="flex-space v-ultra-bold">
                    {{ submission.title }}
                </router-link>
            </span>
        </div>
    </div>
</template>

<script>
    export default {
        components: {},

        mixins: [],

        data () {
            return {
                auth
            }
        },

        props: {
        	nsfw: {}, submission: {},
            full: {
                type: Boolean,
                default: false,
            },
        },

        computed: {
            showBigThumbnail(){
    			if (this.full) return true

    			if (this.nsfw) return false

    			return ! auth.submission_small_thumbnail
    		},

    		showSmallThumbnail(){
    			return ! this.showBigThumbnail && !this.nsfw
    		}
        },

        created () {
            //
        },

        mounted () {
            //
        },

        methods: {
            //
        }
    };
</script>
