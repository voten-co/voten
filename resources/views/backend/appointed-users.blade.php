@extends('layouts.backend-layout')

@section('content')

<section class="section container">
    <div class="columns is-multiline is-mobile">
        <div class="column is-half">
            <h1 class="title">Appointed Users:</h1>

            <form class="field has-addons" action="/appointed/store" method="post">
                {{ csrf_field() }}

                <p class="control">
                    <span class="select">
                        <select name="appointed_as">
                            <option>whitelisted</option>
                            <option>administrator</option>
                        </select>
                    </span>
                </p>

                <p class="control is-expanded">
                    <input class="input is-expanded" type="text" placeholder="username" name="username" required>
                </p>

                <p class="control">
                    <button class="button is-primary" type="submit">
                        Add
                    </button>
                </p>
            </form>

            <table class="table is-striped is-fullwidth">
                <thead>
                    <tr>
                        <th>username</th>
                        <th>Appointed As</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($appointed_users as $appointed)
                        <tr>
                            <td>
                                @if (isset($appointed->user))
                                    <a href="/{{ '@' . $appointed->user->username }}" target="_blank">
                                        {{ '@' . $appointed->user->username }}
                                    </a>
                                @else
                                    <a href="/{{ '@' . $appointed }}" target="_blank">
                                        {{ '@' . $appointed }}
                                    </a>
                                @endif
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
