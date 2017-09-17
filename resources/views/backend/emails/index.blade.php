@extends('layouts.backend-layout')

@section('title')
    Emails
@endsection

@section('content')

<section class="section container">
    <div class="columns is-multiline is-mobile">
        <div class="column is-full">
            <div class="flex-space">
                <h1 class="title">New Announcement Email</h1>

                <div>
                    @if(cache('announcement-email:body'))
                        <div class="flex-space">
                            <div class="margin-right-half">
                                <a class="button is-warning" href="/emails/announcement/preview" target="_blank">
                                    Preview
                                </a>
                            </div>

                            <div>
                                <form action="/emails/announcement/send" method="post">
                                    {{ csrf_field()  }}

                                    <div class="field has-addons">
                                        <div class="control flex1">
                                            <input class="input" type="password" placeholder="Password to confirm..." required name="password">
                                        </div>

                                        <div class="control">
                                            <button type="submit" class="button is-danger">
                                                Send Emails
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <br>


            <form class="control" action="/emails/announcement/store" method="post">
                {{ csrf_field() }}

                <div class="field">
                    <p class="control">
                        <input class="input" type="text" placeholder='Email Subject' name="title" required>
                    </p>
                </div>

                <div class="field">
                    <p class="control">
                        <input class="input" type="text" placeholder='Heading (Leave empty for "hey @username")' name="heading">
                    </p>
                </div>

                <div class="field">
                    <p class="control">
                        <textarea class="textarea" placeholder="Content (markdown syntax)" name="body" required></textarea>
                    </p>
                </div>

                <div class="field">
                    <p class="control">
                        <input class="input" type="text" placeholder='Button Text (Leave empty for no button)' name="button_text">
                    </p>
                </div><div class="field">
                    <p class="control">
                        <input class="input" type="text" placeholder='Button URL (Leave empty for no button)' name="button_url">
                    </p>
                </div>

                <div class="field">
                    <div class="control">
                        <button class="button is-primary" type="submit">
                            Submit
                        </button>
                    </div>
                </div>
            </form>

            {{--<br>--}}
            {{--<hr>--}}
            {{--<br>--}}

            {{--<h1 class="title">Custom Email</h1>--}}

            {{--<p>--}}
                {{--A custom email is hardcodded every time we intend to send it. The reason being, it usually has different fields or maybe it's being sent to--}}
                {{--a group of users and not all of them.--}}
            {{--</p>--}}

            {{--<br>--}}

            {{--<button class="button is-primary" type="submit">--}}
                {{--Send--}}
            {{--</button>--}}

            {{--<br>--}}
        </div>
    </div>
</section>

@endsection
