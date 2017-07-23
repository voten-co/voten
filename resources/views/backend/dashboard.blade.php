@extends('layouts.backend-layout')

@section('title')
    Admin Panel
@endsection

@section('content')

<section class="section container">
    <div class="columns is-multiline is-mobile">
        <div class="column is-half statistics">
            <h1 class="title">
                Real-Time Server
            </h1>

            <div class="statistic red-back">
                <div>
                    Online Connections:
                </div>

                <div class="panel-number-big">
                    20
                </div>
            </div>
            <div class="statistic green-back">
                <div>
                    Memory Usage:
                </div>

                <div class="panel-number-big">
                    <span>90</span>
                    <span class="des">MB</span>
                </div>
            </div>
            <div class="statistic blue-back">
                <div>
                    Uptime:
                </div>

                <div class="panel-number-big">
                    <span>33</span>
                    <span class="des">Days</span>
                </div>
            </div>
        </div>

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
                            Registers
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
                            Active Users
                        </td>
                        <td>
                            {{ $activeUsersToday }}
                        </td>
                        <td>
                            {{ $activeUsersTotal }}
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
    </div>

	<br>

    <div class="columns is-multiline is-mobile">
    	<div class="column is-full">
            <h1 class="title">
                Latest Activities
            </h1>

            <table class="table is-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>By</th>
                    <th>Country</th>
                    <th>At</th>
                </tr>
                </thead>

                <tbody>
                    @foreach($activities as $activity)
                        <tr>
                            <td>
                                {{ $activity->name }}
                            </td>
                            <td>
                                <a href="/{{ '@' . $activity->owner->username }}">
                                    <img width="30" class="margin-right-half" src="{{ $activity->owner->avatar }}"
                                         alt="{{ $activity->owner->username }}">
                                    {{ $activity->owner->username }}
                                </a>
                            </td>
                            <td>
                                <span class="tag">{{ $activity->country ?? 'unknown' }}</span>
                            </td>
                            <td>
                                <span class="tag">{{ $activity->created_at->diffForHumans() }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    	</div>
    </div>
</section>

@endsection
