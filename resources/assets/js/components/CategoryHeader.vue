<template>
<div>
	<transition name="slide-out">
		<div v-bind:style="{ background: coverBackground }" class="category-header-big profile-cover" v-show="showFirstHeader">
			<div class="container user-select full-width">
				<div class="cols-flex">
					<div class="category-header-left">
						<!-- avatar -->
							<div class="profile-avatar avatar-preview" v-if="$route.name == 'category-settings'">
								<button type="button">
									<img v-bind:alt="Store.category.name" v-bind:src="Store.category.avatar" />

									<div class="update">
										<i class="v-icon v-photo" aria-hidden="true"></i>
										Upload photo
									</div>
								</button>

								<input type="file" id="fileUploadFile" @change="passToCropModal">
							</div>

							<div class="profile-avatar" v-else>
								<router-link :to="'/c/' + Store.category.name">
									<img v-bind:src="Store.category.avatar" v-bind:alt="Store.category.name" />
								</router-link>
							</div>
						<!-- end avatar -->
					</div>

					<div class="category-header-middle flex-align-center">
						<p>
							{{ Store.category.description }}
						</p>
					</div>

					<div class="category-header-right">
						<div class="karma">
							<div class="karma-number">
								{{ Store.category.subscribers }}
							</div>

							<div class="karma-text margin-bottom-1">
								Subscribers
							</div>

							<subscribe v-if="!isGuest" subscribed-class="unsubscribe" unsubscribed-class="subscribe"></subscribe>
						</div>
					</div>
				</div>
			</div>
		</div>
	</transition>
	<nav class="nav has-shadow user-select">
	    <div class="container">
			<h1 class="title">
				<i class="v-icon v-channel" aria-hidden="true"></i>{{ Store.category.name }}
			</h1>

	        <div class="nav-left">
	        	<router-link :to="{ path: '/c/' + $route.params.name }" class="nav-item is-tab" :class="{ 'is-active': sort == 'hot' }">
					Hot
				</router-link>

				<router-link :to="{ path: '/c/' + $route.params.name + '?sort=new' }" class="nav-item is-tab" :class="{ 'is-active': sort == 'new' }">
					New
				</router-link>

				<router-link :to="{ path: '/c/' + $route.params.name + '?sort=rising'  }" class="nav-item is-tab" :class="{ 'is-active': sort == 'rising' }">
					Rising
				</router-link>
	        </div>

	        <div class="channel-admin-btn">
				<div class="ui icon top right green pointing dropdown"
					 id="more-button">
					<i class="v-icon v-more" aria-hidden="true"></i>

					<div class="menu">
						<button class="item" @click="emitModerators">
							Moderators
						</button>

						<button class="item" @click="emitRules">
							Rules
						</button>

						<button class="item" @click="block">
							Block
						</button>
					</div>
				</div>

				<i class="v-icon h-yellow pointer" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark go-gray'" @click="bookmark"
				   v-tooltip.bottom="{content: bookmarked ? 'Unbookmark this channel' : 'Bookmark this channel', offset: 8}"
				></i>
            	
				<router-link class="v-button v-button-outline--primary" :to="'/submit?channel=' + $route.params.name" v-if="!isGuest">
					Submit 
				</router-link>

				<router-link :to="{ path: '/c/' + $route.params.name + '/mod' }" class="v-button v-button-outline--green"
					v-if="isModerator"
				>
					Moderation
				</router-link>
	        </div>
	    </div>
	</nav>
</div>
</template>

<script>
import Subscribe from '../components/SubscribeButton.vue'; 
import Helpers from '../mixins/Helpers';

export default {
	mixins: [Helpers],

    components: {Subscribe},

    data: function () {
        return {
    		fileUploadFormData: new FormData(),
        	Store,
        	bookmarked: false, 
			showFirstHeader: true
        }
    },

    created () {
    	this.setBookmarked();
		this.$eventHub.$on('scrolled-to-top', () => {this.showFirstHeader = true}); 
		this.$eventHub.$on('scrolled-a-bit', () => { this.showFirstHeader = false }); 
    },

    watch: {
        '$route' () {
            this.setBookmarked();
        },

        'Store.categoryBookmarks' () {
            this.setBookmarked();
        },
    },

	mounted () {
		this.$nextTick(function () {
        	this.$root.loadSemanticDropdown();
		})
	},

    methods: {
		emitRules(){
			this.$eventHub.$emit('rules');
		},

		emitModerators() {
			this.$eventHub.$emit('moderators');
		},

		block() {
		    axios.post('/category-block', {
		        category_id: Store.category.id
			}).then((response) => {
		        // navigate to home
                this.$router.push('/');
			});
		},

    	/**
		 * Passes the photo to the cropModal to take care of the rest
		 *
		 * @return void
		 */
		passToCropModal (e)
		{
            this.fileUploadFormData.append('photo', e.target.files[0]);

    		axios.post('/upload-temp-avatar', this.fileUploadFormData).then((response) => {
				this.$eventHub.$emit('crop-photo-uploaded', response.data);
            });

    		this.$eventHub.$emit('crop-category-photo');
		},

    	/**
         * Whether or not user has bookmarked the category
         *
         * @return void
         */
        setBookmarked() {
            if (Store.categoryBookmarks.indexOf(Store.category.id) != -1) {
                this.bookmarked = true;
			} else {
                this.bookmarked = false;
			}
		},

        /**
         *  Toggles the category into bookmarks
		 *
		 *  @return void
         */
    	bookmark (category) {
    		if (this.isGuest) {
        		this.mustBeLogin();
        		return;
        	}

    		this.bookmarked = !this.bookmarked

			axios.post('/bookmark-category', {
				id: Store.category.id
			}).then((response) => {
				if (Store.categoryBookmarks.indexOf(Store.category.id) != -1){
                	var index = Store.categoryBookmarks.indexOf(Store.category.id)
                	Store.categoryBookmarks.splice(index, 1)

                	return
                }
				Store.categoryBookmarks.push(Store.category.id)
			})
    	},
    },

    computed: {
    	/**
    	 * the sort of the page
    	 *
    	 * @return mixed
    	 */
    	sort() {
    		if (this.$route.name != 'category-submissions')
    			return null;

    	    if (this.$route.query.sort == 'new')
    	    	return 'new';

    	    if (this.$route.query.sort == 'rising')
    	    	return 'rising';

    	    return 'hot';
    	},

    	date () {
    		return moment(Store.category.created_at).utc(moment().format("MMM Do")).format("MMM Do")
    	},

    	isModerator () {
    		return Store.moderatingAt.indexOf(Store.category.id) != -1
    	},

		coverBackground () {
        	if (Store.category.color == 'Red') {
        		return '#9a4e4e'
        	} else if (Store.category.color == 'Blue') {
        		return '#5487d4'
        	} else if (Store.category.color == 'Dark Blue') {
        		return '#2f3b49'
        	} else if (Store.category.color == 'Dark Green') {
        		return '#507e75'
        	} else if (Store.category.color == 'Bright Green') {
        		return 'rgb(117, 148, 127)'
        	} else if (Store.category.color == 'Purple') {
        		return '#4d4261'
        	} else if (Store.category.color == 'Orange') {
        		return '#ffaf40'
        	} else if (Store.category.color == 'Pink') {
        		return '#ec7daa'
        	} else { // userStore.color == 'Black'
        		return '#424242'
        	}
        }
    }
}
</script>
