<?php require __DIR__ . '/../config.php'; require_login();
$pdo = db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    if (isset($_POST['create'])) {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if ($name && $slug) {
            $stmt = $pdo->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)');
            $stmt->execute([$name, $slug]);
        }
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);
    }
    header('Location: /admin/categories.php'); exit;
}
$cats = $pdo->query('SELECT * FROM categories ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="tr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kategoriler</title>
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
  <h1>Kategoriler</h1>
  <form method="post" class="card" style="padding:12px;margin-bottom:16px">
    <input type="hidden" name="csrf" value="<?php echo esc(csrf_token()); ?>">
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <input name="name" placeholder="Ad" required style="padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea">
      <input name="slug" placeholder="slug (denim, t-shirts...)" required style="padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea">
      <button class="btn" name="create">Ekle</button>
    </div>
  </form>
  <div class="card" style="padding:12px">
    <table style="width:100%;border-collapse:collapse">
      <tr><th align="left">ID</th><th align="left">Ad</th><th align="left">Slug</th><th></th></tr>
      <?php foreach ($cats as $c): ?>
        <tr>
          <td><?php echo $c['id']; ?></td>
          <td><?php echo esc($c['name']); ?></td>
          <td><?php echo esc($c['slug']); ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Silinsin mi?')" style="display:inline">
              <input type="hidden" name="csrf" value="<?php echo esc(csrf_token()); ?>">
              <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
              <button class="btn ghost" name="delete">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</main>
</body></html>
