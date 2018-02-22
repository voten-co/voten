<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="this.width + 'px'" :height="this.height + 'px'" viewBox="0 0 60 60" class="heroicon-load-balancer heroicon heroicon-sm">
        <polygon class="heroicon-load-balancer-box-bottom heroicon-component-accent heroicon-component-fill" points="21 42 39 42 39 60 21 60"></polygon>
        <path class="heroicon-load-balancer-boxes-top heroicon-component-accent heroicon-component-fill" d="M0 0h18v18H0V0zm21 0h18v18H21V0zm39 0v18H42V0h18z"></path>
        <path class="heroicon-load-balancer-cords heroicon-component-fill" d="M9 14a3 3 0 0 0-3 3v7a9 9 0 0 0 9 9h5a3 3 0 0 1 3 3v8h14v-8a3 3 0 0 1 3-3h5a9 9 0 0 0 9-9v-7a3 3 0 1 0-6 0v7a3 3 0 0 1-3 3h-5a8.99 8.99 0 0 0-7 3.35V17a3 3 0 1 0-6 0v13.35A8.99 8.99 0 0 0 20 27h-5a3 3 0 0 1-3-3v-7a3 3 0 0 0-3-3z"></path>
        <path class="heroicon-shadows" d="M12 17h5v-6H1v6h5a3 3 0 1 1 6 0zm21 0h5v-6H22v6h5a3 3 0 1 1 6 0zm21 0a3 3 0 1 0-6 0h-5v-6h16v6h-5zM22 53h16v6H22v-6z"></path>
        <path class="heroicon-outline" d="M12 18v6a3 3 0 0 0 3 3h5c2.82 0 5.35 1.3 7 3.35V18h-6V0h18v18h-6v12.35A8.99 8.99 0 0 1 40 27h5a3 3 0 0 0 3-3v-6h-6V0h18v18h-6v6a9 9 0 0 1-9 9h-5a3 3 0 0 0-3 3v6h2v18H21V42h2v-6a3 3 0 0 0-3-3h-5a9 9 0 0 1-9-9v-6H0V0h18v18h-6zm4-16H2v9h14V2zm0 10H2v4h4.17a3 3 0 0 1 5.66 0H16v-4zm4 17h-5a5 5 0 0 1-5-5v-7a1 1 0 1 0-2 0v7a7 7 0 0 0 7 7h5a5 5 0 0 1 5 5v6h2v-6a7 7 0 0 0-7-7zm5 15h-2v9h14v-9H25zm10-2v-6a5 5 0 0 1 5-5h5a7 7 0 0 0 7-7v-7a1 1 0 1 0-2 0v7a5 5 0 0 1-5 5h-5a7 7 0 0 0-7 7v6h2zm-5-28a3 3 0 0 1 2.83 2H37v-4H23v4h4.17A3 3 0 0 1 30 14zm7-3V2H23v9h14zm-6 6a1 1 0 1 0-2 0v25h2V17zm17.17-1a3 3 0 0 1 5.66 0H58v-4H44v4h4.17zM58 11V2H44v9h14zM23 54v4h14v-4H23z"></path>
    </svg>
</template>

<style>
.heroicon-load-balancer .heroicon-outline {
    fill: #303034;
}
.heroicon-load-balancer .heroicon-component-fill {
    fill: #fff;
}
.heroicon-load-balancer .heroicon-component-accent {
    fill: #919495;
}
.heroicon-load-balancer .heroicon-shadows {
    fill: #303034;
    opacity: 0.4;
}
</style>

<script>
export default {
    props: ['width', 'height']
};
</script>
