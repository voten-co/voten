@component('mail::message')
# Welcome To {{ config('app.name') }} {{ '@' . $username }}

We are so excited you joined {{ config('app.name') }}. Here are a couple of tips to help you get started:
<br>

- **Customize:** You can [customize]({{ config('app.url') }}/{{ '@' . $username }}/settings) your account's color, font and your very own home feed to make sure you always get what's best for you.
- **Find Channels:** {{ config('app.name') }} is nothing but a collection of awesome channels (communities) with awesome users like you. Whenever you felt like finding new ones just [go here]({{ config('app.url') }}/find-channels)
- **Need Help?** If you are wondering about a {{ config('app.name') }}'s feature that is confusing to you, just look for it in our [help center]({{ config('app.url') }}/help)


@component('mail::button', ['url' => config('app.url') . '/c/sayhello'])
Say Hello
@endcomponent


@component('mail::panel')
Should you ever encounter problems with your account or forget your password we will contact you at this address.
@endcomponent


Regards,<br>
The {{ config('app.name') }} Team
@endcomponent


