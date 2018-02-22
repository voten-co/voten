<script>
    <?php
        $settings = [
            'url' => config('app.url'), 
            'name' => config('app.name'), 
            'title' => config('app.title'), 
            'csrfToken' => csrf_token(),
            'env' => config('app.env'),
            'recaptchaKey' => config('services.recaptcha.key'),
            'sentry' => config('sentry.dsn-js'), 

            'pusher' => [
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            ],

            'echo' => [
                'host' => config('broadcasting.connections.echo.host'), 
                'port' => config('broadcasting.connections.echo.port'), 
                'bearerToken' => config('broadcasting.connections.echo.bearerToken'), 
            ], 
        ];
    ?>

    window.Laravel = @json($settings)
</script>