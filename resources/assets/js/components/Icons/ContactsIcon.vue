<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-contacts-book heroicon heroicon-sm">
        <path class="heroicon-contacts-book-cover heroicon-component-fill" d="M5 4a3 3 0 0 1 3-3h43v48H8v7h43v3H8a3 3 0 0 1-3-3V4z"></path>
        <polygon class="heroicon-contacts-book-pages heroicon-component-fill" points="50 56 50 49 8 49 8 56"></polygon>
        <path class="heroicon-contacts-book-binding heroicon-component-accent heroicon-component-fill" d="M5 4a3 3 0 0 1 3-3h6v45H5V4z"></path>
        <polygon class="heroicon-contacts-book-tab-1 heroicon-component-accent heroicon-component-fill" points="54 13.764 52 14.764 52 7.236 54 8.236"></polygon>
        <polygon class="heroicon-contacts-book-tab-2 heroicon-component-accent heroicon-component-fill" points="54 22.764 52 23.764 52 17 52.764 16.618 54 17.236"></polygon>
        <polygon class="heroicon-contacts-book-tab-3 heroicon-component-accent heroicon-component-fill" points="54 31.764 52 32.764 52 26 52.764 25.618 54 26.236"></polygon>
        <polygon class="heroicon-contacts-book-tab-4 heroicon-component-accent heroicon-component-fill" points="54 40.764 52 41.764 52 35 52.764 34.618 54 35.236"></polygon>
        <path class="heroicon-shadows" d="M5 49a3 3 0 0 1 3-3h43v13H8a3 3 0 0 1-3-3v-7z"></path>
        <path class="heroicon-outline" d="M51 53v2h1v5H8a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4h44v5l4 2v8l-1 .5 1 .5v8l-1 .5 1 .5v8l-1 .5 1 .5v8l-4 2v6h-1v3zm-1-10.24V2H15v43h35v-2.24zm4-2v-5.52l-1.24-.62L52 35v6.76l2-1zm0-9v-5.52l-1.24-.62L52 26v6.76l2-1zm0-9v-5.52l-1.24-.62L52 17v6.76l2-1zm0-9V8.24l-2-1v7.52l2-1zM49 51v-1H9v5h40v-1H31v-1h18v-1H11v-1h38zm0 6H7v-9h43v-1H8a2 2 0 0 0-2 2v7c0 1.1.9 2 2 2h42v-1h-1zM6 45.54A3.98 3.98 0 0 1 8 45h5V2h-2v39h-1V2H9v39H8V2a2 2 0 0 0-2 2v41.54zm28.6-18.07A4.99 4.99 0 0 1 26 24a5 5 0 0 1 8-4v-1h2v6.5a1.5 1.5 0 0 0 3 0V24a8 8 0 1 0-4.42 7.16l.9 1.79A10 10 0 1 1 41 24l-.01 1.5a3.5 3.5 0 0 1-6.4 1.97zM31 27a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
    </svg>
</template>

<style>
.heroicon-contacts-book .heroicon-outline {
    fill: #303034;
}
.heroicon-contacts-book .heroicon-component-fill {
    fill: #f7f7f8;
}
.heroicon-contacts-book .heroicon-component-accent {
    fill: #8b9397;
}
.heroicon-contacts-book .heroicon-shadows {
    fill: #303034;
    opacity: 0.4;
}
</style>

<script>
export default {
    props: ['width', 'height']
};
</script>
