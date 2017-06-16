@component('mail::message')
# New user registered: {{ '@' . $username }}

We just had another registeration by the username: "{{ $username }}"

@component('mail::button', ['url' => 'https://voten.co/backend'])
Checkout
@endcomponent


@component('mail::panel')
Hope you're doing great ;)
@endcomponent


Regards,<br>
The Voten Team
@endcomponent
