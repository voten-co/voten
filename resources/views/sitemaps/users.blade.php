<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach($users as $user)
        <url>
            <loc>{{ config('app.url') }}/{{ '@' . $user->username }}</loc>
            <lastmod>{{ iso8601($user->updated_at) }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset>