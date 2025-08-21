<?php require __DIR__ . '/../config.php'; require_login();
$pdo = db();
$id = (int)($_GET['id'] ?? 0);
$prod = $pdo->prepare('SELECT id, name FROM products WHERE id=?'); $prod->execute([$id]);
$p = $prod->fetch(PDO::FETCH_ASSOC);
if (!$p) { echo 'Ürün bulunamadı'; exit; }

if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    if (!empty($_FILES['images'])) {
        $files = $_FILES['images'];
        $count = count($files['name']);
        $uploadDir = __DIR__ . '/../uploads/' . $p['id'];
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
        for ($i=0; $i<$count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmp = $files['tmp_name'][$i];
                $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $name = uniqid('img_') . '.' . strtolower($ext ?: 'jpg');
                $dest = $uploadDir . '/' . $name;
                move_uploaded_file($tmp, $dest);
                $webPath = '/uploads/' . $p['id'] . '/' . $name;
                $stmt = $pdo->prepare('INSERT INTO images (product_id, file_path, is_primary, sort_order) VALUES (?,?,0,0)');
                $stmt->execute([$p['id'], $webPath]);
            }
        }
    }
    if (isset($_POST['primary'])) {
        $imgId = (int)$_POST['primary'];
        $pdo->prepare('UPDATE images SET is_primary=0 WHERE product_id=?')->execute([$p['id']]);
        $pdo->prepare('UPDATE images SET is_primary=1 WHERE id=? AND product_id=?')->execute([$imgId, $p['id']]);
    }
    if (isset($_POST['delete'])) {
        $imgId = (int)$_POST['delete'];
        $img = $pdo->prepare('SELECT * FROM images WHERE id=? AND product_id=?'); $img->execute([$imgId, $p['id']]);
        if ($row = $img->fetch(PDO::FETCH_ASSOC)) {
            $file = __DIR__ . '/..' . $row['file_path'];
            if (file_exists($file)) { unlink($file); }
            $pdo->prepare('DELETE FROM images WHERE id=?')->execute([$imgId]);
        }
    }
    header('Location: /admin/upload.php?id='.$p['id']); exit;
}
$imgs = $pdo->prepare('SELECT * FROM images WHERE product_id=? ORDER BY is_primary DESC, sort_order ASC, id ASC');
$imgs->execute([$p['id']]);
?>
<!DOCTYPE html><html lang="tr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Görsel Yükle</title>
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
  <h1><?php echo esc($p['name']); ?> &mdash; Görseller</h1>
  <form method="post" enctype="multipart/form-data" class="card" style="padding:12px;margin-bottom:16px">
    <input type="hidden" name="csrf" value="<?php echo esc(csrf_token()); ?>">
    <input type="file" name="images[]" accept="image/*" multiple required style="margin-bottom:8px">
    <button class="btn">Yükle</button>
  </form>
  <div class="grid">
    <?php foreach ($imgs as $im): ?>
      <div class="card">
        <img src="<?php echo esc($im['file_path']); ?>" style="width:100%;aspect-ratio:3/4;object-fit:cover">
        <div class="card-body">
          <form method="post" style="display:flex;gap:8px">
            <input type="hidden" name="csrf" value="<?php echo esc(csrf_token()); ?>">
            <button class="btn" name="primary" value="<?php echo $im['id']; ?>"><?php echo $im['is_primary'] ? 'Birincil ✓' : 'Birincil yap'; ?></button>
            <button class="btn ghost" name="delete" value="<?php echo $im['id']; ?>" onclick="return confirm('Silinsin mi?')">Sil</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>
</body></html>
