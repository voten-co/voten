@component('mail::message')
# New {{ $type }} reported at [#{{ $category->name }}]({{ config('app.url') }}/c/{{ $category->name }})

We just had a new reprot!!! OMG, please, hurry. We need you!

@component('mail::button', ['url' => $type == 'submission' ? config('app.url') . '/backend/spams/submissions' : config('app.url') . '/backend/spams/comments'])
    {{ $type }} reports
@endcomponent

Love,<br>
{{ config('app.name') }}
@endcomponent
