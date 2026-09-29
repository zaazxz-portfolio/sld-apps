<?php
// src/pages/api/models.php
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/db.php';

$stmt = $pdo->query("SELECT ai_api_key, ai_api_url FROM settings WHERE id = 1");
$settings = $stmt->fetch();

if (empty($settings->ai_api_key) || empty($settings->ai_api_url)) {
    echo json_encode(['error' => 'API Configuration not found.']);
    exit;
}

$apiUrl = rtrim($settings->ai_api_url, '/') . '/v1/models';
$apiKey = $settings->ai_api_key;

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode(['error' => 'Failed to fetch models', 'http_code' => $httpCode]);
    exit;
}

echo $response;