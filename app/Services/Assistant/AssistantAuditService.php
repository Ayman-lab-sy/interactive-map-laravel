<?php

namespace App\Services\Assistant;

use Illuminate\Support\Facades\DB;

class AssistantAuditService
{
    public function all(): array
    {
        return DB::connection('assistant')
            ->table('assistant_audit_logs')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($row) {
                return [
                    'id'         => $row->id,
                    'action'     => $row->action,
                    'question_id'=> $row->question_id,
                    'category'   => $row->category_name ?? null,
                    'keywords'   => $row->payload
                        ? (json_decode($row->payload, true)['keywords'] ?? null)
                        : null,
                    'payload'    => $row->payload, // ⭐ مهم جدًا
                    'summary'    => $row->payload
                        ? (json_decode($row->payload, true)['summary'] ?? null)
                        : null,
                    'admin'      => $row->admin_email,
                    'created_at' => $row->created_at,
                ];
            })
            ->toArray();
    }
}
