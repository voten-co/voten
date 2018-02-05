@if(Auth::check())
    <script>
        var preload = {};

        var clientsideSettings = {!! Auth::user()->clientsideSettings('Web') ?? '{}' !!}; 

        var meta = {
            isGuest: false, 
            isMobileDevice: {{ isMobileDevice() ? 'true' : 'false' }}, 
            isVotenAdminstrator: {{ Auth::user()->isVotenAdministrator() ? 'true' : 'false' }},
        };

        var auth = {!! json_encode((new \App\Http\Resources\UserResource(auth()->user()))->resolve()) !!}; 
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