<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-document-plus heroicon heroicon-sm">
        <path class="heroicon-document-plus-page heroicon-component-fill" d="M47 36V10a9 9 0 0 0-9-9H1v58h41.2A12 12 0 0 1 47 36z"></path>
        <path class="heroicon-document-plus-fold heroicon-component-accent heroicon-component-fill" d="M38 10h5c1.07 0 2.1.24 3 .67V10a8 8 0 0 0-8-8v8z"></path>
        <circle class="heroicon-document-plus-circle heroicon-component-accent heroicon-component-fill" cx="47" cy="48" r="12"></circle>
        <polygon class="heroicon-document-plus-symbol heroicon-component-fill" points="49 46 49 41 45 41 45 46 40 46 40 50 45 50 45 55 49 55 49 50 54 50 54 46"></polygon>
        <path class="heroicon-shadows" d="M48 11v8-.5c0-2.48-2-4.5-4.5-4.5H36v-3h12z"></path>
        <path class="heroicon-outline" d="M46 60H0V0h38a10 10 0 0 1 10 10v26.04a12 12 0 0 1 0 23.92V60h-2zm0-47c-.84-.63-1.87-1-3-1H8v-1h28v-1H8V9h28V2H2v56h38.36a11.99 11.99 0 0 1-4.98-13H8v-1h27.68c.12-.34.26-.67.4-1H8v-1h28.6c.2-.34.42-.68.65-1H8v-1h30.06c.31-.35.65-.69 1-1H8v-1h32v.25a11.94 11.94 0 0 1 6-2.2V13zM38 2v8h5c1.07 0 2.1.24 3 .67V10a8 8 0 0 0-8-8zm9 56a10 10 0 1 0 0-20 10 10 0 0 0 0 20zm-7-45v1H8v-1h32zm-2 2v1H8v-1h30zm0 5v1H8v-1h30zm2 2v1H8v-1h32zm-1 2v1H8v-1h31zm-6 2v1H8v-1h25zm7 3v1H8v-1h32zm-2 2v1H8v-1h30zm0 2v1H8v-1h30zm2 2v1H8v-1h32zM28 46v1H8v-1h20zm0-29v1H8v-1h20zm26 29v4h-5v5h-4v-5h-5v-4h5v-5h4v5h5zm-6 0v-4h-2v5h-5v2h5v5h2v-5h5v-2h-5v-1z"></path>
    </svg>
</template>

<style>
    .heroicon-document-plus .heroicon-outline { fill: #303034; }
    .heroicon-document-plus .heroicon-component-fill { fill: #FFF; }
    .heroicon-document-plus .heroicon-component-accent { fill: #919495; }
    .heroicon-document-plus .heroicon-shadows { fill: #303034; opacity: 0.4;}
</style>

<script>
    export default {
        props: [
            'width', 'height',
        ],
    };
</script>