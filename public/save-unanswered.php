<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['question'])) {
    http_response_code(400);
    exit("Invalid input");
}

$logFile = __DIR__ . "/unanswered.json";

// حمّل الموجود وتأكد أنه Array
$existing = [];
if (file_exists($logFile)) {
    $decoded = json_decode(file_get_contents($logFile), true);
    if (is_array($decoded)) {
        $existing = $decoded;
    }
}

// أضف بالسكيما الجديدة
$existing[] = [
    "id"         => "q_" . uniqid(),
    "question"   => trim($data['question']),
    "expanded"   => trim($data['expanded'] ?? $data['question']),
    "status"     => "new",
    "created_at" => date("c")
];

// احفظ
file_put_contents(
    $logFile,
    json_encode($existing, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
);

echo json_encode(["ok" => true]);
