<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-tag heroicon heroicon-sm">
        <path class="heroicon-tag-background heroicon-component-fill" d="M0 6l6-6h20l32.59 32.59L60 34l-1.41 1.41L35.4 58.6 34 60l-1.41-1.41L0 26V6zm12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"></path>
        <path class="heroicon-tag-description-border heroicon-component-accent heroicon-component-fill" d="M28.3 10.7l.7-.7.7.7 22.6 22.6.7.7-.7.7-17.6 17.6-.7.7-.7-.7-22.6-22.6-.7-.7.7-.7 17.6-17.6zM34 47.6L47.59 34 29 15.41 15.41 29 34 47.59z"></path>
        <polygon class="heroicon-tag-description heroicon-component-fill" points="34 47.586 15.414 29 29 15.414 47.586 34"></polygon>
        <path class="heroicon-tag-hole heroicon-component-accent heroicon-component-fill" d="M12 19a7 7 0 1 1 0-14 7 7 0 0 1 0 14zm0-4a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
        <path class="heroicon-outline" d="M0 6l6-6h20l32.59 32.59L60 34l-1.41 1.41L35.4 58.6 34 60l-1.41-1.41L0 26V6zm2 19.17l32 32L57.17 34l-32-32H6.83L2 6.83v18.34zm26.3-14.46L29 10l.7.7 22.6 22.6.7.7-.7.7-17.6 17.6-.7.7-.7-.7-22.6-22.6-.7-.7.7-.7 17.6-17.6zM11.4 29L34 51.59 51.59 34 29 11.41 11.41 29zM29 14l.7.7 18.6 18.6.7.7-.7.7-13.6 13.6-.7.7-.7-.7-18.6-18.6-.7-.7.7-.7 13.6-13.6.7-.7zm5 33.59L47.59 34 29 15.41 15.41 29 34 47.59zM12 19a7 7 0 1 1 0-14 7 7 0 0 1 0 14zm6-7a6 6 0 1 0-12 0 6 6 0 0 0 12 0zm-6 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm2-4a2 2 0 1 0-4 0 2 2 0 0 0 4 0z"></path>
    </svg>
</template>

<style>
.heroicon-tag .heroicon-outline {
    fill: #303034;
}
.heroicon-tag .heroicon-component-fill {
    fill: #fff;
}
.heroicon-tag .heroicon-component-accent {
    fill: #919495;
}
.heroicon-tag .heroicon-shadows {
    fill: #303034;
    opacity: 0.4;
}
</style>

<script>
export default {
    props: ['width', 'height']
};
</script>
