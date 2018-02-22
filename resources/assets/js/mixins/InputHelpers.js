export default {
    methods: {
        /**
         * wether or not the input contains only spaces/line-breakers.
         *
         * @return boolean
         **/
        isEmpty(input) {
            return input.trim().length == 0;
        },

        insertPickedItem(textAreaId, pickedStr, starterIndex, typedLength) {
            let textArea = document.getElementById(textAreaId);
            let cursorPosition = this.getCursorPosition(textArea);

            this.replaceBetween(
                textArea,
                pickedStr,
                starterIndex,
                starterIndex + typedLength
            );

            this.updateCursorPosition(
                textArea,
                starterIndex + pickedStr.length
            );
        },

        replaceBetween(textArea, replaceWith, start, end) {
            let before = textArea.value.substring(0, start);
            let after = textArea.value.substring(
                end + 1,
                textArea.value.length
            );

            textArea.value = before + replaceWith + after;

            // to make vue's reactivity work:
            this.message = textArea.value;
        },

        updateCursorPosition(textArea, position) {
            textArea.selectionStart = position;
            textArea.selectionEnd = position;
            textArea.focus();
        },

        getCursorPosition(textArea) {
            return textArea.selectionStart;
        },

        getCursorPositionById(id) {
            let textArea = document.getElementById(id);

            return textArea.selectionStart;
        },

        lastTypedCharacter(id) {
            let cursorPosition = this.getCursorPosition(
                document.getElementById(id)
            );

            return cursorPosition - 1;
        }
    }
};
