<?php
// Combines RSS feeds from multiple sources
$sources = [
  // BASIS
  [
    'feed_url' => 'https://student-activity.binus.ac.id/basis/tag/computerun-2-0/feed/atom',
    'source_type' => 'atom',
    'content_type' => 'gallery',
  ],
  // BINARY
  [
    'feed_url' => 'https://student-activity.binus.ac.id/binary/tag/computerun-2-0/feed/atom',
    'source_type' => 'atom',
    'content_type' => 'news',
  ],
  // HIMKA
  [
    'feed_url' => 'https://student-activity.binus.ac.id/himka/tag/computerun-2-0/feed/atom',
    'source_type' => 'atom',
    'content_type' => 'gallery',
  ],
  // HIMSISFO Greater Jakarta
  [
    'feed_url' => 'https://student-activity.binus.ac.id/himsisfo/tag/computerun-2-0/feed/atom',
    'source_type' => 'atom',
    'content_type' => 'gallery',
  ],
  // HIMTI Greater Jakarta
  [
    'feed_url' => 'https://student-activity.binus.ac.id/himti/tag/computerun-2-0/feed/atom',
    'source_type' => 'atom',
    'content_type' => 'articles',
  ],
  // Official YouTube
  [
    'feed_url' => 'https://www.youtube.com/feeds/videos.xml?channel_id=UCFRD-EdSjCiUXfaU21ueryA',
    'source_type' => 'atom',
    'content_type' => 'video',
  ],
];

$articles = [];

if (Cache::has('news')) $articles = Cache::get('news');
else {
  foreach ($sources as $source){
    // Fetch data
    if ($source['source_type'] == 'atom' || $source['source_type'] == 'rss'){
      $feed = simplexml_load_file($source['feed_url']);
      $entries = ($source['source_type'] == 'atom') ? $feed->entry : $feed->channel->item;

      foreach ($entries as $entry){
        // Title
        $item = [
          'title' => (string) $entry->title,
          'content_type' => $source['content_type'],
          'categories' => [],
        ];

        // URL
        if ($source['source_type'] == 'atom') {
          $item['url'] = (string) $entry->link->attributes()->{'href'};
        } else {
          $item['url'] = (string) $entry->link;
        }

        // Timestamp
        if (isset($entry->pubDate)) $item['timestamp'] = (int) strtotime($entry->pubDate . " UTC");
        else if (isset($entry->published)) $item['timestamp'] = (int) strtotime($entry->published . " UTC");
        else $item['timestamp'] = (int) $entry->timestamp;

        // Cover Image
        if (isset($entry->enclosure)) $item['cover_image'] = (string) $entry->enclosure->attributes()->{'url'};
        else if (isset($entry->children('media', TRUE)->content)) $item['cover_image'] = (string) $entry->children('media', TRUE)->content->attributes()->url;
        else if (isset($entry->children('media', TRUE)->group)) $item['cover_image'] = (string) $entry->children('media', TRUE)->group->children('media', TRUE)->thumbnail->attributes()->url;

        // Categories and Tags
        if (isset($entry->category)){
          // if (!is_array($entry->category)) $entry->category = array($entry->category);
          foreach ($entry->category as $category){
            array_push($item['categories'], (string) $category->attributes()->term);
          }
        }

        // Push into article list
        $articles[$item['timestamp']] = $item;
      }
    }
  }
  ksort($articles, SORT_NUMERIC);
  Cache::put('news', $articles , 360);
}
?>

@if (count($articles) == 0)
  <div class="card">
    <div class="card-body">
      <p>Failed to get recent news</p>
    </div>
  </div>
@else
  <div class="row margin-1" id="RSSarticle">
  @foreach($articles as $article)
    <a href="{{ $article['url'] }}&amp;utm_campaign=computerun2.0" class="card article bg-dark text-white p-0" style="border-radius: 1em;">
      <img src="{{ $article['cover_image'] ?? 'https://picsum.photos/id/1/600/400' }}" class="card-img" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.25); border-radius: 1em;">
      <div class="card-img-overlay" style="margin-top: auto">
        <h4 class="card-title fw-bold mb-3">{{ $article['title'] }}</h4>
        <p class="card-text fw-bold">{{ date('Y-m-d h:i:s', $article['timestamp']); }}</p>
      </div>
    </a>
  @endforeach
  </div>
@endif
