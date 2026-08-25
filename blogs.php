<?php
$apiBase = rtrim(getenv('BLOG_API_BASE_URL') ?: 'https://blog-backend.mohindramachine.tech', '/');
$currentHost = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? 'mohindramechanicalworks.com');
$domain = preg_replace('/^www\./i', '', strtolower($currentHost));
$page = max((int)($_GET['page'] ?? 1), 1);
$websiteId = preg_replace('/[^a-f0-9]/i', '', $_GET['websiteId'] ?? '');

function api_get($url)
{
  if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 12, CURLOPT_HTTPHEADER => ['Accept: application/json']]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code >= 200 && $code < 300 ? json_decode($body, true) : null;
  }
  $body = @file_get_contents($url);
  return $body === false ? null : json_decode($body, true);
}
function e($value)
{
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function excerpt($blog)
{
  $text = trim($blog['excerpt'] ?? '');
  if (!$text) $text = trim(strip_tags($blog['content'] ?? ''));
  return mb_strlen($text) > 150 ? mb_substr($text, 0, 147) . '...' : $text;
}

function clean_domain($value)
{
  return preg_replace('/^www\./i', '', strtolower(trim(preg_replace('#^https?://#i', '', rtrim((string)$value, '/')))));
}

$limit = 9;
$feed = api_get($apiBase . '/api/blogs/public/' . rawurlencode(clean_domain($domain)) . '?page=' . $page . '&limit=' . $limit);
$matchedSite = is_array($feed) ? ($feed['website'] ?? null) : null;
$websiteId = $matchedSite['_id'] ?? $websiteId;
$blogs = is_array($feed) ? ($feed['blogs'] ?? []) : [];
$data = is_array($feed) ? true : null;
$siteName = $matchedSite['name'] ?? 'MMW Machine';
$pagination = is_array($feed) ? ($feed['pagination'] ?? ['page' => $page, 'pages' => 1, 'total' => count($blogs)]) : ['page' => $page, 'pages' => 1, 'total' => 0];
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Industrial Insights & Machine Blogs | <?= e($siteName) ?></title>
  <meta name="description" content="Latest machine, manufacturing and industrial insights from <?= e($siteName) ?>.">
  <style>
    :root {
      --red: #bd001b;
      --dark: #320008;
      --soft: #fff2f4;
      --text: #262022;
      --muted: #71686a
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      color: var(--text);
      background: #faf9f9
    }

    a {
      color: inherit
    }

    .top {
      background: linear-gradient(110deg, #63000c, #d30020);
      color: #fff
    }

    .nav {
      max-width: 1180px;
      margin: auto;
      padding: 20px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between
    }

    .logo {
      font-weight: 900;
      font-size: 27px;
      text-decoration: none
    }

    .logo span {
      color: #ff9aa8
    }

    .nav>a:last-child {
      font-size: 14px
    }

    .hero {
      max-width: 1180px;
      margin: auto;
      padding: 72px 24px 88px
    }

    .hero small {
      letter-spacing: .16em;
      font-weight: 800
    }

    .hero h1 {
      font: 600 clamp(42px, 7vw, 76px)/1.02 Georgia;
      margin: 15px 0;
      max-width: 850px
    }

    .hero p {
      color: #ffd9de;
      font-size: 17px;
      max-width: 620px;
      line-height: 1.7
    }

    .wrap {
      max-width: 1180px;
      margin: -38px auto 0;
      padding: 0 24px 70px
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px
    }

    .card {
      display: flex;
      flex-direction: column;
      background: #fff;
      border: 1px solid #eee1e3;
      border-radius: 15px;
      overflow: hidden;
      text-decoration: none;
      box-shadow: 0 10px 28px #3d000608;
      transition: .2s
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 35px #3d000612
    }

    .thumb {
      height: 210px;
      background: var(--soft);
      overflow: hidden
    }

    .thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover
    }

    .placeholder {
      height: 100%;
      display: grid;
      place-items: center;
      color: var(--red);
      font: 700 48px Georgia
    }

    .body {
      padding: 22px
    }

    .meta {
      display: flex;
      justify-content: space-between;
      color: var(--red);
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: .08em
    }

    .body h2 {
      font: 600 25px/1.15 Georgia;
      margin: 13px 0
    }

    .body p {
      color: var(--muted);
      line-height: 1.65;
      font-size: 14px
    }

    .read {
      color: var(--red);
      font-weight: 800;
      font-size: 12px
    }

    .empty {
      text-align: center;
      background: #fff;
      padding: 70px 24px;
      border-radius: 15px
    }

    .pages {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 35px
    }

    .pages a {
      padding: 9px 13px;
      border-radius: 7px;
      background: #fff;
      text-decoration: none
    }

    .pages a.active {
      background: var(--red);
      color: #fff
    }

    @media(max-width:850px) {
      .grid {
        grid-template-columns: repeat(2, 1fr)
      }
    }

    @media(max-width:560px) {
      .grid {
        grid-template-columns: 1fr
      }

      .hero {
        padding-top: 48px
      }

      .thumb {
        height: 230px
      }
    }

    /* Reference-style MMW blog cards */
    body {
      background: linear-gradient(105deg, #f3d5da 0, #fff 25%, #fff 76%, #f2ced4 100%)
    }

    .top {
      background: #fff;
      color: #092d50;
      border-bottom: 1px solid #eadde0
    }

    .logo {
      color: #092d50
    }

    .nav>a:last-child {
      color: #a90017
    }

    .hero {
      padding: 55px 24px 70px
    }

    .hero small {
      color: #b40019
    }

    .hero h1 {
      color: #06294a;
      font-size: clamp(38px, 6vw, 65px)
    }

    .hero p {
      color: #596c7e
    }

    .wrap {
      max-width: 1320px;
      margin: -20px auto 0
    }

    .grid {
      gap: 28px
    }

    .card {
      position: relative;
      min-height: 510px;
      border: 1px solid #d8d8d8;
      border-radius: 20px;
      box-shadow: none;
      overflow: hidden;
      background: #fff
    }

    .card:hover {
      transform: translateY(-5px);
      border-color: #c5aeb2;
      box-shadow: 0 15px 35px #85001412
    }

    .thumb {
      height: 285px;
      background: #fff;
      padding: 16px 16px 0;
      overflow: hidden
    }

    .thumb img {
      object-fit: contain
    }

    .date-badge {
      position: absolute;
      z-index: 2;
      right: 20px;
      top: 248px;
      width: 60px;
      text-align: center
    }

    .date-badge strong,
    .date-badge span {
      display: grid;
      place-items: center
    }

    .date-badge strong {
      height: 50px;
      background: #c4001b;
      color: #fff;
      font-size: 22px
    }

    .date-badge span {
      height: 40px;
      background: #e4e4e4;
      color: #082d50;
      font-size: 17px;
      font-weight: 800
    }

    .body {
      display: flex;
      flex-direction: column;
      flex: 1;
      padding: 22px 20px 24px
    }

    .meta {
      padding-right: 65px;
      color: #b40019;
      font-weight: 800
    }

    .body h2 {
      color: #062b50;
      font: 700 17px/1.35 Arial, sans-serif;
      margin: 13px 0 9px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden
    }

    .body p {
      color: #191919;
      font-size: 15px;
      line-height: 1.55;
      margin: 4px 0 16px;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden
    }

    .read {
      margin-top: auto;
      color: #082f55;
      font-size: 15px
    }

    .read b {
      font-size: 21px;
      vertical-align: -2px
    }

    .placeholder {
      background: #fff4f5
    }

    @media(max-width:950px) {
      .grid {
        grid-template-columns: repeat(2, 1fr)
      }

      .thumb {
        height: 260px
      }

      .date-badge {
        top: 223px
      }
    }

    @media(max-width:600px) {
      .grid {
        grid-template-columns: 1fr
      }

      .card {
        min-height: 480px
      }

      .thumb {
        height: 260px
      }

      .date-badge {
        top: 223px
      }

      .hero {
        text-align: center
      }
    }
    /* Compact blog listing */
    .hero {
      padding: 34px 24px 46px
    }

    .hero h1 {
      font-size: clamp(32px, 4.5vw, 50px)
    }

    .wrap {
      max-width: 940px;
      padding: 0 18px 55px
    }

    .page-banner {
      width: 100%;
      padding: 42px 24px 52px;
      text-align: center;
      background: linear-gradient(110deg, #fff1f3, #fff 52%, #f5d8dc);
      border-bottom: 1px solid #efdadd
    }

    .page-banner small {
      color: #b40019;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .16em
    }

    .page-banner h1 {
      margin: 8px 0;
      color: #062b50;
      font: 700 clamp(32px, 4vw, 48px)/1.15 Arial, sans-serif
    }

    .page-banner p {
      margin: 0;
      color: #596c7e;
      font-size: 15px
    }

    .grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 20px
    }

    .card {
      min-height: 390px;
      border-radius: 5px
    }

    .thumb {
      height: 205px;
      padding: 0
    }

    .date-badge {
      left: 10px;
      right: auto;
      top: 10px;
      width: 44px
    }

    .date-badge strong {
      height: 31px;
      font-size: 14px
    }

    .date-badge span {
      height: 24px;
      font-size: 11px
    }

    .body {
      padding: 18px 18px 20px
    }

    .body h2 {
      margin-top: 10px
    }

    .body p {
      font-size: 14px;
      -webkit-line-clamp: 2
    }

    .read {
      align-self: flex-start;
      padding: 0;
      border-radius: 0;
      background: transparent;
      color: #c0001a;
      font-size: 11px;
      text-transform: uppercase
    }

    .read b {
      font-size: 17px
    }

    @media(max-width:600px) {
      .card { min-height: 410px }
      .thumb { height: 220px }
      .date-badge { top: 10px }
      .grid { grid-template-columns: 1fr }
    }

    .blog-layout { max-width:1200px; margin:0 auto; padding:42px 20px 70px; display:grid; grid-template-columns:minmax(0,1fr) 300px; gap:30px; align-items:start }
    .blog-layout .wrap { width:100%; max-width:none; margin:0; padding:0 }
    .blog-layout .grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:22px }
    .blog-layout .card { min-width:0 }
    .side-panel { position:sticky; top:20px; padding:22px; background:#fff; border:1px solid #e5e5e5; border-radius:12px; box-shadow:0 8px 26px #0000000b }
    .side-panel h3 { margin:0 0 14px; padding-bottom:10px; border-bottom:2px solid #bd001b; color:#172f43; font-size:13px; text-transform:uppercase }
    .side-search { display:flex; margin-bottom:26px }
    .side-search input { min-width:0; flex:1; height:40px; padding:0 11px; border:1px solid #ddd; border-right:0 }
    .side-search button { width:42px; border:0; background:#bd001b; color:#fff; font-size:17px }
    .recent-post { display:grid; grid-template-columns:84px minmax(0,1fr); gap:11px; align-items:center; padding:11px 0; border-bottom:1px solid #eee; color:#1c3042; text-decoration:none }
    .recent-post img { width:84px; height:58px; object-fit:cover }
    .recent-post strong { display:block; font-size:12px; line-height:1.35 }
    .quote-box { margin-top:26px; padding:18px; background:#fff3f5; border-left:4px solid #bd001b }
    .quote-box p { font-size:13px; line-height:1.5 }
    .quote-box a { display:block; padding:11px; background:#bd001b; color:#fff; text-align:center; text-decoration:none; font-size:12px; font-weight:800; text-transform:uppercase }
    @media(max-width:900px) { .blog-layout { grid-template-columns:1fr } .side-panel { position:static } }
    @media(max-width:640px) { .blog-layout { padding:25px 14px 50px } .blog-layout .grid { grid-template-columns:1fr } .page-banner { padding:32px 16px 38px } }
  </style>
</head>

<body>
  <?php if (file_exists(__DIR__ . '/header.php')) include_once __DIR__ . '/header.php'; ?>

  <section class="page-banner"><small>MMW INDUSTRIAL INSIGHTS</small><h1>Latest Blogs &amp; Industry Updates</h1><p>Expert guidance, machine knowledge and manufacturing trends.</p></section>

  <div class="blog-layout"><main class="wrap"><?php if (!$data): ?><div class="empty">
        <h2>Blogs could not be loaded</h2>
        <p>Confirm that the blog API is running and this domain is registered in the admin panel.</p>
      </div><?php elseif (!$blogs): ?><div class="empty">
        <h2>No published blogs yet</h2>
        <p>Publish your first article from the MMW admin panel.</p>
      </div><?php else: ?><section class="grid"><?php foreach ($blogs as $blog): $image = $blog['featuredImage'] ?? ($blog['images'][0] ?? '');
                                                  $published = strtotime($blog['publishedAt'] ?? $blog['createdAt']); ?><a class="card" href="blog.php?domain=<?= e(urlencode($domain)) ?>&websiteId=<?= e(urlencode($websiteId)) ?>&slug=<?= e(urlencode($blog['slug'])) ?>">
            <div class="thumb"><?php if ($image): ?><img src="<?= e($image) ?>" alt="<?= e($blog['featuredImageAlt'] ?? $blog['title']) ?>"><?php else: ?><div class="placeholder">MMW</div><?php endif; ?></div>
            <time class="date-badge" datetime="<?= e(date('Y-m-d', $published)) ?>"><strong><?= e(date('d', $published)) ?></strong><span><?= e(date('M', $published)) ?></span></time>
            <div class="body">
              <div class="meta"><span><?= e($blog['category']['name'] ?? 'Industry') ?></span></div>
              <h2><?= e($blog['title']) ?></h2>
              <p><?= e(excerpt($blog)) ?></p><span class="read">Read More <b>→</b></span>
            </div>
          </a><?php endforeach; ?></section>
      <nav class="pages"><?php for ($i = 1; $i <= ($pagination['pages'] ?? 1); $i++): ?><a class="<?= $i == ($pagination['page'] ?? 1) ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a><?php endfor; ?></nav><?php endif; ?>
  </main><aside class="side-panel"><h3>Search</h3><form class="side-search" action="blogs.php" method="get"><input type="search" name="q" placeholder="Search posts"><button aria-label="Search">⌕</button></form><h3>Recent Posts</h3><?php foreach (array_slice($blogs,0,5) as $recent): $ri=$recent['featuredImage'] ?? ($recent['images'][0] ?? ''); ?><a class="recent-post" href="blog.php?slug=<?= e(urlencode($recent['slug'] ?? '')) ?>"><?php if($ri): ?><img src="<?= e($ri) ?>" alt=""><?php endif; ?><strong><?= e($recent['title'] ?? 'Blog') ?></strong></a><?php endforeach; ?><div class="quote-box"><h3>Request a Quote</h3><p>Share your machine requirement and our team will contact you.</p><a href="/contact-us.php">Submit Enquiry</a></div></aside></div>
  <?php if (file_exists(__DIR__ . '/footer.php')) include_once __DIR__ . '/footer.php'; ?>
</body>

</html>
