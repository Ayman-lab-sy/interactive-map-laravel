<?php

namespace App\Services\Assistant;

class UnansweredReader
{
    protected string $file;

    public function __construct()
    {
        $this->file = public_path('unanswered.json');
    }

    public function all(): array
    {
        if (!file_exists($this->file)) {
            return [];
        }

        $data = json_decode(file_get_contents($this->file), true);

        return is_array($data) ? array_reverse($data) : [];
    }

    public function byStatus(string $status): array
    {
        return array_filter($this->all(), fn($q) => ($q['status'] ?? '') === $status);
    }
}
