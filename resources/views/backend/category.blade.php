@extends('layouts.backend-layout')

@section('title')
    #{{ $category->name }}
@endsection

@section('content')

    <section class="section container">
        <div class="columns">
            <div class="column is-one-quarter align-center">
                <h1 class="title">
                    <a href="/c/{{ $category->name }}">
                        #{{ $category->name }}
                    </a>
                </h1>

                <br>

                <img width="200" src="{{ $category->avatar }}" alt="{{ $category->name }}">
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
                            <td>Created By</td>
                            <td class="align-right">
                                <a href="/{{ '@' . $category->creator()->username }}">
                                    {{ '@' . $category->creator()->username }}
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td>Created At</td>
                            <td class="align-right">
                                <span class="tag">
                                    {{ $category->created_at->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Subscribers</td>
                            <td class="align-right">
                                {{ $category->subscribers }}
                            </td>
                        </tr>
                        <tr>
                            <td>Submissions Number</td>
                            <td class="align-right">
                                {{ $category->submissions()->count() }}
                            </td>
                        </tr>
                        <tr>
                            <td>Comments Number</td>
                            <td class="align-right">
                                {{ $category->comments()->count() }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>

                <h2 class="title">
                    Rules:
                </h2>

                <div class="content">
                    @if($category->rules()->count())
                        <ol>
                            @foreach($category->rules as $rule)
                                <li>{{ $rule->title }}</li>
                            @endforeach
                        </ol>
                    @else
                        This Channel has no exclusive rules.
                    @endif
                </div>

                <hr>

                <h2 class="title">
                    Delete Channel:
                </h2>

                <div class="field">
                    <p>
                        Deleting a channel would delete all its records and can not be undone. It also requires your (adminstrator's) password.
                    </p>
                </div>

                <form action="/backend/channels/{{ $category->id }}/destroy" method="post">
                    {{ csrf_field()  }}
                    {{ method_field('delete') }}

                    <div class="field has-addons">
                        <div class="control flex1">
                            <input class="input" type="password" placeholder="Your password to confirm..." required name="password">
                        </div>

                        <div class="control">
                            <button type="submit" class="button is-danger">
                                Delete #{{ $category->name }}
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <h2 class="title">
                    Take Over
                </h2>

                @if($isAdministrator)
                    <i class="field">
                        You're already an administrator of this channel.
                    </i>
                @else
                    <p class="field">
                        This'll make you an administrator of the channel so you can take necessary actions.
                    </p>

                    <form action="/backend/channels/{{ $category->id }}/takeover" method="post">
                        {{ csrf_field() }}

                        <div class="field has-addons">
                            <div class="control">
                                <button type="submit" class="button is-primary">
                                    Take Over #{{ $category->name }}
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>



@endsection