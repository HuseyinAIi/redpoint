<?php require __DIR__ . '/config.php';
$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare('SELECT id, name, slug FROM categories WHERE slug = ?');
$stmt->execute([$slug]);
$cat = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cat) { http_response_code(404); echo 'Kategori bulunamadı'; exit; }
$title = $cat['name'];
include __DIR__ . '/includes/header.php';
?>
<section class="container">
  <h1 class="page-title"><?php echo esc($cat['name']); ?></h1>
  <div class="product-grid">
    <?php
      $ps = db()->prepare('SELECT * FROM products WHERE category_id = ? AND is_active = 1 ORDER BY created_at DESC');
      $ps->execute([$cat['id']]);
      $products = $ps->fetchAll(PDO::FETCH_ASSOC);
      if (!$products) echo '<p class="muted">Bu kategoride ürün yok.</p>';
      foreach ($products as $p) {
        $img = db()->prepare('SELECT file_path FROM images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC LIMIT 2');
        $img->execute([$p['id']]);
        $imgs = $img->fetchAll(PDO::FETCH_COLUMN);
        $primary = $imgs[0] ?? '/assets/img/placeholder.jpg';
        $hover = $imgs[1] ?? $primary;
        echo '<a class="product-card" href="/product.php?id='.esc($p['id']).'">
          <div class="pc-media">
            <img src="'.esc($primary).'" alt="'.esc($p['name']).'" class="img primary" />
            <img src="'.esc($hover).'" alt="" class="img hover" />
          </div>
          <div class="pc-body">
            <div class="pc-name">'.esc($p['name']).'</div>
            <div class="pc-model">Model: '.esc($p['model'] ?: '-').'</div>
          </div>
        </a>';
      }
    ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
