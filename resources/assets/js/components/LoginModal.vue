<template>
    <div class="v-modal-small" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box v-modal-small-box--light" v-on-clickaway="close">
            <div class="flex1">
				<h2>
					Have an account?
				</h2>

				<form action="/login" method="POST" class="align-left">

					<div class="form-group">
						<input type="text" class="form-control" id="username" name="username" placeholder="Username..." required>
					</div>

					<div class="form-group">
						<input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
					</div>

	                <div class="form-group ui form">
		                <div class="inline field">
		                    <div class="ui toggle checkbox">
		                        <input type="checkbox" class="hidden" name="remember">
		                        <label>Remember Me</label>
		                    </div>
		                </div>
		            </div>

					<div class="flex-space">
						<a class="v-button" href="/password/reset">Forgot my password</a>
						<button class="v-button v-button--green">Sign In</button>
					</div>
				</form>
            </div>
        </div>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway';
import Helpers from '../mixins/Helpers';

export default {
	props: ['sidebar'],

    mixins: [ clickaway, Helpers ],

    data () {
        return {

        }
    },

    created: function () {

    },

    methods: {
    	/**
    	 * Fires the 'close' event which causes all the modals to be closed.
    	 *
    	 * @return void
    	 */
    	close() {
    		this.$eventHub.$emit('close')
    	},
    },
}

</script>
