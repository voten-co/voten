@extends('layouts.backend-layout')

@section('title')
    Channels
@endsection

@section('content')

    <section class="section container">
        <div class="columns is-multiline is-mobile">
            <div class="column is-full">
                <h1 class="title">
                    Recently Created Channels
                </h1>

                <form class="field">
                    <p class="control has-icons-left">
                        <input class="input" type="text" name="filter" id="filter"
                           placeholder="Search by #name or description" value="{{ request('filter') }}">

                        <span class="icon is-left">
                            <i class="fa fa-search"></i>
                        </span>
                    </p>
                </form>


                <table class="table is-striped is-fullwidth">
                    <thead>
                        <tr>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>
                                <a href="{{ url()->current() }}?sort_by=subscribers">
                                    Subscribers
                                </a>
                            </th>
                            <th>
                                <a href="{{ url()->current() }}?sort_by=submissions_count">
                                    Submissions
                                </a>
                            </th>
                            <th>
                                <a href="{{ url()->current() }}?sort_by=comments_count">
                                    Comments
                                </a>
                            </th>
                            <th>Founder</th>
                            <th>Created At</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach ($channels as $c)
                        <tr>
                            <td>
                                <a href="/c/{{ $c->name }}" target="_blank">
                                    <img src="{{ $c->avatar }}" class="image is-32x32" alt="{{ $c->name }}">
                                </a>
                            </td>
                            <td>
                                <a href="/backend/channels/{{ $c->name }}">
                                    {{ $c->name }}
                                </a>
                            </td>
                            <td>{{ $c->subscribers }}</td>
                            <td>{{ $c->submissions()->count() }}</td>
                            <td>{{ $c->comments()->count() }}</td>
                            <td>
                                @if($c->createdByUsername() != 'deleted')
                                    <a href="/backend/users/{{ $c->createdByUsername() }}">
                                        {{ $c->createdByUsername() }}
                                    </a>
                                @else
                                    <span class="tag is-danger">deleted</span>
                                @endif
                            </td>
                            <td><span class="tag">{{ $c->created_at->diffForHumans() }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @if(get_class($channels) === 'Illuminate\Pagination\LengthAwarePaginator')
                    {{ $channels->links() }}
                @endif
            </div>
        </div>
    </section>

@endsection
