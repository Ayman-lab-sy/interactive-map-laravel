<?php

namespace App\Services\Assistant;

use Illuminate\Support\Facades\DB;

class UnansweredRepository
{
    protected $db;

    public function __construct()
    {
        $this->db = DB::connection('assistant')->table('assistant_unanswered');
    }

    public function all(): array
    {
        return $this->db
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($row) => $this->mapRow($row))
            ->all();
    }

    public function byStatus(string $status): array
    {
        return $this->db
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($row) => $this->mapRow($row))
            ->all();
    }

    public function findById(string $id): ?array
    {
        $row = $this->db->where('id', $id)->first();
        return $row ? $this->mapRow($row) : null;
    }

    public function approve(string $id): bool
    {
        return (bool) $this->db
            ->where('id', $id)
            ->update([
                'status'      => 'approved',
                'approved_at' => now(),
                'updated_at'  => now(),
            ]);
    }

    public function ignore(string $id): bool
    {
        return (bool) $this->db
            ->where('id', $id)
            ->update([
                'status'     => 'ignored',
                'updated_at' => now(),
            ]);
    }
    
    public function log(string $question, string $expanded): void
    {
        // منع التكرار (حسب النص الأصلي)
        $exists = $this->db
            ->where('question_text', $question)
            ->exists();

        if ($exists) {
            return;
        }

        $this->db->insert([
            'external_id'   => \Illuminate\Support\Str::uuid()->toString(),
            'question_text' => $question,
            'expanded_text' => $expanded,
            'status'        => 'new',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    protected function mapRow($row): array
    {
        return [
            'id'         => (string) $row->id,
            'external_id'=> $row->external_id,
            'question'   => $row->question_text,
            'expanded'   => $row->expanded_text,
            'status'     => $row->status,
            'approved_at'=> $row->approved_at,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ];
    }
}
