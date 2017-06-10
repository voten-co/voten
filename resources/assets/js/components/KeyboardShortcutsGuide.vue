<template>
    <div class="v-modal-small user-select" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box" v-on-clickaway="close">
            <div class="flex1">
                <h2 class="align-center">
                    Keyboard Shortcuts
                </h2>

                <table class="table">
				  	<thead>
					    <tr>
							<th>Key</th>
							<th class="half-width">Action</th>
					    </tr>
				  	</thead>

				  	<tbody>
			  			<tr>
					      	<td>
					      		<kbd>Alt</kbd> + <kbd>S</kbd>
					      	</td>

					      	<td>
					      		New Submission
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      		<kbd>Alt</kbd> + <kbd>C</kbd>
					      	</td>

					      	<td>
					      		New Channel
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      		<kbd>S</kbd>
					      	</td>

					      	<td>
					      		Hide/Show Sidebar
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      		<kbd>N</kbd>
					      	</td>

					      	<td>
					      		Notifications
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      		<kbd>M</kbd>
					      	</td>

					      	<td>
					      		Messages
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      		<kbd>/</kbd>
					      	</td>

					      	<td>
					      		Search
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      		<kbd>B</kbd>
					      	</td>

					      	<td>
					      		Bookmarks
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      		<kbd>H</kbd>
					      	</td>

					      	<td>
					      		Home
					      	</td>
					    </tr>

                        <tr>
                            <td>
                                <kbd>P</kbd>
                            </td>
                            <td>
                                Profile
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <kbd>R</kbd>
                            </td>
                            <td>
                                Refresh Submissions
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <kbd>Shift</kbd> + <kbd>/</kbd>
                            </td>
                            <td>
                                Show these shortcuts
                            </td>
                        </tr>
				  	</tbody>
				</table>



                <button type="button" class="v-button v-button--green v-button--block"
                    data-toggle="tooltip" data-placement="bottom" title="Close (esc)"
                    @click="close">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import { mixin as clickaway } from 'vue-clickaway';

	export default {
		props: ['sidebar'],

        mixins: [ clickaway ],

	    methods: {
	    	close () {
	    		this.$eventHub.$emit('close')
	    	},
	    },
	}
</script>
