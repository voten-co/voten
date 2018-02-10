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
        var preload = {};

        var clientsideSettings = {}; 
        
        var meta = {
            isGuest: true, 
            isMobileDevice: {{ isMobileDevice() ? 'true' : 'false' }},
            isVotenAdminstrator: false 
        };
    </script>
@endif