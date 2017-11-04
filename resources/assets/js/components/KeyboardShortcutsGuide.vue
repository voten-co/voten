<template>
    <div class="vo-modal-small">
		<div class="wrapper" v-on-clickaway="close">
			<header class="user-select">
				<h3>
					Keyboard Shortcuts
				</h3>

				<div class="close" @click="close">
					<i class="v-icon v-cancel-small"></i>
				</div>
			</header>

			<div class="middle">
				<div class="flex1">
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
				</div>
			</div>

			<footer>
				<button type="button" class="v-button v-button--green v-button--block" @click="close">
					Close
				</button>
			</footer>
		</div>
    </div>
</template>

<script>
    import { mixin as clickaway } from 'vue-clickaway';

	export default {
        mixins: [ clickaway ],

	    methods: {
	    	close() {
	    		this.$eventHub.$emit('close');
	    	},
	    },
	}
</script>
