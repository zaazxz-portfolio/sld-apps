<?php
// src/pages/api/analyses.php
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/db.php';

// Parse request path: /api/analyses or /api/analyses/{id}
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($requestUri, '/'));
// parts: api, analyses, {id}
$id = $parts[2] ?? null;

if ($id !== null && ctype_digit($id)) {
    // Detail
    $stmt = $pdo->prepare("SELECT * FROM analyses WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => 'Analysis not found']);
        exit;
    }
    // Decode JSON fields
    $row->answers = json_decode($row->answers_json, true) ?? [];
    $row->scores = json_decode($row->scores_json, true) ?? [];
    $row->percentages = json_decode($row->percentages_json, true) ?? [];
    $row->ai_result = json_decode($row->ai_result_json, true) ?? [];
    unset($row->answers_json, $row->scores_json, $row->percentages_json, $row->ai_result_json);
    echo json_encode($row, JSON_UNESCAPED_UNICODE);
} else {
    // List all
    $stmt = $pdo->query("SELECT id, nama, nisn, kelas, sekolah, jenjang, jurusan, created_at FROM analyses ORDER BY id DESC");
    $rows = $stmt->fetchAll();
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
}