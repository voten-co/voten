@extends('layouts.backend-layout')

@section('title')
    #{{ $channel->name }}
@endsection

@section('content')

    <section class="section container">
        <div class="columns">
            <div class="column is-one-quarter align-center">
                <h1 class="title">
                    <a href="/c/{{ $channel->name }}" target="_blank">
                        #{{ $channel->name }}
                    </a>
                </h1>

                <br>

                <img width="200" src="{{ $channel->avatar }}" alt="{{ $channel->name }}">
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
                                <a href="/{{ '@' . optional($channel->creator())->username }}">
                                    {{ '@' . optional($channel->creator())->username }}
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td>Created At</td>
                            <td class="align-right">
                                <span class="tag">
                                    {{ $channel->created_at->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Subscribers</td>
                            <td class="align-right">
                                {{ $channel->subscribers }}
                            </td>
                        </tr>
                        <tr>
                            <td>Submissions Number</td>
                            <td class="align-right">
                                {{ $channel->submissions()->count() }}
                            </td>
                        </tr>
                        <tr>
                            <td>Comments Number</td>
                            <td class="align-right">
                                {{ $channel->comments()->count() }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>

                <h2 class="title">
                    Rules:
                </h2>

                <div class="content">
                    @if($channel->rules()->count())
                        <ol>
                            @foreach($channel->rules as $rule)
                                <li>{{ $rule->title }}</li>
                            @endforeach
                        </ol>
                    @else
                        This Channel has no exclusive rules.
                    @endif
                </div>
            </div>
        </div>
    </section>



@endsection