<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach($helps as $help)
        <url>
            <loc>{{ config('app.url') }}/help/{{ $help->id }}</loc>
            <lastmod>{{ iso8601($help->created_at) }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>