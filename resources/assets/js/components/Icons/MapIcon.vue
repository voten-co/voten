<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-map heroicon heroicon-sm">
        <path class="heroicon-map-outer heroicon-component-accent heroicon-component-fill" d="M20.05 13.02L15 11 0 17v40l15-6 15 6 15-6 15 6V17l-15-6-5.05 2.02c.03.32.05.65.05.98 0 5.52-10 20-10 20S20 19.52 20 14c0-.33.02-.66.05-.98z"></path>
        <path class="heroicon-map-inner heroicon-component-fill" d="M21.14 18.46L15 16 4 20v30l11-4 15 6 15-6 11 4V20l-11-4-6.14 2.46C36.23 24.97 30 34 30 34s-6.23-9.03-8.86-15.54z"></path>
        <path class="heroicon-map-pin heroicon-component-accent heroicon-component-fill" d="M30 34S20 19.52 20 14a10 10 0 1 1 20 0c0 5.52-10 20-10 20zm0-18a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
        <path class="heroicon-shadows" d="M30 37S20 22.52 20 17c0-.58.05-1.15.14-1.7C21.37 21.5 30 34 30 34l.27-.4.73-1.09v3L30 37v20l15-6V11l-5.05 2.02c.03.32.05.65.05.98 0 4.98-8.14 17.25-9.73 19.6L30 37zM0 17l15-6v40L0 57V17z"></path>
        <path class="heroicon-outline" d="M20.05 13.02a10 10 0 0 1 19.9 0L45 11l15 6v40l-15-6-15 6-15-6-15 6V17l15-6 5.05 2.02zm1.28 5.9L16 16.79v13.53l13 5.2v-3a69.99 69.99 0 0 1-.2-.31c-1.88-2.87-5.51-8.63-7.47-13.3zm-.5-1.26c-.32-.89-.57-1.72-.7-2.46L16 13.55v2.16l4.83 1.93v.02zM39.9 15.1c-.13.75-.38 1.61-.72 2.53L44 15.7v-2.16l-4.13 1.65.02-.09zm-1.2 3.75c-1.95 4.67-5.6 10.47-7.49 13.35l-.2.3v3.01l13-5.2V16.8l-5.33 2.13.02-.06zm-5.42 6.2a66.36 66.36 0 0 0 3.34-6.4C37.54 16.52 38 14.92 38 14a8 8 0 1 0-16 0c0 .93.46 2.53 1.4 4.65.9 2.06 2.1 4.3 3.33 6.42 1.18 2.03 2.36 3.9 3.27 5.32.91-1.42 2.09-3.29 3.27-5.32zM46 13.57v2.15l10 4v14.61l2 .8V18.35l-12-4.8zM14 15.7v-2.16l-12 4.8v16.77l2-.8V19.71l10-4zM5 33.92l9-3.6V16.8l-9 3.6v13.53zm-1 2.56l-2 .8v16.77l12-4.8v-2.16l-9 3.6-1 .4V36.48zM14 46V32.48l-9 3.6V49.6l9-3.6zm2 1.08v2.16l13 5.2v-2.16l-13-5.2zm13 4.12V37.68l-13-5.2V46l13 5.2zm2 1.08v2.16l13-5.2v-2.16l-13 5.2zm13-6.28V32.48l-13 5.2V51.2l13-5.2zm2 1.08v2.16l12 4.8V37.28l-2-.8v14.61l-1-.4-9-3.6zm9-11.01l-9-3.6V46l9 3.6V36.08zm0-2.16V20.4l-9-3.6v13.53l9 3.6zM30 17a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm2-4a2 2 0 1 0-4 0 2 2 0 0 0 4 0z"></path>
    </svg>
</template>

<style>
.heroicon-map .heroicon-outline {
    fill: #303034;
}
.heroicon-map .heroicon-component-fill {
    fill: #fff;
}
.heroicon-map .heroicon-component-accent {
    fill: #919495;
}
.heroicon-map .heroicon-shadows {
    fill: #303034;
    opacity: 0.4;
}
</style>

<script>
export default {
    props: ['width', 'height']
};
</script>
