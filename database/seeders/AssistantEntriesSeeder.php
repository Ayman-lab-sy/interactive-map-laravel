<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssistantEntriesSeeder extends Seeder
{
    public function run(): void
    {
        $path = public_path('assets/assistant-data.json');

        if (!file_exists($path)) {
            $this->command->error('assistant-data.json not found.');
            return;
        }

        $data = json_decode(file_get_contents($path), true);

        if (!is_array($data)) {
            $this->command->error('Invalid JSON.');
            return;
        }
        
        $this->command->info('📦 Loading assistant-data.json entries: ' . count($data));

        foreach ($data as $item) {
            $this->command->line("➡ Processing category: {$item['category']}");
            // CATEGORY
            $category = DB::connection('assistant')
                ->table('assistant_categories')
                ->where('name', $item['category'])
                ->first();

            if (!$category) {
                $this->command->warn(
                    "⛔ Category not found in DB: '{$item['category']}'"
                );
                continue;
            }


            // ENTRY
            $entryId = DB::connection('assistant')
                ->table('assistant_entries')
                ->insertGetId([
                    'category_id' => $category->id,
                    'tone'        => $item['tone'] ?? null,
                    'tags'        => isset($item['tags']) ? json_encode($item['tags']) : null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

            // ANSWERS
            foreach ($item['answers'] as $answer) {
                DB::connection('assistant')
                    ->table('assistant_answers')
                    ->insert([
                        'entry_id'   => $entryId,
                        'answer_text' => $answer,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            // KEYWORDS
            foreach ($item['keywords'] as $keyword) {
                DB::connection('assistant')
                    ->table('assistant_keywords')
                    ->insert([
                        'entry_id'   => $entryId,
                        'keyword'    => trim($keyword),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        }

        $this->command->info('Assistant data imported successfully.');
    }
}
