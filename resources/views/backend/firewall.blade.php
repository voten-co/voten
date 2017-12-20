@extends('layouts.backend-layout')

@section('title')
    Firewall
@endsection

@section('content')

<section class="section container">
    <div class="columns is-multiline is-mobile">
        <div class="column is-half">
            <h1 class="title">Forbidden Channel Names ({{ $forbiddenChannelNames->total() }}):</h1>

            <form class="control has-addons" action="/forbidden-channel-name/store" method="post">
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
                    @foreach ($forbiddenChannelNames as $forbiddenChannelName)
                        <tr>
                            <td>
                                {{ $forbiddenChannelName->name }}
                            </td>
                            <td>
                                <form action="/forbidden-channel-name/destroy/{{ $forbiddenChannelName->id }}" method="post">
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

            {{ $forbiddenChannelNames->links() }}
        </div>

        <div class="column is-half">
            <h1 class="title">Forbidden usernames ({{ $forbiddenUsernames->total() }}):</h1>

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
			<h1 class="title">Banned domain addresses ({{ $blockedDomains->total() }}):</h1>

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
                                    <input type="hidden" name="channel" value="all">

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

        <div class="column is-half">
            <h1 class="title">
                Banned IP addresses ({{ $banned_ip_addresses->total() }}):
            </h1>

            <form class="control has-addons" action="/backend/firewall/ip/store" method="post">
                {{ csrf_field() }}

                <div class="field has-addons">
                    <div class="control flex1">
                        <input class="input" type="text" placeholder="IP Address..." required name="ip_address">
                    </div>

                    <div class="control">
                        <button class="button is-primary" type="submit">
                            Add
                        </button>
                    </div>
                </div>
            </form>

            <br>

            <table class="table is-striped is-fullwidth">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Ban until</th>
                        <th>Banned At</th>
                        <th>Unban</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($banned_ip_addresses as $ip)
                        <tr>
                            <td>{{ $ip->ip_address }}</td>
                            <td><span class="tag is-white">{{ $ip->unban_at->diffForHumans() }}</span></td>
                            <td><span class="tag">{{ $ip->created_at->diffForHumans() }}</span></td>
                            <td>
                                <form action="/backend/firewall/ip/destroy" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}

                                    <input type="hidden" value="{{ $ip->ip_address }}" name="ip_address">

                                    <button class="button is-danger is-small" type="submit">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if(get_class($banned_ip_addresses) === 'Illuminate\Pagination\LengthAwarePaginator')
                {{ $banned_ip_addresses->links() }}
            @endif
        </div>
	</div>
</section>

@endsection
