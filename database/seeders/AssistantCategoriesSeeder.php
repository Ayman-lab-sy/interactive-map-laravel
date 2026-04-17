<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssistantCategoriesSeeder extends Seeder
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

        $categories = collect($data)
            ->pluck('category')
            ->unique()
            ->values();

        $this->command->info('📂 Importing categories: ' . $categories->count());

        foreach ($categories as $name) {
            DB::connection('assistant')
                ->table('assistant_categories')
                ->updateOrInsert(
                    ['name' => $name],
                    [
                        'slug' => Str::slug($name, '-'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

            $this->command->line("✔ Category added: {$name}");
        }
        $this->command->info('✅ Categories imported successfully.');
    }
}
