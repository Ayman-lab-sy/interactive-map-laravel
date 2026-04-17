<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

@foreach ($staticPages as $page)
<url>
    <loc>{{ $base }}{{ $page }}</loc>
    <priority>0.9</priority>
</url>
@endforeach

@foreach ($news as $item)
<url>
    <loc>{{ $base }}/ar/news-new/{{ $item->slug }}</loc>
    <lastmod>{{ $item->updated_at->toAtomString() }}</lastmod>
    <priority>0.8</priority>
</url>

<url>
    <loc>{{ $base }}/en/news-new/{{ $item->slug }}</loc>
    <lastmod>{{ $item->updated_at->toAtomString() }}</lastmod>
    <priority>0.8</priority>
</url>
@endforeach

</urlset>
