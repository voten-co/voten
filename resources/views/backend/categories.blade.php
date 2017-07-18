@extends('layouts.backend-layout')

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


                <table class="table is-striped">
                    <thead>
                        <tr>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Subscribers</th>
                            <th>Created By</th>
                            <th>Created At</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach ($categories as $c)
                        <tr>
                            <td>
                                <a href="/c/{{ $c->name }}" target="_blank">
                                    <img src="{{ $c->avatar }}" class="image is-32x32" alt="{{ $c->name }}">
                                </a>
                            </td>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->subscribers }}</td>
                            <td>
                                <a href="/{{ '@' . $c->creator()->username }}">
                                    {{ $c->creator()->username }}
                                </a>
                            </td>
                            <td>{{ $c->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @if(get_class($categories) === 'Illuminate\Pagination\LengthAwarePaginator')
                    {{ $categories->links() }}
                @endif
            </div>
        </div>
    </section>

@endsection
