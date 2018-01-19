@if(Auth::check())
    <script>
        var auth = {
            id: '{{ Auth::user()->id }}',
            bio: {!! json_encode(Auth::user()->bio) !!},
            name: '{{ Auth::user()->name }}',
            email: '{{ Auth::user()->email }}',
            color: '{{ Auth::user()->color }}',
            avatar: '{{ Auth::user()->avatar }}',
            location: '{{ Auth::user()->location }}',
            username: '{{ Auth::user()->username }}',
            created_at: '{{ Auth::user()->created_at }}',
            font: '{{ settings('font') }}',
            nsfw: {{ settings('nsfw') ? 'true' : 'false' }},
            nsfwMedia: {{ settings('nsfw_media') ? 'true' : 'false' }},
            sidebar_color: '{{ settings('sidebar_color') }}',
            notify_comments_replied: {{ settings('notify_comments_replied') ? 'true' : 'false' }},
            notify_submissions_replied: {{ settings('notify_submissions_replied') ? 'true' : 'false' }},
            notify_mentions: {{ settings('notify_mentions') ? 'true' : 'false' }},
            exclude_upvoted_submissions: {{ settings('exclude_upvoted_submissions') ? 'true' : 'false' }},
            exclude_downvoted_submissions: {{ settings('exclude_downvoted_submissions') ? 'true' : 'false' }},
            isMobileDevice: {{ isMobileDevice() ? 'true' : 'false' }},
            submission_small_thumbnail: {{ isMobileDevice() ? 'false' : 'true' }},
            info: {
                website: '{{ Auth::user()->info['website'] }}',
                twitter: '{{ Auth::user()->info['twitter'] }}'
            },
            stats: {!! Auth::user()->stats() !!},
            isGuest: {{ 'false' }},
            confirmedEmail: {{ Auth::user()->confirmed ? 'true' : 'false' }},
            isVotenAdminstrator: {{ optional(Auth::user())->isVotenAdministrator() ? 'true' : 'false' }}
        };

        var preload = {};

        var clientsideSettings = {!! Auth::user()->clientsideSettings('Web') ?? '{}' !!}
    </script>
@else
    <script>
        var auth = {
            font: 'Lato',
            nsfw: {{ 'false' }},
            nsfwMedia: {{ 'false' }},
            sidebar_color: 'Gray',
            isMobileDevice: {{ isMobileDevice() ? 'true' : 'false' }},
            <?php
                if (isMobileDevice()) {
                    $submission_small_thumbnail = 'false';
                } else {
                    $submission_small_thumbnail = 'true';
                }
            ?>
            submission_small_thumbnail: {{ $submission_small_thumbnail }},
            isGuest: {{ 'true' }}, 
            isVotenAdminstrator: false 
            };

        var preload = {};

        var clientsideSettings = {}; 
</script>
@endif