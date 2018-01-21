<template>
    <el-dialog
            title="Markdown Guide"
            :visible="visible"
            :width="isMobile ? '99%' : '600px'"
            @close="close"
            append-to-body
            class="user-select"
    >
        <p>
            Voten uses a slightly-customized and limited version of Markdown for formatting. Here is a quick and simple guide:
        </p>

        <table class="table enable-user-select">
            <thead class="user-select">
            <tr>
                <th>You Type</th>
                <th class="half-width">You See</th>
            </tr>
            </thead>

            <tbody class="markdown">
            <tr>
                <td>
                    **bold** or __bold__ 
                </td>

                <td>
                    <b>bold</b>
                </td>
            </tr>

            <tr>
                <td>
                    *italic* or _italic_
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
                    - unordered lists <br> - unordered lists <br> - unordered lists
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
                    1. ordered list <br> 2. ordered list <br> 3. ordered list
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
    </el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    props: ['visible'],

    beforeDestroy() {
        if (window.location.hash == '#markdownGuide') {
            history.go(-1);
        }
    },

    created() {
        window.location.hash = 'markdownGuide';
    },

    methods: {
        close() {
            this.$emit('update:visible', false);
        }
    }
};
</script>
