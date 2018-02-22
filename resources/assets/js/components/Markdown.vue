<template>
	<div class="markdown enable-user-select" v-html="compiled"></div>
</template>

<script>
let md = require('markdown-it')({
    html: true,
    breaks: true,
    langPrefix: 'language-',
    linkify: true,
    typographer: true,
    quotes: '“”‘’'
});

export default {
    props: ['text'],

    computed: {
        compiled() {
            let text = this.text;

            text = text
                .replace(
                    /(?:^| )(@[A-Za-z0-9\._]+)/gm,
                    ' [$1](https://voten.co/$1)'
                )
                .replace(
                    /(?:^| )#([A-Za-z0-9_]+)/gm,
                    ' [#$1](https://voten.co/c/$1)'
                )
                .replace(
                    /(?:^| )\/c\/([A-Za-z0-9_]+)/gm,
                    ' [#$1](https://voten.co/c/$1)'
                );

            text = emojione.shortnameToImage(text);

            return md.render(text);
        }
    }
};
</script>

<style lang="scss">
.markdown,
.preview {
    a {
        color: rgb(85, 135, 215);

        &:hover {
            text-decoration: underline;
        }
    }

    ul,
    ol {
        margin-top: 0;

        li {
            margin-top: 0.25em;
        }
    }

    p,
    pre {
        margin-top: 0;
    }

    code {
        padding: 0.2em 0.4em;
        margin: 0;
        background-color: rgba(27, 31, 35, 0.05);
        border-radius: 3px;
        font-weight: bold;
    }

    pre {
        padding: 16px;
        overflow: auto;
        line-height: 1.45;
        background-color: #f6f8fa;
        border-radius: 3px;
        border: 2px solid #e7e7e7;

        code {
            display: inline;
            max-width: auto;
            padding: 0;
            margin: 0;
            overflow: visible;
            line-height: inherit;
            word-wrap: normal;
            background-color: transparent;
            border: 0;
            font-weight: normal;
        }
    }

    blockquote {
        margin: 0;
        padding: 0 1em;
        color: #6a737d;
        border-left: 0.25em solid #dfe2e5;
        margin-top: 0;
        margin-bottom: 16px;
    }
}

.preview {
    border-radius: 4px;
    border: 1px solid #e7e7e7;
    padding: 1em;
    line-height: initial;
    margin-bottom: 0;
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
}

#comment-form .preview {
    max-height: 500px;
    overflow: auto;
}
</style>
