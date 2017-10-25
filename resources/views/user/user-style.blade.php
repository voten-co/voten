@if (!Auth::check())
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">

	<style media="screen">
		body,
	    #voten-app{
	        font-family: 'Lato', sans-serif;
	    }
	</style>
@else
	<link href="https://fonts.googleapis.com/css?family={{ title_case(str_slug(settings('font'), '+')) }}:300,400,700" rel="stylesheet">

	<style media="screen">
	    body,
	    #voten-app{
	        font-family: '{{ settings('font') }}', sans-serif;
	    }
	</style>
@endif


