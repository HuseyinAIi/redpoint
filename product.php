<?php require __DIR__ . '/config.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE p.id=?');
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$p) { http_response_code(404); echo 'Ürün bulunamadı'; exit; }
$title = $p['name'];
include __DIR__ . '/includes/header.php';
?>
<section class="container product-detail">
  <div class="pd-gallery">
    <?php
      $imgs = db()->prepare('SELECT * FROM images WHERE product_id=? ORDER BY is_primary DESC, sort_order ASC');
      $imgs->execute([$p['id']]);
      foreach ($imgs as $im) {
        echo '<img src="'.esc($im['file_path']).'" class="pd-img" alt="'.esc($p['name']).'">';
      }
      if ($imgs->rowCount() === 0) {
        echo '<img src="/assets/img/placeholder.jpg" class="pd-img" alt="placeholder">';
      }
    ?>
  </div>
  <div class="pd-info">
    <a class="breadcrumb" href="/category.php?slug=<?php echo esc($p['cat_slug']); ?>">&larr; <?php echo esc($p['cat_name']); ?></a>
    <h1><?php echo esc($p['name']); ?></h1>
    <ul class="specs">
      <li><strong>Model:</strong> <?php echo esc($p['model'] ?: '-'); ?></li>
      <li><strong>Materyal:</strong> <?php echo esc($p['material'] ?: '-'); ?></li>
      <li><strong>Kesim/Fit:</strong> <?php echo esc($p['fit'] ?: '-'); ?></li>
      <li><strong>Renk:</strong> <?php echo esc($p['color'] ?: '-'); ?></li>
      <li><strong>Bedenler:</strong> <?php echo esc($p['sizes'] ?: '-'); ?></li>
      <li><strong>Boylar:</strong> <?php echo esc($p['lengths'] ?: '-'); ?></li>
    </ul>
    <p class="muted">Vitrin ürünü. Satış yapılmamaktadır.</p>
    <p><?php echo nl2br(esc($p['description'] ?: '')); ?></p>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
