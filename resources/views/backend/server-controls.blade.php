@extends('layouts.backend-layout')

@section('title')
    Server Control
@endsection

@section('content')

<section class="section container">
    <div class="columns is-multiline is-mobile">
        <div class="column is-half">
            <div class="block">
                <h1 class="title">Maintenance Mode:</h1>

                <p>
                    Here you can put the website down for users. This is great for maintenance and updating stuff.
                </p>
            </div>

            <div class="block">
                <a class="button is-danger" href="/ssh/start-maintenance">Start maintenance mode</a>
                <a class="button is-success" href="/ssh/stop-maintenance">Stop maintenance mode</a>
            </div>
        </div>


        <div class="column is-half">
            <div class="block">
                <h1 class="title">Artisan Cache:</h1>

                <p>
                    This clears Laravel's cache. It's necessary after updating .env file.
                </p>
            </div>

            <div class="block">
                <a class="button is-info" href="/ssh/cache-clear">Clear Artisan Cache</a>
            </div>
        </div>

    </div>
</section>


<section class="section container">
    <div class="columns is-multiline is-mobile">
        <div class="column is-half">
            <div class="block">
                <h1 class="title">Redis Cache:</h1>

                <p>
                    You may clear all Redis cache in server, this'll cause more database queries, so please becareful. Run it only when it's necessary.
                </p>
            </div>

            <div class="block">
                <a class="button is-warning" href="/ssh/flush-all">Clear Redis Cache</a>
            </div>
        </div>


        <div class="column is-half">
            <div class="column is-half">
                <div class="block">
                    <h1 class="title">Update comments count:</h1>
                </div>

                <div class="block">
                    <a class="button is-info" href="/backend/update-comments-count">Update Comments count</a>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection
