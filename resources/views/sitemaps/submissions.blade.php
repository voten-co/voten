<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach($submissions as $submission)
        <url>
            <loc>{{ config('app.url') }}/c/{{ $submission->channel_name }}/{{ $submission->slug }}</loc>
            <lastmod>{{ iso8601($submission->created_at) }}</lastmod>
            @if(strtotime($submission->created_at) > strtotime('-1 days'))
                <changefreq>hourly</changefreq>
                <priority>0.9</priority>
            @else
                <changefreq>monthly</changefreq>
                <priority>0.8</priority>
            @endif
        </url>
    @endforeach
</urlset>