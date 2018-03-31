@if(Auth::check())
    <script>
        var clientsideSettings = {!! Auth::user()->clientsideSettings('Web') ?? '{}' !!}; 

        var meta = {
            isGuest: false, 
            isMobileDevice: {{ isMobileDevice() ? 'true' : 'false' }}, 
            isVotenAdministrator: {{ Auth::user()->isVotenAdministrator() ? 'true' : 'false' }},
        };

        var preload = {};

        var auth = {!! json_encode((new \App\Http\Resources\UserResource(auth()->user(), true, false, true))->resolve()) !!}; 
    </script>
@else
    <script>
        var clientsideSettings = {}; 
        
        var meta = {
            isGuest: true, 
            isMobileDevice: {{ isMobileDevice() ? 'true' : 'false' }},
            isVotenAdministrator: false 
        };

        var preload = {};

        var auth = {}; 
    </script>
@endif