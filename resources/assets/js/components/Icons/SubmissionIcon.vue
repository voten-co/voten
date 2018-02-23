<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-document heroicon heroicon-sm">
        <path class="heroicon-document-page heroicon-component-fill" d="M7 1h37a9 9 0 0 1 9 9v49H7V1z"></path>
        <path class="heroicon-document-fold heroicon-component-accent heroicon-component-fill" d="M44 10h5c1.07 0 2.1.24 3 .67V10a8 8 0 0 0-8-8v8z"></path>
        <path class="heroicon-shadows" d="M54 11v8-.5c0-2.48-2-4.5-4.5-4.5H42v-3h12z"></path>
        <path class="heroicon-outline" d="M54 17v43H6V0h38a10 10 0 0 1 10 10v7zm-10-7h5c1.07 0 2.1.24 3 .67V10a8 8 0 0 0-8-8v8zm8 3c-.84-.63-1.87-1-3-1H14v-1h28v-1H14V9h28V2H8v56h44V13zm-6 0v1H14v-1h32zm-32 2h30v1H14v-1zm30 5v1H14v-1h30zm-30 2h32v1H14v-1zm31 2v1H14v-1h31zm-31 2h25v1H14v-1zm32 3v1H14v-1h32zm-32 2h30v1H14v-1zm30 2v1H14v-1h30zm-30 2h32v1H14v-1zm32 3v1H14v-1h32zm-32 2h31v1H14v-1zm30 2v1H14v-1h30zm-30 2h32v1H14v-1zm20 2v1H14v-1h20zM14 17h20v1H14v-1z"></path>
    </svg>
</template>

<style>
.heroicon-document .heroicon-outline {
    fill: #303034;
}
.heroicon-document .heroicon-component-fill {
    fill: #fff;
}
.heroicon-document .heroicon-component-accent {
    fill: #919495;
}
.heroicon-document .heroicon-shadows {
    fill: #303034;
    opacity: 0.4;
}
</style>

<script>
export default {
    props: ['width', 'height']
};
</script>
