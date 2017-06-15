@extends('layouts.backend-layout')

@section('content')

<section class="section container">
    <div class="columns is-multiline is-mobile">
        <div class="column is-half">
        	<h1 class="title">
        		Statistics
        	</h1>

			<table class="table is-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Today</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            Users
                        </td>
                        <td>
                            {{ $usersToday }}
                        </td>
                        <td>
                            {{ $usersTotal }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Submissions
                        </td>
                        <td>
                            {{ $submissionsToday }}
                        </td>
                        <td>
                            {{ $submissionsTotal }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Comments
                        </td>
                        <td>
                            {{ $commentsToday }}
                        </td>
                        <td>
                            {{ $commentsTotal }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Channels
                        </td>
                        <td>
                            {{ $categoriesToday }}
                        </td>
                        <td>
                            {{ $categoriesTotal }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Messages
                        </td>
                        <td>
                            {{ $messagesToday }}
                        </td>
                        <td>
                            {{ $messagesTotal }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Reports
                        </td>
                        <td>
                            {{ $reportsToday }}
                        </td>
                        <td>
                            {{ $reportsTotal }}
                        </td>
                    </tr>

                	<tr>
                        <td>
                            Submission Votes
                        </td>
                        <td>
                            {{ $submissionVotesToday }}
                        </td>
                        <td>
                            {{ $submissionVotesTotal }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Comment Votes
                        </td>
                        <td>
                            {{ $commentVotesToday }}
                        </td>
                        <td>
                            {{ $commentVotesTotal }}
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="column is-half">
			{{--  --}}
        </div>
    </div>

	<br>

    <div class="columns is-multiline is-mobile">
    	<div class="column is-full">
    		<h1 class="title">
				Latest Users:
			</h1>

			<table class="table is-striped">
                <thead>
                    <tr>
                    	<th>avatar</th>
                        <th>username</th>
                        <th>name</th>
                        <th>email</th>
                        <th>location</th>
                        <th>country</th>
                        <th>registered at</th>
                    </tr>
                </thead>

                <tbody>
                	@foreach ($users as $user)
                		<tr>
	                        <td>
	                        	<a href="/{{ '@' . $user->username }}" target="_blank">
	                        		<img src="{{ $user->avatar }}" class="image is-32x32" alt="{{ $user->username }}">
	                        	</a>
	                        </td>
	                        <td>{{ $user->username }}</td>
	                        <td>{{ $user->name }}</td>
	                        <td>{{ $user->email }}</td>
	                        <td>{{ $user->location }}</td>
	                        <td><span class="tag">{{ $user->country() }}</span></td>
	                        <td>{{ $user->created_at->diffForHumans() }}</td>
	                    </tr>
                	@endforeach
                </tbody>
            </table>
    	</div>
    </div>
</section>

@endsection
