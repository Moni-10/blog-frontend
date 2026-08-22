<?php
$API_ORIGIN = "https://blog-api.mohindramachine.tech";
$API_BASE = $API_ORIGIN . "/api";
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

$blog = null;
if ($slug !== '' && function_exists('curl_init')) {
  $apiUrl = $API_BASE . "/blogs/slug/" . urlencode($slug);
  $ch = curl_init();
  if ($ch) {
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    curl_close($ch);
    $decoded = $response ? json_decode($response, true) : null;
    if (is_array($decoded)) {
      $blog = $decoded;
    }
  }
}

$title = $blog['title'] ?? 'Blog Not Found';
$content = $blog['content'] ?? '';
$metaTitle = $blog['metaTitle'] ?? $title . ' | Mohindra Mechanical Works';
$metaKeyword = $blog['metaKeyword'] ?? '';
$metaDescription = $blog['metaDescription'] ?? substr(strip_tags($content), 0, 160);
$images = $blog['images'] ?? [];

$pageTitle = $metaTitle;
$pageDescription = $metaDescription;
$pageKeywords = $metaKeyword;
$canonicalURL = "https://www.mohindraroto.com/blog.php";
$pageStylesheet = "css/blog-system.css?v=20260812";

include('header.php');
?>

<section class="mmw-page-hero">
  <div class="mmw-page-hero-inner">
    <span>Industrial Machinery Blog</span>
    <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
    <nav aria-label="Breadcrumb">Home / Blogs / <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></nav>
  </div>
</section>

<main class="mmw-blog-detail-page">
  <div class="mmw-blog-detail-container">
    <?php if ($blog): ?>
      <article class="mmw-blog-detail-card">
        <header class="mmw-blog-detail-head">
          <span>Industrial Machinery Blog</span>
          <h2><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>
        </header>

        <?php if (!empty($images)): ?>
          <div class="mmw-blog-detail-images">
            <?php foreach ($images as $img): ?>
              <img src="<?= htmlspecialchars(preg_match('/^https?:\/\//i', $img) ? $img : $API_ORIGIN . $img, ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                title="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                loading="lazy" decoding="async">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="mmw-blog-content">
          <?= str_replace(
            ['src="uploads/', 'src="/uploads/'],
            ['src="' . $API_ORIGIN . '/uploads/', 'src="' . $API_ORIGIN . '/uploads/'],
            $content
          ) ?>
        </div>
      </article>
    <?php else: ?>
      <section class="mmw-blog-empty">
        <h2>Blog not found</h2>
        <p>The article may have been moved or is temporarily unavailable.</p>
        <a class="mmw-blog-readmore" href="blogs.php">Back To Blogs</a>
      </section>
    <?php endif; ?>

  </div>

</main>

<?php include('footer.php'); ?>
