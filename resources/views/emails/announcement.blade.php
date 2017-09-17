@component('mail::message')

@if($heading)
# {{ $heading }}
@else
# Hey {{ '@' . $username }},
@endif

{{ $content }}

@if($button_text)
@component('mail::button', ['url' => $button_url])
    {{ $button_text }}
@endcomponent
@endif

Thanks for your time,<br>
The Voten Team
@endcomponent