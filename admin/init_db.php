<?php
require __DIR__ . '/config.php';
$pdo = db();
$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL
);
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    model TEXT,
    material TEXT,
    fit TEXT,
    color TEXT,
    sizes TEXT,    -- JSON array of sizes (e.g., "28,30,32" for denim) 
    lengths TEXT,  -- JSON array (e.g., "30,32,34")
    description TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    file_path TEXT NOT NULL,
    is_primary INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
");

$hasAdmin = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if (!$hasAdmin) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->execute(['admin', $hash]);
}

# Seed default categories
$existing = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
if (!$existing) {
    $cats = [
        ['Denim', 'denim'],
        ['T-Shirts', 't-shirts'],
        ['Jogger', 'jogger'],
        ['Shirts', 'shirts'],
        ['Jacket', 'jacket'],
    ];
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
    foreach ($cats as $c) { $stmt->execute($c); }
}

echo 'DB initialized. Default admin: admin / admin123';
