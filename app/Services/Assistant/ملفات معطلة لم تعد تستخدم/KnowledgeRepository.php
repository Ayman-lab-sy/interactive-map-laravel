<?php

namespace App\Services\Assistant;

class KnowledgeRepository
{
    protected string $dataPath;

    public function __construct()
    {
        $this->dataPath = base_path('public/assets/assistant-data.json');
    }

    /**
     * إرجاع كل المعرفة بصيغة جاهزة للمحرّك
     */
    public function all(): array
    {
        if (!file_exists($this->dataPath)) {
            return [];
        }

        $raw = json_decode(file_get_contents($this->dataPath), true);

        if (!is_array($raw)) {
            return [];
        }

        return $this->normalizeEntries($raw);
    }
    
    public function categories(): array
    {
        $all = $this->all();

        return collect($all)
            ->groupBy('category')
            ->map(fn($items) => [
                'keywords' => $items->flatMap(fn($i) => $i['keywords'])->unique()->values(),
                'answers'  => $items->flatMap(fn($i) => $i['answers'])->unique()->values(),
            ])
            ->toArray();
    }

    /**
     * توحيد شكل البيانات
     */
    protected function normalizeEntries(array $entries): array
    {
        return array_map(function ($entry) {
            return [
                'category' => $entry['category'] ?? null,
                'keywords' => array_map('mb_strtolower', $entry['keywords'] ?? []),
                'answers'  => $entry['answers']
                    ?? (isset($entry['answer']) ? [$entry['answer']] : []),
                'tags'     => $entry['tags'] ?? [],
                'tone'     => $entry['tone'] ?? null,
            ];
        }, $entries);
    }
}
