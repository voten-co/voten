<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-user heroicon heroicon-sm">
        <path class="heroicon-user-top heroicon-component-accent heroicon-component-fill" d="M54 58v-9c0-.92-.78-2.22-1.58-2.66L37 38.04v2.97a7 7 0 0 1-14 0v-2.97l-15.42 8.3A3.57 3.57 0 0 0 6 49v9h48z"></path>
        <path class="heroicon-user-face heroicon-component-fill" d="M35 34.91v6.1a5 5 0 0 1-10 0v-6.1A12 12 0 0 1 18 24V14a12 12 0 1 1 24 0v10a12 12 0 0 1-7 10.91z"></path>
        <path class="heroicon-shadows" d="M22.68 35.94l-6.55 3.53A17.96 17.96 0 0 0 30 46c5.58 0 10.57-2.54 13.87-6.53l-6.55-3.53C38.27 35.35 33.89 38 30 38s-8.27-2.65-7.32-2.06z"></path>
        <path class="heroicon-outline" d="M22.69 35.94A14 14 0 0 1 16 24V14a14 14 0 1 1 28 0v10a14 14 0 0 1-6.68 11.94l16.04 8.64A5.52 5.52 0 0 1 56 49v11H4V49a5.53 5.53 0 0 1 2.64-4.42l16.05-8.64zM49 58h5v-9c0-.92-.78-2.22-1.58-2.66L37 38.04v2.97a7 7 0 0 1-14 0v-2.97l-15.42 8.3A3.57 3.57 0 0 0 6 49v9h5v-4h1v4h36v-4h1v4zM35 37.08a13.97 13.97 0 0 1-10 0v3.93a5 5 0 0 0 10 0v-3.93zM18 14v10a12 12 0 0 0 24 0V14a12 12 0 0 0-24 0zm3.05-4.47A10 10 0 0 1 30 4v1a9 9 0 0 0-8.05 4.97l-.9-.44z"></path>
    </svg>
</template>

<style>
.heroicon-user .heroicon-outline {
    fill: #303034;
}
.heroicon-user .heroicon-component-fill {
    fill: #fff;
}
.heroicon-user .heroicon-component-accent {
    fill: #919495;
}
.heroicon-user .heroicon-shadows {
    fill: #303034;
    opacity: 0.4;
}
</style>

<script>
export default {
    props: ['width', 'height']
};
</script>
