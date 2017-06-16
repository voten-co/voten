@component('mail::message')
# New Feedback from {{ '@' . $user->username }}

Subject: **{{ $feedback->subject }}** <br>

>{{ $feedback->description }}


@component('mail::panel')
Hope you're doing great ;)
@endcomponent


Regards,<br>
The {{ config('app.name') }} Team
@endcomponent
