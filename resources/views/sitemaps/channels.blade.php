<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach($channels as $channel)
        <url>
            <loc>{{ config('app.url') }}/c/{{ $channel->name }}</loc>
            <lastmod>{{ iso8601($channel->created_at) }}</lastmod>
            <changefreq>hourly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>