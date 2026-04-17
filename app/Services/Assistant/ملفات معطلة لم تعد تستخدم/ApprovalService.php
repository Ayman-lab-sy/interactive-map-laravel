<?php

namespace App\Services\Assistant;

use App\Services\Assistant\AssistantLogger;
use App\Services\Assistant\AssistantAuditService;


class ApprovalService
{
    protected string $knowledgeFile;
    protected string $unansweredFile;
    protected AssistantLogger $logger;
    protected AssistantAuditService $audit;
    
    public function __construct(AssistantAuditService $audit)
    {
        $this->knowledgeFile  = public_path('assets/assistant-data.json');
        $this->unansweredFile = public_path('unanswered.json');
        $this->logger         = new AssistantLogger();
        $this->audit          = $audit;
    }

    public function convert(
        string $draftId,
        string $category,
        array $keywords,
        string $answer,
        string $adminEmail
    ): void {
        // 1️⃣ تحميل المعرفة
        $knowledge = json_decode(file_get_contents($this->knowledgeFile), true) ?? [];

        $found = false;

        foreach ($knowledge as &$item) {
            if ($item['category'] === $category) {

                // دمج الكلمات المفتاحية
                $item['keywords'] = $this->uniqueKeywords(
                    $item['keywords'] ?? [],
                    $keywords
                );

                if ($answer !== '' && !$this->answerExists($item['answers'] ?? [], $answer)) {
                    $item['answers'][] = $answer;
                }

                $found = true;
                break;
            }
        }

        // 2️⃣ إذا التصنيف غير موجود → أنشئه
        if (!$found) {
            $knowledge[] = [
                'category' => $category,
                'keywords' => array_values(array_unique($keywords)),
                'answers'  => $answer !== '' ? [$answer] : [],
                'tone'     => 'محايد',
                'tags'     => [],
            ];
        }

        file_put_contents(
            $this->knowledgeFile,
            json_encode($knowledge, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        // 3️⃣ تحديث حالة السؤال
        $unanswered = json_decode(file_get_contents($this->unansweredFile), true) ?? [];

        foreach ($unanswered as &$q) {
            if (($q['id'] ?? null) === $draftId) {
                $q['status'] = 'approved';
                $q['approved_at'] = date('c');
                break;
            }
        }

        file_put_contents(
            $this->unansweredFile,
            json_encode($unanswered, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        // 4️⃣ Logging
        $this->logger->log([
            'question_id'    => $draftId,
            'category'       => $category,
            'keywords_added' => $keywords,
            'answer_added'   => $answer !== '',
            'admin_user' => $adminEmail,
        ]);
        $summary = $answer !== ''
            ? mb_substr(strip_tags($answer), 0, 160)
            : null;

        // 5️⃣ Audit Trail (إداري)
        $this->audit->log([
            'action'      => 'convert_to_knowledge',
            'question_id' => $draftId,
            'category'    => $category,
            'keywords'    => $keywords,
            'answer'      => $answer !== '' ? $answer : null,
            'admin'       => $adminEmail,
            'summary'     => $summary,
        ]);
    }

    protected function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/\s+/u', ' ', $text);
        return $text;
    }

    protected function uniqueKeywords(array $existing, array $incoming): array
    {
        $map = [];

        foreach ($existing as $word) {
            $map[$this->normalize($word)] = $word;
        }

        foreach ($incoming as $word) {
            $key = $this->normalize($word);
            if (!isset($map[$key])) {
                $map[$key] = $word;
            }
        }

        return array_values($map);
    }

    protected function answerExists(array $answers, string $newAnswer): bool
    {
        $needle = $this->normalize($newAnswer);

        foreach ($answers as $ans) {
            if ($this->normalize($ans) === $needle) {
                return true;
            }
        }

        return false;
    }

}
