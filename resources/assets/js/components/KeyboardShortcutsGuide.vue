<template>
    <el-dialog
            title="Keyboard Shortcuts"
            :visible="visible"
            :width="isMobile ? '99%' : '600px'"
            @close="close"
            append-to-body
            class="user-select"
    >
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
    </el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    props: ['visible'],

    beforeDestroy() {
        if (window.location.hash == '#keyboardShortcutsGuide') {
            history.go(-1);
        }
    },

    created() {
        window.location.hash = 'keyboardShortcutsGuide';
    },

    methods: {
        close() {
            this.$emit('update:visible', false);
        }
    }
};
</script>
