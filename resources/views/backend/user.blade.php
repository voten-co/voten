@extends('layouts.backend-layout')

@section('title')
    {{ '@' . $user->username }} settings
@endsection

@section('content')
    @if($user->isShadowBanned())
        <section class="section container">
            <article class="message is-warning">
                <div class="message-body">
                    <div class="flex-space">
                        <div>
                            <h3><strong>{{ '@' . $user->username }} is banned!</strong></h3>

                            <p>
                                Just a reminder to let you know that {{ '@' . $user->username }} is banned for x days.
                            </p>
                        </div>

                        <div>
                            <form action="/ban-user/destroy" method="post">
                                {{ csrf_field() }}
                                {{ method_field('delete') }}

                                <input type="hidden" name="channel" value="all">

                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                <button type="submit" class="button is-warning">
                                    UnBan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    @endif

    <section class="section container">
        <div class="columns">
            <div class="column is-one-quarter align-center">
                <h1 class="title">
                    <a href="/{{ '@' . $user->username }}" target="_blank">
                        {{ '@' . $user->username }}
                    </a>
                </h1>

                <h4 class="title">
                    {{ $user->name }}
                </h4>

                <br>

                <img width="200" src="{{ $user->avatar }}" alt="{{ $user->username }}">
            </div>

            <div class="column">
                <h2 class="title">
                    Info:
                </h2>

                <table class="table is-striped is-fullwidth">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td>Joined At</td>
                        <td class="align-right">
                                <span class="tag">
                                    {{ $user->created_at->diffForHumans() }}
                                </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Subscriptions</td>
                        <td class="align-right">
                            {{ $user->subscriptions()->count() }}
                        </td>
                    </tr>
                    <tr>
                        <td>Submissions Number</td>
                        <td class="align-right">
                            {{ $user->submissions()->count() }}
                        </td>
                    </tr>
                    <tr>
                        <td>Comments Number</td>
                        <td class="align-right">
                            {{ $user->comments()->count() }}
                        </td>
                    </tr>
                    <tr>
                        <td>Messages</td>
                        <td class="align-right">
                            {{ $user->messages()->count() }}
                        </td>
                    </tr>
                    <tr>
                        <td>Submission Xp</td>
                        <td class="align-right">
                            {{ $user->stats['submission_xp'] }}
                        </td>
                    </tr>
                    <tr>
                        <td>Comment Xp</td>
                        <td class="align-right">
                            {{ $user->stats['comment_xp'] }}
                        </td>
                    </tr>
                    <tr>
                        <td>Bio</td>
                        <td class="align-right">
                            {{ $user->bio }}
                        </td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td class="align-right">
                            {{ $user->name }}
                        </td>
                    </tr>
                    <tr>
                        <td>Location</td>
                        <td class="align-right">
                            {{ $user->location }}
                        </td>
                    </tr>
                    <tr>
                        <td>Last Activiy</td>
                        <td class="align-right">
                            {{ $user->lastActivity() ? $user->lastActivity()->created_at->diffForHumans() : 'Never' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Activities</td>
                        <td class="align-right">
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
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td class="align-right">
                            <span class="tag">
                                {{ $user->country() }}
                            </span>
                        </td>
                    </tr>

                    </tbody>
                </table>

                {{--<hr>--}}

                {{--<h2 class="title">--}}
                    {{--Rules:--}}
                {{--</h2>--}}

                {{--<div class="content">--}}
                    {{--@if($user->rules()->count())--}}
                        {{--<ol>--}}
                            {{--@foreach($user->rules as $rule)--}}
                                {{--<li>{{ $rule->title }}</li>--}}
                            {{--@endforeach--}}
                        {{--</ol>--}}
                    {{--@else--}}
                        {{--This Channel has no exclusive rules.--}}
                    {{--@endif--}}
                {{--</div>--}}

                <hr>

                <h2 class="title">
                    Ban User:
                </h2>

                <div class="flex-space">
                    <p>
                        Ban <a href="/{{ '@' . $user->username }}" target="_blank">{{ '@' . $user->username }}</a> from submitting submissions or/and comments to entire application.
                    </p>

                    @if(!$user->isShadowBanned())
                        <form class="field has-addons" action="/ban-user" method="post">
                            {{ csrf_field() }}

                            <p class="control">
                            <span class="select">
                                <select name="duration" class="is-expanded">
                                    <option value="3">3 days</option>
                                    <option value="1">1 days</option>
                                    <option value="7">7 days</option>
                                    <option value="30">1 month</option>
                                    <option value="90">3 months</option>
                                    <option value="0">Forever</option>
                                </select>
                            </span>
                            </p>

                            <input type="hidden" value="{{ $user->username }}" name="username">

                            <input type="hidden" value="all" name="channel">

                            <p class="control">
                                <button class="button is-primary" type="submit">
                                    Ban {{ '@' . $user->username }}
                                </button>
                            </p>
                        </form>
                    @endif
                </div>


                <hr>

                <h2 class="title">
                    Delete User:
                </h2>

                <div class="field">
                    <p>
                        Deleting a user would delete all its records and can not be undone. It also requires your (adminstrator's) password.
                    </p>
                </div>

                <form action="/backend/users/destroy" method="post">
                    {{ csrf_field()  }}
                    {{ method_field('delete') }}

                    <div class="field has-addons">
                        <div class="control flex1">
                            <input class="input" type="password" placeholder="Your password to confirm..." required name="password">
                        </div>

                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        <div class="control">
                            <button type="submit" class="button is-danger">
                                Delete {{ '@' . $user->username }}
                            </button>
                        </div>
                    </div>
                </form>

                {{--<hr>--}}

                {{--<h2 class="title">--}}
                    {{--Take Over--}}
                {{--</h2>--}}

                {{--@if($isAdministrator)--}}
                    {{--<i class="field">--}}
                        {{--You're already an administrator of this channel.--}}
                    {{--</i>--}}
                {{--@else--}}
                    {{--<p class="field">--}}
                        {{--This'll make you an administrator of the channel so you can take necessary actions.--}}
                    {{--</p>--}}

                    {{--<form action="/backend/channels/{{ $user->id }}/takeover" method="post">--}}
                        {{--{{ csrf_field() }}--}}

                        {{--<div class="field has-addons">--}}
                            {{--<div class="control">--}}
                                {{--<button type="submit" class="button is-primary">--}}
                                    {{--Take Over #{{ $user->username }}--}}
                                {{--</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                {{--@endif--}}
            </div>
        </div>
    </section>



@endsection