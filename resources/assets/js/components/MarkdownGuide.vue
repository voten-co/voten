<template>
    <div class="v-modal-small" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box" v-on-clickaway="close">
            <div class="flex1">
                <h2 class="align-center user-select">
                    Markdown Guide
                </h2>

                <p class="user-select">
                    Voten uses a slightly-customized and limited version of Markdown for formatting. Here is a quick and simple guide:
                </p>

                <table class="table">
				  	<thead class="user-select">
					    <tr>
							<th>Your Type</th>
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
                                -unordered lists <br>
                                -unordered lists <br>
                                -unordered lists
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
                                1.ordered list <br>
                                2.ordered list <br>
                                3.ordered list
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
					      	<br>
					      		```if (url == "voten") {<br>
						        	return "welcome"<br>
						        }```
					      	</td>

					      	<td>
<pre>if (url == "voten") {
    return "welcome"
}</pre>
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
