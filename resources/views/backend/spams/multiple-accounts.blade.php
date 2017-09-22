@extends('layouts.backend-layout')

@section('title')
    Multiple Accounts | Spams
@endsection

@section('content')
    <section class="section container is-fluid">
        @include('backend.spams.header')

        <div class="columns is-multiline is-mobile">
            <div class="column is-full">
                <h1 class="title">
                    Users Registered with same IP more than once
                </h1>

                <table class="table is-striped is-fullwidth">
                    <thead>
                    <tr>
                        <th>avatar</th>
                        <th>username</th>
                        <th>name</th>
                        <th>email</th>
                        <th>IP Address</th>
                        <th>Country</th>
                        <th>Activities</th>
                        <th>last activiy</th>
                        <th>registered at</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($groupedByIpUsers as $key => $group)
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <span class="tag is-primary">{{ $key }}</span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach($group as $user)
                            <tr>
                                <td>
                                    <a href="/{{ '@' . $user->username }}" target="_blank">
                                        <img src="{{ $user->avatar }}" class="image is-32x32" alt="{{ $user->username }}">
                                    </a>
                                </td>
                                <td>
                                    <a href="/backend/users/{{ $user->username }}">{{ $user->username }}</a>
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="tag">
                                        {{ $user->ip }}
                                    </span>
                                </td>
                                <td>
                                    <span class="tag is-white">
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
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection


