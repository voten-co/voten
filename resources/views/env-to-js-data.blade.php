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
            
            'broadcasting' => [
                'service' => config('broadcasting.service', null), 

                'pusher' => [
                    'key' => config('broadcasting.connections.pusher.key'),
                    'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                ],

                'echo' => [
                    'host' => config('broadcasting.connections.echo.host'), 
                    'port' => config('broadcasting.connections.echo.port'), 
                    'key' => config('broadcasting.connections.echo.auth_key'), 
                ], 
            ], 
        ];
    ?>

    window.Laravel = @json($settings)
</script>