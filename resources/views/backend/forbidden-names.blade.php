@extends('layouts.backend-layout')

@section('content')

<section class="section container">
    <div class="columns is-multiline is-mobile">
        <div class="column is-half">
            <h1 class="title">Forbidden Channel Names:</h1>

            <form class="control has-addons" action="/forbidden-category-name/store" method="post">
                {{ csrf_field() }}

                <div class="field has-addons">
                    <div class="control flex1">
                        <input class="input" type="text" placeholder="name..." required name="name">
                    </div>

                    <div class="control">
                        <button class="button is-primary" type="submit">
                            Add
                        </button>
                    </div>
                </div>
            </form>

            <br>

            <table class="table is-fullwidth table is-striped">
                <thead>
                    <tr>
                        <th>username</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($forbiddenCategoryNames as $forbiddenCategoryName)
                        <tr>
                            <td>
                                {{ $forbiddenCategoryName->name }}
                            </td>
                            <td>
                                <form action="/forbidden-category-name/destroy/{{ $forbiddenCategoryName->id }}" method="post">
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

            {{ $forbiddenCategoryNames->links() }}
        </div>

        <div class="column is-half">
            <h1 class="title">Forbidden usernames:</h1>

            <form class="control has-addons" action="/forbidden-username/store" method="post">
                {{ csrf_field() }}

                <div class="field has-addons">
                    <div class="control flex1">
                        <input class="input" type="text" placeholder="username..." required name="username">
                    </div>

                    <div class="control">
                        <button class="button is-primary" type="submit">
                            Add
                        </button>
                    </div>
                </div>
            </form>

            <br>

            <table class="table is-fullwidth table is-striped">
                <thead>
                    <tr>
                        <th>username</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($forbiddenUsernames as $forbiddenUsername)
                        <tr>
                            <td>
                                {{ $forbiddenUsername->username }}
                            </td>
                            <td>
                                <form action="/forbidden-username/destroy/{{ $forbiddenUsername->id }}" method="post">
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

            {{ $forbiddenUsernames->links() }}
        </div>

    </div>

    <div class="columns is-multiline is-mobile">
		<div class="column is-half">
			<h1 class="title">
				Ban a domain address
			</h1>

			<form class="control margin-bottom-3" action="/block-domain" method="post">
                {{ csrf_field() }}

                <div class="control">
                    <input class="input" type="url" placeholder="URL" name="domain" required>
				</div>

				<br>

				<div class="control">
                    <input class="input" type="text" placeholder="Description(optional)" name="description">
				</div>

				<br>

				<div class="control">
                    <button class="button is-primary" type="submit">
                        Ban
                    </button>
				</div>
            </form>

            <table class="table is-fullwidth table is-striped">
                <thead>
                    <tr>
                        <th>domain address</th>
                        <th>description</th>
                        <th>adctions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($blockedDomains as $domain)
                        <tr>
                            <td>
                                {{ $domain->domain }}
                            </td>
                            <td>
                                {{ $domain->description }}
                            </td>
                            <td>
                                <form action="/block-domain/destroy" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}

                                    <input type="hidden" name="domain" value="{{ $domain->domain }}">
                                    <input type="hidden" name="category" value="all">

                                    <button class="button is-danger is-small" type="submit">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $blockedDomains->links() }}
		</div>
	</div>
</section>

@endsection
