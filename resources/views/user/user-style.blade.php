@if (!Auth::check())
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">

	<style media="screen">
		body,
	    #voten-app{
	        font-family: 'Lato', sans-serif;
	    }

	    <?php
    		$colours = collect([
				'Gray',
				'Dark Blue',
				'Purple',
				'Gray',
				'Blue'
    		]);

	    	$sidebar_color = $colours->random();
	    ?>

	    @if($sidebar_color == 'Green')
	    	.side-fixed {
				background: linear-gradient(#455d58, #37524d);
	    	}
	    	.v-side-quick-actions i:hover {
			    background: #edb431;
			    color: #333;
			}
			.menu-list a:hover {
			    background-color: rgba(204, 204, 204, 0.12);
			    background: #edb431;
			    color: #333 !important;
			}
	    @endif

	    @if($sidebar_color == 'Purple')
	    	.side-fixed {
				background: linear-gradient(#4d4261, #4d4463);
	    	}
	    	.v-side-quick-actions i:hover {
			    background: #edb431;
			    color: #333;
			}
			.menu-list a:hover {
			    background-color: rgba(204, 204, 204, 0.12);
			    background: #edb431;
			    color: #333 !important;
			}
	    @endif

	    @if($sidebar_color == 'Dark')
	    	.side-fixed {
				background: linear-gradient(#3e3e3e, #3c3c3c);
	    	}
	    	.v-side-quick-actions i:hover {
			    background-color: #d2d2d2;
			    color: #333 !important;
			}
			.menu-list a:hover {
			    background-color: #d2d2d2;
				color: #333 !important;
			}
	    @endif

	    @if($sidebar_color == 'Red')
	    	.side-fixed {
				background: linear-gradient(#673e3d, #523636);
	    	}
	    	.v-side-quick-actions i:hover {
			    background-color: #5b7368;
			    color: #fff;
			}
			.menu-list a:hover {
			    background-color: #5b7368;
			    color: #fff !important;
			}
	    @endif

	    @if($sidebar_color == 'Dark Blue')
	    	.side-fixed {
				background: linear-gradient(#323e4c, #25313e);
	    	}
	    	.v-side-quick-actions i:hover {
			    background: #7197c3;
			    color: #fff;
			}
			.menu-list a:hover {
			    background-color: rgba(204, 204, 204, 0.12);
			    background: #7197c3;
			    color: #fff !important;
			}
	    @endif

	    @if($sidebar_color == 'Gray')
	    	.side-fixed{
	    		background: -webkit-linear-gradient(#f6f6f6, #ffffff);
				background: linear-gradient(#f6f6f6, #ffffff);
				color: #333;
				/*border-right: 2px solid #dcdcdc;*/
	    	}
	    	.side-fixed a, .side-fixed a:hover, .side-fixed a:focus {
	    		color: #333;
	    	}
			.sidebar-avatar img{
	        	border-color: rgba(199, 199, 199, 0.63) rgba(189, 189, 189, 0.68) rgba(211, 211, 211, 0.95);
	        }
	        .menu-list a:hover{
	        	background-color: #92989f;
	        }
	        .side-fixed hr{
				border-top: 1px solid rgba(35, 35, 35, 0.14);
	        }
	        .v-side aside .ui.icon.input input {
				color: #333;
				border: 1.5px dashed rgba(0, 0, 0, 0.38);
	        }
	        .side-fixed .ui.search .prompt ~ .search.icon {
			    color: #333;
			}
			.sidebar-offer-wrapper {
				border: 1px solid rgba(212, 207, 207, 0.62);
			}
			.sidebar-panel-button i:hover {
				color: #000;
			}
	    @endif
	</style>
@else
	<link href="https://fonts.googleapis.com/css?family={{ title_case(str_slug(settings('font'), '+')) }}:300,400,700" rel="stylesheet">

	<style media="screen">
	    body,
	    #voten-app{
	        font-family: '{{ settings('font') }}', sans-serif;
	    }


	    @if(settings('sidebar_color') == 'Green')
	    	.side-fixed {
				background: linear-gradient(#455d58, #37524d);
	    	}
	    	.v-side-quick-actions i:hover {
			    background: #edb431;
			    color: #333;
			}
			.menu-list a:hover {
			    background-color: rgba(204, 204, 204, 0.12);
			    background: #edb431;
			    color: #333 !important;
			}
	    @endif

	    @if(settings('sidebar_color') == 'Purple')
	    	.side-fixed {
				background: linear-gradient(#4d4261, #4d4463);
	    	}
	    	.v-side-quick-actions i:hover {
			    background: #edb431;
			    color: #333;
			}
			.menu-list a:hover {
			    background-color: rgba(204, 204, 204, 0.12);
			    background: #edb431;
			    color: #333 !important;
			}
	    @endif

	    @if(settings('sidebar_color') == 'Dark')
	    	.side-fixed {
				background: linear-gradient(#3e3e3e, #3c3c3c);
	    	}
	    	.v-side-quick-actions i:hover {
			    background-color: #d2d2d2;
			    color: #333 !important;
			}
			.menu-list a:hover {
			    background-color: #d2d2d2;
				color: #333 !important;
			}
	    @endif

	    @if(settings('sidebar_color') == 'Red')
	    	.side-fixed {
				background: linear-gradient(#673e3d, #523636);
	    	}
	    	.v-side-quick-actions i:hover {
			    background-color: #5b7368;
			    color: #fff;
			}
			.menu-list a:hover,
			.menu-list .active {
			    background-color: #5b7368;
			    color: #fff !important;
			}
	    @endif

	    @if(settings('sidebar_color') == 'Dark Blue')
	    	.side-fixed {
				background: linear-gradient(#323e4c, #25313e);
	    	}
	    	.v-side-quick-actions i:hover {
			    background: #7197c3;
			    color: #fff;
			}
			.menu-list a:hover,
			.menu-list .active {
			    background-color: rgba(204, 204, 204, 0.12);
			    background: #7197c3;
			    color: #fff !important;
			}
	    @endif

	    @if(settings('sidebar_color') == 'Gray')
	    	.side-fixed{
	    		background: -webkit-linear-gradient(#f6f6f6, #ffffff);
				background: linear-gradient(#f6f6f6, #ffffff);
				color: #333;
				/*border-right: 2px solid #dcdcdc;*/
	    	}
	    	.side-fixed a, .side-fixed a:hover, .side-fixed a:focus {
	    		color: #333;
	    	}
			.sidebar-avatar img{
	        	border-color: rgba(199, 199, 199, 0.63) rgba(189, 189, 189, 0.68) rgba(211, 211, 211, 0.95);
	        }
	        .menu-list a:hover{
	        	background-color: #92989f;
	        }
	        .side-fixed hr{
				border-top: 1px solid rgba(35, 35, 35, 0.14);
	        }
	        .v-side aside .ui.icon.input input {
				color: #333;
				border: 1.5px dashed rgba(0, 0, 0, 0.38);
	        }
	        .side-fixed .ui.search .prompt ~ .search.icon {
			    color: #333;
			}
	    @endif
	</style>
@endif


