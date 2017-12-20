@component('mail::message')
# New {{ $type }} reported at [#{{ $channel->name }}]({{ config('app.url') }}/c/{{ $channel->name }})

We just had a new reprot!!! OMG, please, hurry. We need you!

@component('mail::button', ['url' => $type == 'submission' ? config('app.url') . '/backend/spams/submissions' : config('app.url') . '/backend/spams/comments'])
    {{ $type }} reports
@endcomponent

Love,<br>
{{ config('app.name') }}
@endcomponent
