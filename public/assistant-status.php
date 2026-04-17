<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['questionId'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing questionId"]);
    exit;
}

$questionId = $data['questionId'];

$filepath = __DIR__ . "/assistant-status.json";

// قراءة الملف الحالي أو إنشاء مصفوفة جديدة
$status = file_exists($filepath)
    ? json_decode(file_get_contents($filepath), true)
    : [];

// إذا كانت الحالة موجودة مسبقًا، لا نعيد الكتابة
if (!isset($status[$questionId])) {
    $status[$questionId] = [
        "connected" => true,
        "timestamp" => date("c")
    ];

    file_put_contents(
        $filepath,
        json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

echo json_encode(["status" => "connected"]);
