@extends('layouts.landing-layout')

@section('content')
	<section class="container">
		<h1 class="title align-center">Terms Of Service</h1>

		<p>
			{{ config('app.name') }} is a website for groups and individuals to communicate, meet, and contribute in an open atmosphere, resort to some of the most authentic materials online. The nature of this online materials might be funny, serious, offensive, scientific, political or anywhere in between. While participating, it’s crucial to remember this value above all others: be respectful to all users, so we all can enjoy {{ config('app.name') }} for what it is.
		</p>

		<br>
		<h2 class="title">
			Unwelcome content
		</h2>

		<p>
			While {{ config('app.name') }} enables users to apply leeway to what content is acceptable, here are some guidelines for content that is not.
			Please keep in mind the spirit in which these were written, and know that looking for loopholes are waste of time. Materials are banned if they:
		</p>

		<ul>
			<li>
				<b>Are illegal</b>: Materials may break the law if they include, but are not limited to Copyright or trademark infringements and
				Illegal sexual contents.
			</li>

			<li>
				<b>Are involuntary pornography</b>: {{ config('app.name') }} bans the posting of photographs, videos,
				or digital images of users in a state of nudity or engaged in any act of sexual activity,
				taken or posted without their direct permission. <br>
				Other banned materials are child sexual abuse imagery,
				material that encourages or promote pedophilia, as well as materials that glorify or encourage rape or non-consensual sexual violence.
				<br>
				To report involuntary pornography or encouragement of sexual violence,
				please use the report button in the application. {{ config('app.name') }} bans the posting of such materials without consent.
			</li>

			<li>
				<b>Encourage or stimulate violence</b>: Do not post materials that stimulate harm against any person or groups of people.
				Furthermore, if you're going to post something violent in nature, think about including the NSFW tag.
			</li>

			<li>
				<b>Threaten, harass, or bully or encourage others to do so</b>:
				We do not tolerate the harassment of people on our site,
				nor do we tolerate communities dedicated to fostering harassing behavior. Being annoying, vote brigading, or participating in an argument is not harassment, but following an individual or group of users, online or off,
				to the point where they no longer feel that it's safe to post online or are in fear of their real life safety is.
			</li>

			<li>
				<b>Are personal and confidential information</b>: {{ config('app.name') }} is pro-free speech,
				but it is not okay to post someone's personal information or post any link directing to such stuff.
				This includes links to public Facebook pages and screenshots of Facebook pages with the names still readable.
				We all get outraged by the ignorant things people say and do online,
				but exposing personal information can hurt innocent people, and personal information found online (and elsewhere) is often false or out of date. Posting someone's personal information will get you banned. When posting screenshots, be sure to edit out any personally identifiable information. Public figures can be an exception to this rule, such as posting professional links to contact a congressman or the CEO of a company.
				But don't post anything inviting harassment, don't harass, and don't cheer on or upvote obvious vigilantism.
			</li>

			<li>
				<b>Impersonate someone in a misleading or deceptive manner</b>:
				Impersonating someone in a misleading or deceptive manner is not allowed. However, satire and parody are ok.
			</li>

			<li>
				<b>Are spam</b>: Sometimes spam is obvious, but often it is hard to figure out. 
				If you are posting your own content and other voters appreciate and upvote your posts, you shouldn't have anything to worry about BUT: If your activities on {{ config('app.name') }} consists mainly of sending links only about a certain topic, you should be careful. Additionally, if you do not engage in other conversations and different activities, you may be considered a spammer and banned from {{ config('app.name') }}. If your contributions are often off-topic or not relevant to the community you are posting in, you may be considered a spammer. If you flood a {{ config('app.name') }} group with posts or comments, you may be considered a spammer. It is better to send a message to the moderators of the community you'd like to submit to. They'll probably appreciate the advance notice.
				{{ config('app.name') }} channels are allowed to have their additional rules. Thus, they can decide what is spam and what is not.
			</li>
		</ul>


		<br>
		<h2 class="title">
			Prohibited behavior
		</h2>

		<p>
			In addition to not submitting unwelcome content, the following behaviors are banned on {{ config('app.name') }}:
		</p>

		<p>
			Asking for votes or engaging in vote manipulation: Vote manipulation is against the {{ config('app.name') }} rules, whether it is manual, programmatic, or otherwise. Some common forms of vote cheating are: Using multiple accounts, voting services, or any other software to increase or decrease vote scores. Asking people to vote up or down certain posts, either on {{ config('app.name') }} itself or through social networks, messaging, etc. for personal gain. Forming or joining a group that votes together, either on a specific post, a user's posts, posts from a domain, etc. Cheating or attempting to manipulate voting will result in your account being banned. Don't do it. Breaking {{ config('app.name') }} or doing anything that interferes with normal use of {{ config('app.name') }}.
			Creating multiple accounts to evade punishment or avoid restrictions
		</p>

		<br>
		<h2 class="title">
			NSFW (Not Safe For Work) content
		</h2>

		<p>
			Content that contains nudity, pornography, or insulting, which a reasonable viewer may not want to be seen accessing in a public or formal setting such as in a workplace should be tagged as NSFW.
			This tag can be applied to individual pieces of content or to entire communities.
		</p>

		<br>
		<h2 class="title">
			Enforcement
		</h2>

		<p>
			We have different of ways of applying our rules, including, but not limited to:
		</p>

		<ul>
			<li>
				Sending you a message to quit your act
			</li>

			<li>
				Temporary or permanently freezing your account
			</li>

			<li>
				Restricting your account from certain groups or parts of the site
			</li>

			<li>
				Banning your IP
			</li>
		</ul>

		<br>
		<h2 class="title">
			Moderation within communities
		</h2>

		<p>
			Individual groups on {{ config('app.name') }} which are called channels may have their own rules in addition to ours and their own moderators to enforce them.
			{{ config('app.name') }} provides tools to aid moderators but does not prescribe their usage.
		</p>
	</section>
@endsection



@section('head')
	<title>{{ config('app.name') }} | Terms Of Service</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="{{ config('app.name') }} | Terms Of Service" />
	<meta property="og:url" content="{{ config('app.url') }}/tos" />
	<meta property="og:site_name" content="{{ config('app.name') }}" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="{{ config('settings.twitter.name') }}" />
	<meta name="twitter:title" content="{{ config('app.name') }} | Terms Of Service" />

	<meta name="description" content="{{ config('settings.tos_description') }}"/>
	<meta property="og:description" content="{{ config('settings.tos_description') }}" />
	<meta name="twitter:description" content="{{ config('settings.tos_description') }}" />
	<meta property="og:image" content="{{ config('app.url') }}/imgs/voten-circle.png">
	<meta name="twitter:image" content="{{ config('app.url') }}/imgs/voten-circle.png" />
@endsection