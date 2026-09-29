<?php
// src/pages/api/analyze.php
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/db.php';

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$student = $data['student'] ?? [];
$scores = $data['scores'] ?? [];
$percentages = $data['percentages'] ?? [];
$answers = $data['answers'] ?? [];

// Fetch Settings
$stmt = $pdo->query("SELECT ai_api_key, ai_api_url, ai_model FROM settings WHERE id = 1");
$settings = $stmt->fetch();

if (empty($settings->ai_api_key) || empty($settings->ai_api_url)) {
    echo json_encode(['error' => 'API Configuration not found.']);
    exit;
}

// 9Router uses OpenAI /v1/chat/completions format by default
$apiUrl = rtrim($settings->ai_api_url, '/') . '/v1/chat/completions';
$apiKey = $settings->ai_api_key;

$systemPrompt = "Anda adalah Pakar Psikologi Pendidikan dan Kurikulum Merdeka Indonesia. Tugas Anda adalah memberikan analisis presisi mengenai gaya belajar siswa berdasarkan data asesmen diagnosis VAK (Visual, Auditori, Kinestetik).\nBerikan luaran berupa format JSON MURNI tanpa markdown penutup atau pembuka triple backtick.";

$userQuery = "Analisis profil siswa berikut:\n";
$userQuery .= "- Nama: " . ($student['nama'] ?? 'Unknown') . "\n";
$jurusanText = !empty($student['jurusan']) ? " (Jurusan: " . $student['jurusan'] . ")" : "";
$userQuery .= "- Jenjang: " . ($student['jenjang'] ?? '') . $jurusanText . "\n";
$userQuery .= "- Sekolah: " . ($student['sekolah'] ?? '') . "\n";
$userQuery .= "- Perolehan Skor VAK: Visual " . ($percentages['visual'] ?? 0) . "% (" . ($scores['visual'] ?? 0) . " soal), Auditori " . ($percentages['auditori'] ?? 0) . "% (" . ($scores['auditori'] ?? 0) . " soal), Kinestetik " . ($percentages['kinestetik'] ?? 0) . "% (" . ($scores['kinestetik'] ?? 0) . " soal).\n\n";

$smkRule = (($student['jenjang'] ?? '') === 'SMK') ? " (Khusus SMK wajib dikaitkan dengan metode praktik bengkel/laboratorium/dunia kerja)" : "";

$userQuery .= "Wajib menghasilkan JSON murni dengan skema berikut:\n{\n";
$userQuery .= '  "analisis_karakteristik": "Penjelasan minimal 3 kalimat mengenai cara siswa menyerap informasi berdasarkan gaya belajar dominannya serta potensi hambatannya.",'."\n";
$userQuery .= '  "rekomendasi_mandiri": ["Poin 1", "Poin 2", "Poin 3"],'."\n";
$userQuery .= '  "rekomendasi_sekolah": ["Poin 1", "Poin 2", "Poin 3"]' . $smkRule . ",\n";
$userQuery .= '  "media_digital": ["Media 1", "Media 2"],'."\n";
$userQuery .= '  "panduan_guru": "Instruksi taktis untuk guru dalam menerapkan pembelajaran berdiferensiasi pada siswa ini."'."\n";
$userQuery .= "}";

$payload = [
    "model" => $settings->ai_model ?: "coding",
    "messages" => [
        ["role" => "system", "content" => $systemPrompt],
        ["role" => "user", "content" => $userQuery]
    ],
    "stream" => false
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode([
        'error' => 'LLM API Error', 
        'http_code' => $httpCode,
        'curl_error' => $curlError,
        'details' => $response
    ]);
    exit;
}

$respData = json_decode($response, true);
$aiResultText = $respData['choices'][0]['message']['content'] ?? '{}';

// Clean AI Result
$aiResultText = preg_replace('/^```(?:json)?\s*/i', '', trim($aiResultText));
$aiResultText = preg_replace('/\s*```$/', '', $aiResultText);
preg_match('/\{[\s\S]*\}/', $aiResultText, $matches);
if (!empty($matches)) {
    $aiResultText = $matches[0];
}

// Save full analysis to database
$stmt = $pdo->prepare("
    INSERT INTO analyses (nama, nisn, kelas, sekolah, jenjang, jurusan, answers_json, scores_json, percentages_json, ai_result_json, created_at)
    VALUES (:nama, :nisn, :kelas, :sekolah, :jenjang, :jurusan, :answers, :scores, :percentages, :ai_result, :created_at)
");
$stmt->execute([
    'nama'         => $student['nama'] ?? '',
    'nisn'         => $student['nisn'] ?? '',
    'kelas'        => $student['kelas'] ?? '',
    'sekolah'      => $student['sekolah'] ?? '',
    'jenjang'      => $student['jenjang'] ?? '',
    'jurusan'      => $student['jurusan'] ?? '',
    'answers'      => json_encode($answers, JSON_UNESCAPED_UNICODE),
    'scores'       => json_encode($scores, JSON_UNESCAPED_UNICODE),
    'percentages'  => json_encode($percentages, JSON_UNESCAPED_UNICODE),
    'ai_result'    => $aiResultText,
    'created_at'   => date('Y-m-d H:i:s'),
]);

$analysisId = $pdo->lastInsertId();

// Return JSON directly to frontend with saved ID
echo json_encode([
    'id' => $analysisId,
    'ai_result' => json_decode($aiResultText, true)
], JSON_UNESCAPED_UNICODE);