<?php
$pageTitle = "Blogs | Mohindra Mechanical Works";
$pageDescription = "Blogs from Mohindra Mechanical Works. Explore machine specifications, applications and technical support for dependable industrial production.";
$pageKeywords = "blogs, Mohindra Mechanical Works, industrial machinery, packaging machine, India";
$canonicalURL = "https://www.mohindraroto.com/blogs.php";
$pageStylesheet = "css/blog-system.css?v=20260812";

$API_ORIGIN = "https://blog-api.mohindramachine.tech";
$API_BASE = $API_ORIGIN . "/api";
$websiteId = "691c127349577ebf9b85c04a";
$apiUrl = $API_BASE . "/blogs/website/" . $websiteId;

$apiContext = stream_context_create([
  'http' => ['timeout' => 8],
  'https' => ['timeout' => 8],
]);
$response = @file_get_contents($apiUrl, false, $apiContext);
$blogs = $response ? json_decode($response, true) : [];
if (!is_array($blogs)) {
  $blogs = [];
}

include('header.php');
?>

<section class="mmw-page-hero">
  <div class="mmw-page-hero-inner">
    <span>Latest Insights</span>
    <h1>Blogs</h1>
    <nav aria-label="Breadcrumb">Home / Blogs</nav>
  </div>
</section>

<main class="mmw-blogs-page">
  <section class="mmw-blog-list-section">
    <div class="mmw-blog-container">
      <div class="mmw-blog-section-head">
        <span>Latest Articles</span>
        <h2>Printing, Packaging And Converting Machinery Blogs</h2>
        <p>Explore practical information about machinery selection, production efficiency, maintenance, and manufacturing trends.</p>
      </div>

      <?php if (!empty($blogs)): ?>
        <div class="mmw-blog-grid">
          <?php foreach ($blogs as $blog): ?>
            <?php
            $title = $blog['title'] ?? 'Mohindra Mechanical Works Blog';
            $slug = $blog['slug'] ?? '';
            $content = $blog['content'] ?? '';
            $createdAt = !empty($blog['createdAt']) ? strtotime($blog['createdAt']) : false;
            $imageUrl = !empty($blog['images'][0])
              ? (preg_match('/^https?:\/\//i', $blog['images'][0]) ? $blog['images'][0] : $API_ORIGIN . $blog['images'][0])
              : "images/pageheader.avif";
            ?>

            <article class="mmw-blog-card">
              <a class="mmw-blog-card-media" href="blog.php?slug=<?= urlencode($slug) ?>">
                <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>"
                  alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                  title="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                  loading="lazy" decoding="async">
              </a>

              <div class="mmw-blog-card-body">
                <span class="mmw-blog-date">
                  <?= $createdAt ? date("d M Y", $createdAt) : "Mohindra Blog" ?>
                </span>
                <h2>
                  <a href="blog.php?slug=<?= urlencode($slug) ?>">
                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                  </a>
                </h2>
                <p><?= htmlspecialchars(substr(strip_tags($content), 0, 155), ENT_QUOTES, 'UTF-8') ?>...</p>
                <a class="mmw-blog-readmore" href="blog.php?slug=<?= urlencode($slug) ?>">Read More</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="mmw-blog-empty">
          <h2>Blogs will be available soon</h2>
          <p>We are preparing useful machinery articles and production insights. Please check back later.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include('footer.php'); ?>
