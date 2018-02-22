<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-envelope-plus heroicon heroicon-sm">
        <rect class="heroicon-envelope-plus-body heroicon-component-fill" width="60" height="36" y="6" rx="2"></rect>
        <path class="heroicon-envelope-plus-sides heroicon-component-accent heroicon-component-fill" d="M55 31.25V11l4-4v29.2a12.04 12.04 0 0 0-4-4.95zM1 7l4 4v26l-4 4V7z"></path>
        <circle class="heroicon-envelope-plus-circle heroicon-component-accent heroicon-component-fill" cx="48" cy="41" r="12"></circle>
        <polygon class="heroicon-envelope-plus-symbol heroicon-component-fill" points="50 39 50 34 46 34 46 39 41 39 41 43 46 43 46 48 50 48 50 43 55 43 55 39"></polygon>
        <polygon class="heroicon-shadows" points="2 9 30 32 58 9 59 9 30 35 1 9"></polygon>
        <path class="heroicon-outline" d="M60 40c0 .1 0 .2-.02.29L60 41a12 12 0 0 1-23.96 1H2a2 2 0 0 1-2-2V8c0-1.1.9-2 2-2h56a2 2 0 0 1 2 2v32zM4 11.2L2 9.6v28.8l2-1.6V11.2zM5 36l15-12L5 12v24zm-1.8 4h32.84a12 12 0 0 1 7.9-10.3l-5.54-4.42L30 32l-8.4-6.72L3.2 40zM48 29c2.61 0 5.03.83 7 2.25V12L40 24l6.38 5.1c.53-.06 1.07-.1 1.62-.1zm10 5.36V9.6l-2 1.6v20.86a12.2 12.2 0 0 1 2 2.3zM3.2 8L30 29.44 56.8 8H3.2zM48 51a10 10 0 1 0 0-20 10 10 0 0 0 0 20zm7-12v4h-5v5h-4v-5h-5v-4h5v-5h4v5h5zm-6 0v-4h-2v5h-5v2h5v5h2v-5h5v-2h-5v-1z"></path>
    </svg>
</template>

<style>
.heroicon-envelope-plus .heroicon-outline {
    fill: #303034;
}
.heroicon-envelope-plus .heroicon-component-fill {
    fill: #fff;
}
.heroicon-envelope-plus .heroicon-component-accent {
    fill: #556080;
}
.heroicon-envelope-plus .heroicon-shadows {
    fill: #303034;
    opacity: 0.4;
}
</style>

<script>
export default {
    props: ['width', 'height']
};
</script>
