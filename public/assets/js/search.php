<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

$es_host = "https://157.180.88.2:9200";
$es_user = "elastic";
$es_pass = "wZrZc+Lkare0Ot4h8xI8";
$index   = "assistant";

$input = json_decode(file_get_contents("php://input"), true);
$question = $input['question'] ?? '';

if (!$question) {
    http_response_code(400);
    echo json_encode(["error" => "❌ لا يوجد سؤال مرسل"]);
    exit;
}

$query = [
    "query" => [
        "match" => [
            "question" => [
                "query" => $question,
                "fuzziness" => "AUTO"
            ]
        ]
    ]
];

$ch = curl_init("$es_host/$index/_search");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD        => "$es_user:$es_pass",
    CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
    CURLOPT_POSTFIELDS     => json_encode($query),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!$response || $httpCode !== 200) {
    http_response_code(500);
    echo json_encode([
        "error" => "⚠️ فشل الاتصال بـ ElasticSearch",
        "http_code" => $httpCode,
        "raw_response" => htmlentities($response)
    ]);
    exit;
}

$data = json_decode($response, true);
$hits = $data['hits']['hits'] ?? [];

if (count($hits) > 0) {
    $top = $hits[0]['_source'];
    echo json_encode([
        "answer"   => $top['answer'],
        "category" => $top['category'] ?? null,
        "score"    => $hits[0]['_score'],
        "note"     => "✔ الجواب من السيرفر الذكي"
    ]);
} else {
    echo json_encode(["answer" => null, "note" => "❌ لم يتم العثور على جواب"]);
}
?>
