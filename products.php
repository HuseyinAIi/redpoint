<?php require __DIR__ . '/../config.php'; require_login();
$pdo = db();
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    if (isset($_POST['create'])) {
        $stmt = $pdo->prepare('INSERT INTO products (category_id, name, model, material, fit, color, sizes, lengths, description, is_active) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            (int)$_POST['category_id'],
            trim($_POST['name'] ?? ''),
            trim($_POST['model'] ?? ''),
            trim($_POST['material'] ?? ''),
            trim($_POST['fit'] ?? ''),
            trim($_POST['color'] ?? ''),
            trim($_POST['sizes'] ?? ''),
            trim($_POST['lengths'] ?? ''),
            trim($_POST['description'] ?? ''),
            isset($_POST['is_active']) ? 1 : 0
        ]);
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
    }
    header('Location: /admin/products.php'); exit;
}
$products = $pdo->query('SELECT p.*, c.name as cat FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="tr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ürünler</title>
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
  <h1>Ürünler</h1>
  <form method="post" class="card" style="padding:12px;margin-bottom:16px">
    <input type="hidden" name="csrf" value="<?php echo esc(csrf_token()); ?>">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px">
      <select name="category_id" required style="padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea">
        <option value="">Kategori</option>
        <?php foreach ($cats as $c) echo '<option value="'.$c['id'].'">'.esc($c['name']).'</option>'; ?>
      </select>
      <input name="name" placeholder="Ürün adı" required style="padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea">
      <input name="model" placeholder="Model (ör. RP-301)" style="padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea">
      <input name="material" placeholder="Materyal (%100 pamuk)" style="padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea">
      <input name="fit" placeholder="Fit (Slim, Relaxed...)" style="padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea">
      <input name="color" placeholder="Renk" style="padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea">
      <input name="sizes" placeholder="Bedenler (ör. 28,30,32)" style="padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea">
      <input name="lengths" placeholder="Boylar (ör. 30,32,34)" style="padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea">
      <textarea name="description" placeholder="Açıklama" style="grid-column:1/-1;height:80px;padding:10px;border-radius:8px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea"></textarea>
      <label><input type="checkbox" name="is_active" checked> Aktif</label>
      <button class="btn" name="create">Ekle</button>
    </div>
  </form>

  <div class="card" style="padding:12px">
    <table style="width:100%;border-collapse:collapse">
      <tr><th align="left">ID</th><th align="left">Ad</th><th align="left">Kategori</th><th align="left">Model</th><th align="left">Görseller</th><th></th></tr>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?php echo $p['id']; ?></td>
          <td><a href="/product.php?id=<?php echo $p['id']; ?>"><?php echo esc($p['name']); ?></a></td>
          <td><?php echo esc($p['cat']); ?></td>
          <td><?php echo esc($p['model']); ?></td>
          <td><a href="/admin/upload.php?id=<?php echo $p['id']; ?>">Yükle</a></td>
          <td>
            <form method="post" onsubmit="return confirm('Silinsin mi?')" style="display:inline">
              <input type="hidden" name="csrf" value="<?php echo esc(csrf_token()); ?>">
              <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
              <button class="btn ghost" name="delete">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</main>
</body></html>
