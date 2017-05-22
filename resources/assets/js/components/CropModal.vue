<template>
	<div class="v-modal-small user-select" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box">
            <div class="flex1">
                <h2 class="align-center">
                    Please position and size your photo
                </h2>

				<loading v-show="loading"></loading>

				<div v-show="!loading">
					<img v-bind:src="photo" id="crop" v-show="photo">
				</div>

                <div class="margin-top-1">
                	<button type="button" class="v-button v-button--green" :disabled="loading"
	                @click="apply" >
	                    Apply
	                </button>

	                <button type="button" class="v-button v-button--red"
	                    data-toggle="tooltip" data-placement="bottom" title="Close (esc)" :disabled="loading"
	                    @click="close">
	                    Cancel
	                </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import Loading from '../components/Loading.vue'

    export default {
        components: {
        	Loading
        },

        data: function () {
            return {
            	width: 0,
            	height: 0,
            	x: 0,
            	y: 0,
            	photo: '',
                loading: true,
                auth,
                Store,
                clientWidth: 0,
				clientHeight: 0,
				naturalWidth: 0,
				naturalHeight: 0
            }
        },

		props: ['sidebar', 'type'],

        created () {
        	this.$eventHub.$on('crop-photo-uploaded', this.getReady)
        },

        methods: {
        	getReady (uploaded) {
        		this.photo = uploaded

        		this.loading = false

        		let that = this
        		$(document).ready(function() {
				    $("#crop").on('load', function() {
				    	that.clientWidth = $(this).width()
				    	that.clientHeight = $(this).height()

				    	that.naturalWidth = $(this).prop('naturalWidth')
				    	that.naturalHeight = $(this).prop('naturalHeight')
				    });
				});

				this.loadCrop()
        	},

			loadCrop () {
	    		this.$nextTick(function () {
	    			$('#crop').Jcrop({
				        setSelect: [200, 200, 0, 0],
				        aspectRatio: 1 / 1,
				        onSelect: showCords,
				        onChange: showCords
				    });

				    let self = this

				    function showCords (c) {
				        self.width = c.w
				        self.height = c.h
				        self.x = c.x
				        self.y = c.y
				    }
				})
            },

        	apply () {
				this.loading = true
        		// Just two aspects to calculate the real pixel numbers
				let horizontal = this.naturalWidth / this.clientWidth
				let vertical = this.naturalHeight / this.clientHeight

				if (this.type == 'user') {

    				axios.post('/user-avatar-crop', {
	        			photo: this.photo,

	        			width: parseInt(this.width * horizontal),
	        			x: parseInt(this.x * horizontal),

	        			height: parseInt(this.height * vertical),
	        			y: parseInt(this.y * vertical),
	        		}).then((response) => {
	        			auth.avatar = response.data
						this.loading = false
	        			this.close()
		            });

    			} else if (this.type == 'category') {
    				axios.post('/category-avatar-crop', {
	        			photo: this.photo,

	        			name: Store.category.name,

	        			width: parseInt(this.width * horizontal),
	        			x: parseInt(this.x * horizontal),

	        			height: parseInt(this.height * vertical),
	        			y: parseInt(this.y * vertical),
	        		}).then((response) => {
	        			Store.category.avatar = response.data
						this.loading = false
	            		this.close()
		            });

    			}

        	},

            close () {
            	location.reload()
	    		this.$eventHub.$emit('close')
	    	},
        }
    };

</script>
