@component('mail::message')
# New user registered: {{ '@' . $username }}

We just had another registration by the username: "{{ $username }}"

@component('mail::button', ['url' => config('app.url') . '/backend'])
Checkout
@endcomponent


@component('mail::panel')
Hope you're doing great ;)
@endcomponent


Regards,<br>
The {{ config('app.name') }} Team
@endcomponent
