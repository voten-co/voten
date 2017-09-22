@extends('layouts.backend-layout')

@section('title')
    Reproted Submissions | Spams
@endsection

@section('content')
    <section class="section container is-fluid">
        @include('backend.spams.header')

        <div class="columns is-multiline is-mobile">
            <div class="column is-full">
                <div class="flex-space">
                    <h1 class="title">
                        Reported Submissions
                    </h1>

                    <div>
                        <a class="button is-light {{ !request()->filled('type') ? 'is-dark' : '' }}" href="{{ url('backend/spams/submissions') }}">
                            Un-solved
                        </a>

                        <a class="button is-light {{ request()->filled('type') == 'solved' ? 'is-dark' : '' }}" href="?type=solved">
                            Solved
                        </a>
                    </div>
                </div>

                <table class="table is-striped is-fullwidth">
                    <thead>
                    <tr>
                        <th>Submission Title</th>
                        <th>Author</th>
                        <th>Reporter</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Reproted At</th>
                        <th>{{-- Actions --}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td>
                                <a href="{{ optional($report->submission)->url() }}" data-toggle="tooltip" data-placement="top" title="{{ optional($report->submission)->title }}" target="_blank">
                                    {{ str_limit(optional($report->submission)->title, 80) }}
                                </a>
                            </td>

                            <td>
                                <a href="/backend/users/{{ optional($report->submission)->owner->username }}" target="_blank">
                                    <img src="{{ optional($report->submission)->owner->avatar }}" class="margin-right-half" width="35" data-toggle="tooltip" data-placement="top" title="{{ optional($report->submission)->owner->username }}">
                                    {{ optional($report->submission)->owner->username }}
                                </a>
                            </td>

                            <td>
                                <a href="/{{ '@' . optional($report->reporter)->username }}" target="_blank">
                                    <img src="{{ optional($report->reporter)->avatar }}" class="margin-right-half" width="35" data-toggle="tooltip" data-placement="top" title="{{ optional($report->reporter)->username }}">
                                    {{ optional($report->reporter)->username }}
                                </a>
                            </td>

                            <td>
                                <span class="tag is-warning" data-toggle="tooltip" data-placement="top" title="{{ $report->subject }}">{{ str_limit($report->subject, 20) }}</span>
                            </td>

                            <td>
                                @if($report->description)
                                    <button class="button is-info is-small" data-toggle="tooltip" data-placement="top" title="{{ $report->description }}">
                                        Description
                                    </button>
                                @endif
                            </td>

                            <td><span class="tag">{{ $report->created_at->diffForHumans() }}</span></td>

                            <td>
                                @unless(optional($report->submission)->deleted_at)
                                    <form action="/disapprove-submission" method="post" class="display-inline">
                                        {{ csrf_field() }}

                                        <input type="hidden" name="submission_id" value="{{ optional($report->submission)->id }}">

                                        <button class="button is-danger is-small" type="submit" data-toggle="tooltip" data-placement="top" title="Delete">
                                            <i class="fa fa-ban" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @endif

                                @unless(optional($report->submission)->approved_at)
                                    <form action="/approve-submission" method="post" class="display-inline">
                                        {{ csrf_field() }}

                                        <input type="hidden" name="submission_id" value="{{ optional($report->submission)->id }}">

                                        <button class="button is-success is-small" type="submit" data-toggle="tooltip" data-placement="top" title="Approve">
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{ $reports->links() }}
            </div>
        </div>
    </section>
@endsection
