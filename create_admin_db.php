<?php
$dbFile = __DIR__ . '/storage/admin.sqlite';

// Create DB file and connect
$pdo = new PDO("sqlite:$dbFile");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create users table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role_id INTEGER,
        status TEXT DEFAULT 'active'
    )
");

// Hash password 'admin123'
$passwordHash = password_hash('admin123', PASSWORD_DEFAULT);

// Insert default Super Admin user
$stmt = $pdo->prepare("
    INSERT OR IGNORE INTO users (name, email, password, role_id, status)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute(['Super Admin', 'admin@example.com', $passwordHash, 1, 'active']);

echo "✅ admin.sqlite created and user inserted.\n";
