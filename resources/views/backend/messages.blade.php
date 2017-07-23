@if (count($errors) > 0)
    <section class="section container">
        <article class="message is-danger">
            <div class="message-body">
                <h3><strong>Error:</strong></h3>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </article>
    </section>
@endif

@if(Session::has('warning'))
    <section class="section container">
        <article class="message is-danger">
            <div class="message-body">
                <h3><strong>Error:</strong></h3>

                <p>
                    {{ Session::get('warning') }}
                </p>
            </div>
        </article>
    </section>
@endif

@if(App::isDownForMaintenance())
    <section class="section container">
        <article class="message is-warning">
            <div class="message-body">
                <h3><strong>Maintenance Mode is on!</strong></h3>

                <p>
                    This is just a reminder to let you know that the maintenance mode is on meaning that users can not access website.
                    So please make it quick.
                </p>
            </div>
        </article>
    </section>
@endif

@if(Session::has('status'))
    <section class="section container">
        <article class="message is-success">
            <div class="message-body">
                <p>
                    {{ Session::get('status') }}
                </p>
            </div>
        </article>
    </section>
@endif