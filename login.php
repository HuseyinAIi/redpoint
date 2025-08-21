<?php
require __DIR__ . '/../config.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM users WHERE username=?');
    $stmt->execute([$u]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($p, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: /admin/'); exit;
    } else {
        $error = 'Geçersiz kullanıcı adı veya şifre';
    }
}
?>
<!DOCTYPE html><html lang="tr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head><body>
<div class="container" style="max-width:420px;padding:48px 16px">
  <h1>Admin Login</h1>
  <?php if (!empty($error)) echo '<p class="muted" style="color:#fca5a5">'.esc($error).'</p>'; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?php echo esc(csrf_token()); ?>">
    <div style="margin:8px 0"><input required name="username" placeholder="Kullanıcı adı" style="width:100%;padding:12px;border-radius:10px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea"></div>
    <div style="margin:8px 0"><input required name="password" type="password" placeholder="Şifre" style="width:100%;padding:12px;border-radius:10px;border:1px solid #2a2a2e;background:#131318;color:#e6e6ea"></div>
    <button class="btn" type="submit">Giriş Yap</button>
  </form>
</div>
</body></html>
