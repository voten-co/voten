<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach($categories as $category)
        <url>
            <loc>{{ config('app.url') }}/c/{{ $category->name }}</loc>
            <lastmod>{{ iso8601($category->created_at) }}</lastmod>
            <changefreq>hourly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>