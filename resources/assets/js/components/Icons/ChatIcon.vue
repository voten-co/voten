<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-chat heroicon heroicon-sm">
        <path class="heroicon-chat-bubble-back heroicon-component-accent heroicon-component-fill" d="M40 16V6a6 6 0 0 0-6-6H6a6 6 0 0 0-6 6v18a6 6 0 0 0 6 6h6v10l4-4V22a6 6 0 0 1 6-6h18z"></path>
        <path class="heroicon-chat-bubble-front heroicon-component-fill" d="M16 22a6 6 0 0 1 6-6h32a6 6 0 0 1 6 6v20a6 6 0 0 1-6 6h-8v12L34 48H22a6 6 0 0 1-6-6V22z"></path>
        <path class="heroicon-chat-dots-back heroicon-component-fill" d="M19.06 16.77a2 2 0 1 1 2.67-.76c-.97.04-1.87.31-2.67.76zm6.2-.77a2 2 0 1 1 3.46 0h-3.45zM13 17a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"></path>
        <path class="heroicon-chat-dots-front heroicon-component-accent heroicon-component-fill" d="M29 35a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm9 0a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm9 0a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
        <path class="heroicon-shadows" d="M18 39v1a4 4 0 0 0 4 4h32a4 4 0 0 0 4-4v-1h2v3a6 6 0 0 1-6 6h-8v12L34 48H22a6 6 0 0 1-6-6v-3h2zm-2-3l-4 4V30H6a6 6 0 0 1-6-6v-8h2v6a4 4 0 0 0 3.99 4H14v-4a6 6 0 0 1 6-6h11-9a6 6 0 0 0-6 6v14z"></path>
        <path class="heroicon-outline" d="M16 42v-6l-2 2-2 2V30H6a6 6 0 0 1-6-6V6a6 6 0 0 1 6-6h28a6 6 0 0 1 6 6v10h14a6 6 0 0 1 6 6v20a6 6 0 0 1-6 6h-8v12l-2-2-10-10H22a6 6 0 0 1-6-6zm22-26V6a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v18a4 4 0 0 0 4 4h8v7.17l2-2V22a6 6 0 0 1 3.06-5.23 2 2 0 1 1 2.67-.76L22 16h3.27a2 2 0 1 1 3.47 0H38zm-11 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm7.83 30l.58.59L44 55.17V46h10a4 4 0 0 0 4-4V22a4 4 0 0 0-4-4H22a4 4 0 0 0-4 4v20a4 4 0 0 0 4 4h12.83zM21 15a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm-8 2a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm1-2a1 1 0 1 0-2 0 1 1 0 0 0 2 0zM6 4h7v1H6a1 1 0 0 0-1 1v4H4V6c0-1.1.9-2 2-2zm49 31h1v7a2 2 0 0 1-2 2H41v-1h13a1 1 0 0 0 1-1v-7zm-17 0a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm2-3a2 2 0 1 0-4 0 2 2 0 0 0 4 0zm-8 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm-3 2a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm18 1a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm2-3a2 2 0 1 0-4 0 2 2 0 0 0 4 0zM22 20h20v1H22v-1z"></path>
    </svg>
</template>

<style>
.heroicon-chat .heroicon-outline {
    fill: #303034;
}
.heroicon-chat .heroicon-component-fill {
    fill: #fff;
}
.heroicon-chat .heroicon-component-accent {
    fill: #919495;
}
.heroicon-chat .heroicon-shadows {
    fill: #303034;
    opacity: 0.4;
}
</style>

<script>
export default {
    props: ['width', 'height']
};
</script>
