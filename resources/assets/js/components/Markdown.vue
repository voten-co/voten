<template>
    <div v-html="compiled" class="markdown"></div>
</template>

<script>
    export default {
        props: ['text'],

        computed: {
            compiled(){
                let text = this.text;

                text = text.replace(/(?:^| )(@[A-Za-z0-9\._]+)/gm, " [$1](https://voten.co/$1)");
                text = text.replace(/(?:^| )(https?:\/\/[^ ]*)/gm, " [$1]($1)");
                text = text.replace(/\\([*`_>()\[\]-])/gm, function (i) {
                    return '~' + i.charCodeAt(1)
                });
                text = text.split("&").join("&amp;").split("<").join("&lt;").split("'").join("&apos;").split('"').join("&quot;");
                text = text.replace(/\*\*([^*]*)\*\*/gm, "<strong>$1</strong>");

                text = text.replace(/\[([^\[\n]*)\]\(((https?):\/\/voten.co\/[^\)]*)\)/igm, "<a href='$2'>$1</a>");
                text = text.replace(/\[([^\[\n]*)\]\(((https?):\/\/[^\)]*)\)/igm, "<a href='$2' target='_blank' rel='nofollow'>$1</a>");
                text = text.replace(/^>([^\n]*)/gm, "<p>$1</p>");
                for (var i = 1; i < 4; i++) text = text.replace(new RegExp("\<p\>{" + i + "}([^\n]*(\n\<p\>{" + i + "}[^\n]*)*)", "gm"), "<blockquote><p>$1</blockquote>");
                text = text.replace(/\<p\>*/gm, "<p>");
                text = text.replace(/```([^`]*)```/gm, "<pre>$1</pre>");
                text = text.replace(/`([^`\n]*)`/gm, "<code>$1</code>");
                text = text.replace(/^\-([^\n]*)/gm, "<li>$1</li>");
                text = text.replace(/(\<li\>[^\n]*(\n\<li\>[^\n]*)*)/gm, "<ul>$1</ul>");
                text = text.replace(/^(\d*\.[^\n]*)/gm, "<li>$1</li>");
                text = text.replace(/(\<li\>\d[^\n]*(\n\<li\>\d[^\n]*)*)/gm, "<ol>$1</ol>").replace(/\<li\>\d*\./g, "<li>");
                text = text.replace(/\n^(?!<li>|<p>|<\/?blockquote>|<\/?ol>|<\/?ul>)/gm, "<br>").replace(/~(40|41|42|45|62|91|93|95|96)/gm, function (p, q) {
                    return String.fromCharCode(q)
                });

                // Emoji
                text = emojione.shortnameToImage(text);

                text = text.replace(/__([^]*)__/gm, "<i>$1</i>");

                return text;
            }
        }
    }
</script>

<style>
    .markdown a {
        color: rgb(85, 135, 215);
    }
    .markdown a:hover {
        text-decoration: underline;
    }
    .announcement .markdown a {
        color: #fff;
    }
</style>
