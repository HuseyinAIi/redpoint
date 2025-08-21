<?php require __DIR__ . '/../config.php'; require_login(); ?>
<!DOCTYPE html><html lang="tr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head><body>
<header class="topbar"><div class="container">
  <div class="logo">Admin</div>
  <nav class="nav">
    <a href="/admin/products.php">Ürünler</a>
    <a href="/admin/categories.php">Kategoriler</a>
    <a href="/admin/logout.php">Çıkış</a>
  </nav>
</div></header>
<main class="container" style="padding:24px 0">
  <h1>Hoş geldiniz, <?php echo esc($_SESSION['username']); ?></h1>
  <p class="muted">Soldaki menüden içerik yönetimi yapabilirsiniz.</p>
</main>
</body></html>
