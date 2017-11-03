<template>
    <div class="vo-modal-small">
		<div class="wrapper" v-on-clickaway="close">
			<header class="user-select">
				<h3>
					Markdown Guide
				</h3>

				<div class="close" @click="close">
					<i class="v-icon v-cancel-small"></i>
				</div>
			</header>
					
			<div class="middle">
				<div class="flex1">
					<p class="user-select">
						Voten uses a slightly-customized and limited version of Markdown for formatting. Here is a quick and simple guide:
					</p>

					<table class="table">
						<thead class="user-select">
							<tr>
								<th>You Type</th>
								<th class="half-width">You See</th>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td>
									**bold**
								</td>

								<td>
									<b>bold</b>
								</td>
							</tr>

							<tr>
								<td>
									__italic__
								</td>

								<td>
									<i>italic</i>
								</td>
							</tr>

							<tr>
								<td>
									[voten.co](https://voten.co)
								</td>

								<td>
									<a href="https://voten.co" target="_blank">voten.co</a>
								</td>
							</tr>

							<tr>
								<td>
									-unordered lists <br> -unordered lists <br> -unordered lists
								</td>

								<td>
									<ul class="no-margin">
										<li>unordered lists</li>
										<li>unordered lists</li>
										<li>unordered lists</li>
									</ul>
								</td>
							</tr>

							<tr>
								<td>
									1.ordered list <br> 2.ordered list <br> 3.ordered list
								</td>

								<td>
									<ol class="no-margin">
										<li>ordered list</li>
										<li>ordered list</li>
										<li>ordered list</li>
									</ol>
								</td>
							</tr>

							<tr>
								<td>
									`Inline code`
								</td>

								<td>
									<code>Inline code</code>
								</td>
							</tr>

							<tr>
								<td>
									<br> ```if (url == "voten") {<br> return "welcome"<br> }```
								</td>

								<td>
<pre>if (url == "voten") {
return "welcome"
}</pre>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<footer>
				<el-button type="success" size="medium" @click="close">
					Close
				</el-button>
			</footer>
		</div>
    </div>
</template>

<script>
    import { mixin as clickaway } from 'vue-clickaway';

	export default {
        mixins: [ clickaway ],

	    methods: {
	    	close () {
	    		this.$eventHub.$emit('close');
	    	},
	    },
	}
</script>
