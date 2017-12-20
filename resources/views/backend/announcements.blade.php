@extends('layouts.backend-layout')

@section('title')
    Announcements
@endsection

@section('content')

<section class="section container">
    <div class="columns is-multiline is-mobile">
        <div class="column is-full">
            <h1 class="title">New inside announcement:</h1>

			<form class="control" action="/create-announcement" method="post">
                {{ csrf_field() }}

                <div class="field">
					<p class="control">
						<input class="input" type="text" placeholder="Title(not displayed to users)" name="title" required>
					</p>
				</div>

				<div class="field">
					<p class="control">
						<textarea class="textarea" placeholder="Body(markdown syntax)" name="body" required></textarea>
					</p>
				</div>

				<div class="field">
					<p class="control">
						<input class="input" type="number" placeholder="Duration in days (0 = forever)" name="duration" value="3" required>
					</p>
				</div>

				<input type="hidden" name="channel_name" value="home">

				<div class="field">
					<div class="control">
						<button class="button is-primary" type="submit">
							Submit
						</button>
					</div>
				</div>
            </form>

            <br>
            <hr>

            <h1 class="title">
                All Announcements:
            </h1>


            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Body</th>
                        <th>Created</th>
                        <th>Active for</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($announcements as $announcement)
                        <tr>
                            <td>
                                {{ $announcement->title }}
                            </td>
                            <td>
                                {{ $announcement->body }}
                            </td>
                            <td>
                                {{ $announcement->created_at->diffForHumans() }}
                            </td>
                            <td>
                                {{ $announcement->active_until->diffForHumans() }}
                            </td>
                            <td>
                                <form action="/announcement/destroy/{{ $announcement->id }}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}

                                    <button class="button is-danger is-small" type="submit">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
