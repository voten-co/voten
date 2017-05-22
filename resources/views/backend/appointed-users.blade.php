@extends('layouts.backend-layout')

@section('content')

<section class="section container">
    <div class="columns is-multiline is-mobile">
        <div class="column is-half">
            <h1 class="title">Appointed Users:</h1>

            <form class="control has-addons" action="/appointed/store" method="post">
                {{ csrf_field() }}

                <span class="select">
                    <select name="appointed_as">
                        <option>whitelisted</option>
                        <option>administrator</option>
                    </select>
                </span>

                <input class="input is-expanded" type="text" placeholder="username" name="username" required>

                <button class="button is-primary" type="submit">
                    Add
                </button>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th>username</th>
                        <th>Appointed As</th>
                        <th>Actions</th>
                        <th>Login</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($appointed_users as $appointed)
                        <tr>
                            <td>
                                <a href="/{{ '@' . $appointed->user->username }}" target="_blank">
                                    {{ '@' . $appointed->user->username }}
                                </a>
                            </td>
                            <td>
                                <span class="tag">{{ $appointed->appointed_as }}</span>
                            </td>
                            <td>
                                <form action="/appointed/destroy/{{ $appointed->id }}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}

                                    <button class="button is-danger is-small" type="submit">
                                        Delete
                                    </button>
                                </form>
                            </td>
                            <td>
                                <a class="button is-info is-small" href="/login-as?username={{ $appointed->user->username }}">
                                    Log In As
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="column is-half">
            {{-- block of code --}}
        </div>

    </div>
</section>




@endsection
