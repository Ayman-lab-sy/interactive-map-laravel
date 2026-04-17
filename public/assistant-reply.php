<?php
// اسم الملف: assistant-reply.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['questionId']) || !isset($data['answer'])) {
  http_response_code(400);
  echo json_encode(["error" => "بيانات ناقصة"]);
  exit;
}

$filepath = __DIR__ . "/assistant-replies.json";

// ✅ فك الترميز إن وجد
$cleanAnswer = $data['answer'];
$cleanAnswer = urldecode($cleanAnswer);
$cleanAnswer = htmlspecialchars_decode($cleanAnswer, ENT_QUOTES);

// تأكد من إزالة الرموز الزائدة مثل \/ إن وُجدت
$cleanAnswer = str_replace("\\/", "/", $cleanAnswer);

// قراءة الملف الحالي أو بدء مصفوفة جديدة
$replies = file_exists($filepath) ? json_decode(file_get_contents($filepath), true) : [];

$replies[] = [
  "questionId" => $data['questionId'],
  "answer"     => $cleanAnswer,
  "type"       => $data['type'] ?? 'reply',
  "from"       => $data['from'] ?? "الدعم",
  "timestamp"  => date("c")
];


// حفظ الملف
file_put_contents($filepath, json_encode($replies, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo json_encode(["status" => "تم حفظ الرد"]);
