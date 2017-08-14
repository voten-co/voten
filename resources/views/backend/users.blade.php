@extends('layouts.backend-layout')

@section('title')
    Users
@endsection

@section('content')

    <section class="section container">
        <div class="columns is-multiline is-mobile">
            <div class="column is-full">
                <h1 class="title">
                    Recently Registered Users
                </h1>

                <form class="field">
                    <p class="control has-icons-left">
                        <input class="input" type="text" name="filter" id="filter"
                           placeholder="Search by #username or name" value="{{ request('filter') }}">

                        <span class="icon is-left">
                            <i class="fa fa-search"></i>
                        </span>
                    </p>
                </form>


                <table class="table is-striped is-fullwidth">
                    <thead>
                    <tr>
                        <th>avatar</th>
                        <th>username</th>
                        <th>name</th>
                        <th>email</th>
                        <th>location</th>
                        <th>country</th>
                        <th>Activities</th>
                        <th>last activiy</th>
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
                            <td>
                                <a href="/backend/users/{{ $user->username }}">
                                    {{ $user->username }}
                                </a>
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->location }}</td>
                            <td>
                                <span class="tag">
                                    {{ $user->country() }}
                                </span>
                            </td>
                            <td>
                                @if($user->activities()->count() > 1)
                                    <span class="tag is-warning">
                                        {{ $user->activities()->count() }}
                                    </span>
                                @else
                                    <span class="tag is-danger">
                                        never
                                    </span>
                                @endif

                            </td>
                            <td>
                                <span class="tag">
                                    {{ $user->lastActivity() ? $user->lastActivity()->created_at->diffForHumans() : 'Never' }}
                                </span>
                            </td>
                            <td><span class="tag is-white">{{ $user->created_at->diffForHumans() }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @if(get_class($users) === 'Illuminate\Pagination\LengthAwarePaginator')
                    {{ $users->links() }}
                @endif
            </div>
        </div>
    </section>

@endsection
