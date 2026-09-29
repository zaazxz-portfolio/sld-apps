<?php

// src/includes/db.php

date_default_timezone_set('Asia/Jakarta');

$dbPath = $_ENV['DB_SQLITE_PATH'] ?? (__DIR__ . '/../../database.db');

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $pdo->exec('PRAGMA journal_mode=WAL');
    $pdo->exec('PRAGMA foreign_keys=ON');

    // Auto-create tables if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY CHECK (id = 1),
        ai_api_key TEXT DEFAULT '',
        ai_api_url TEXT DEFAULT '',
        ai_model TEXT DEFAULT 'coding',
        updated_at TEXT DEFAULT (datetime('now', 'localtime'))
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS analyses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nama TEXT NOT NULL DEFAULT '',
        nisn TEXT DEFAULT '',
        kelas TEXT DEFAULT '',
        sekolah TEXT DEFAULT '',
        jenjang TEXT DEFAULT '',
        jurusan TEXT DEFAULT '',
        answers_json TEXT NOT NULL DEFAULT '[]',
        scores_json TEXT NOT NULL DEFAULT '{}',
        percentages_json TEXT NOT NULL DEFAULT '{}',
        ai_result_json TEXT DEFAULT '{}',
        created_at TEXT DEFAULT (datetime('now', 'localtime'))
    )");
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
