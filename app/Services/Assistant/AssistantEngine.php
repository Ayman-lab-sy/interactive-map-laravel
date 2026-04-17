<?php

namespace App\Services\Assistant;

class AssistantEngine
{
    protected KnowledgeRepository $knowledge;

    public function __construct(KnowledgeRepository $knowledge)
    {
        $this->knowledge = $knowledge;
    }

    /**
     * النقطة الوحيدة التي يدخل منها أي سؤال
     */
    public function handle(string $question): array
    {
        $normalized = $this->normalize($question);
        $expanded   = $this->expandWithSynonyms($normalized);

        $entries = $this->knowledge->all();

        $scored = [];

        foreach ($entries as $entry) {
            $score = $this->scoreEntry($expanded, $entry);
            if ($score > 0) {
                $scored[] = [
                    'entry' => $entry,
                    'score' => $score,
                ];
            }
        }

        // ترتيب حسب الأعلى
        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        // 🔹 حالة: تطابق واحد واضح
        if (count($scored) === 1) {
            return $this->localAnswer($scored[0]['entry']);
        }

        // 🔹 حالة: أكثر من تطابق → نطلب من السيرفر
        if (count($scored) > 1) {
            return [
                'action' => 'server',
                'question' => $normalized,
            ];
        }

        // 🔹 حالة: لا يوجد تطابق
        return [
            'action' => 'unanswered',
            'question' => $question,
            'expanded' => $expanded,
        ];
    }

    /* ------------------------
       Helpers
    ------------------------ */

    protected function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        return $text;
    }

    protected function expandWithSynonyms(string $text): string
    {
        if (!function_exists('config')) {
            return $text;
        }

        $synonyms = config('assistant.synonyms', []);
        $words = explode(' ', $text);
        $expanded = [];

        foreach ($words as $word) {
            $expanded[] = $word;
            if (isset($synonyms[$word])) {
                foreach ($synonyms[$word] as $syn) {
                    $expanded[] = $syn;
                }
            }
        }

        return implode(' ', array_unique($expanded));
    }

    protected function scoreEntry(string $input, array $entry): int
    {
        $score = 0;

        foreach ($entry['keywords'] ?? [] as $keyword) {
            $keyword = mb_strtolower($keyword);

            if ($input === $keyword) {
                $score += 50;
            } elseif (str_contains($input, $keyword)) {
                $score += strlen($keyword) > 5 ? 10 : 5;
            }
        }

        return $score;
    }

    protected function localAnswer(array $entry): array
    {
        $answers = $entry['answers'] ?? [];
        $answer = $answers
            ? $answers[array_rand($answers)]
            : '🤖 لم أجد جوابًا مناسبًا.';

        return [
            'action' => 'local',
            'answer' => $answer,
        ];
    }
}
