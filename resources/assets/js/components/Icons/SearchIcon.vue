<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-magnify heroicon heroicon-sm">
        <circle class="heroicon-magnify-glass-edge heroicon-component-accent heroicon-component-fill" cx="22" cy="22" r="22"></circle>
        <circle class="heroicon-magnify-glass heroicon-component-fill" cx="22" cy="22" r="15"></circle>
        <polygon class="heroicon-magnify-handle heroicon-component-fill" points="45.121 48.293 51.707 54.879 54.879 51.707 48.293 45.121"></polygon>
        <path class="heroicon-magnify-handle-connector heroicon-component-fill" d="M43.59 42.41L42.4 43.6l-3-3 1.18-1.18 3 3zm-4.53-6.52L40.17 37 37 40.17l-1.11-1.1a22.14 22.14 0 0 0 3.17-3.18z"></path>
        <path class="heroicon-magnify-handle-edge heroicon-component-accent heroicon-component-fill" d="M54 57.17l-1.59-1.58 3.18-3.18L57.17 54 54 57.17zM42.83 46L46 42.83l1.59 1.58-3.18 3.18L42.83 46z"></path>
        <path class="heroicon-shadows" d="M4.06 23.5a18 18 0 1 1 35.88 0 18 18 0 0 0-35.88 0z"></path>
        <path class="heroicon-outline" d="M40.27 34.27l1.32 1.32L43 37l-1 1 3 3 1-1 1.41 1.41L49 43l.7.7 6.6 6.6.7.7 1.59 1.59L60 54l-1.41 1.41-3.18 3.18L54 60l-1.41-1.41L51 57l-.7-.7-6.6-6.6-.7-.7-1.59-1.59L40 46l1-1-3-3-1 1-1.41-1.41-1.32-1.32a22 22 0 1 1 6-6zM42 22a20 20 0 1 0-40 0 20 20 0 0 0 40 0zm3.12 26.3l6.59 6.58 3.17-3.17-6.59-6.59-3.17 3.17zM54 57.16L57.17 54l-1.58-1.59-3.18 3.18L54 57.17zM42.83 46l1.58 1.59 3.18-3.18L46 42.83 42.83 46zm.76-3.59l-3-3-1.18 1.18 3 3 1.18-1.18zm-4.53-6.52a22.14 22.14 0 0 1-3.17 3.17L37 40.17 40.17 37l-1.1-1.11zM40 22a18 18 0 1 1-36 0 18 18 0 0 1 36 0zM22 39a17 17 0 1 0 0-34 17 17 0 0 0 0 34zm0-2a15 15 0 1 1 0-30 15 15 0 0 1 0 30zm13-15a13 13 0 1 0-26 0 13 13 0 0 0 26 0zm-24 0a11 11 0 0 1 11-11v1a10 10 0 0 0-8.95 14.47l-.9.45A10.95 10.95 0 0 1 11 22z"></path>
    </svg>
</template>

<style>
.heroicon-outline {
    fill: #303034;
}
.heroicon-component-fill {
    fill: #f7f7f8;
}
.heroicon-component-accent {
    fill: #8b9397;
}
.heroicon-shadows {
    fill: #303034;
    opacity: 0.4;
}
</style>

<script>
export default {
    props: ['width', 'height']
};
</script>
