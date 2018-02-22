<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-bell heroicon heroicon-sm">
        <path class="heroicon-bell-bowl heroicon-component-fill" d="M50.7 48c-.34-.3-.66-.64-.95-1l-.68-1A7.96 7.96 0 0 1 48 42V24a18 18 0 0 0-36 0V42.01A7.98 7.98 0 0 1 9.3 48h41.4z"></path>
        <path class="heroicon-bell-lip heroicon-component-accent heroicon-component-fill" d="M2 51v-1h56v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
        <path class="heroicon-bell-top heroicon-component-accent heroicon-component-fill" d="M32 4.1V4a2 2 0 1 0-4 .1 20.24 20.24 0 0 1 4 0z"></path>
        <path class="heroicon-bell-clapper heroicon-component-accent heroicon-component-fill" d="M26 54a4 4 0 0 0 8 0h-8z"></path>
        <rect class="heroicon-shadows" width="10" height="3" x="25" y="53"></rect>
        <path class="heroicon-outline" d="M26 4a4 4 0 1 1 7.98.4A20 20 0 0 1 50 24v18a6 6 0 0 0 6 6h4v3a3 3 0 0 1-3 3H36a6 6 0 0 1-12 0H3a3 3 0 0 1-3-3v-3h4a6 6 0 0 0 6-6V24A20 20 0 0 1 26.02 4.4 4.02 4.02 0 0 1 26 4zm6 .1V4a2 2 0 1 0-4 .1 20.24 20.24 0 0 1 4 0zM50.7 48c-.34-.3-.66-.64-.95-1H34v-1h15.07A7.96 7.96 0 0 1 48 42V24a18 18 0 0 0-36 0V42.01A7.98 7.98 0 0 1 9.3 48h41.4zM26 54a4 4 0 0 0 8 0h-8zM2 51a1 1 0 0 0 1 1h54a1 1 0 0 0 1-1v-1H2v1zm15.74-33.77A14 14 0 0 1 30 10v1a13 13 0 0 0-11.35 6.66l-.9-.43zm-1.12 2.62l.98.23c-.3.93-.49 1.91-.56 2.92h-1c.07-1.09.27-2.14.58-3.15zM17 25v8h-1v-8h1z"></path>
    </svg>
</template>

<style>
.heroicon-bell .heroicon-outline {
    fill: #303034;
}
.heroicon-bell .heroicon-component-fill {
    fill: #f7f7f8;
}
.heroicon-bell .heroicon-component-accent {
    fill: #8b9397;
}
.heroicon-bell .heroicon-shadows {
    fill: #303034;
    opacity: 0.4;
}
</style>

<script>
export default {
    props: ['width', 'height']
};
</script>
