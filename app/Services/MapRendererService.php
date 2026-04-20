<?php

namespace App\Services;

use Illuminate\Http\Request;

class MapRendererService
{
    public function render(Request $request)
    {
        $stats = app(StatsService::class)->getStats($request);

        // تحميل SVG
        $svg = file_get_contents(storage_path('app/maps/syria.svg'));

        // نحصل على عدد الأحداث لكل محافظة
        $byGov = $stats['by_governorate'] ?? [];

        // 🔥 mapping بين DB و SVG
        $map = [
            'دمشق' => 'Damascus',
            'ريف دمشق' => 'Rural Damascus',
            'حلب' => 'Aleppo',
            'حمص' => 'Homs',
            'حماة' => 'Hama',
            'اللاذقية' => 'Lattakia',
            'طرطوس' => 'Tartous',
            'درعا' => 'Daraa',
            'السويداء' => 'As-Sweida',
            'القنيطرة' => 'Quneitra',
            'دير الزور' => 'Deir-ez-Zor',
            'الحسكة' => 'Al-Hasakeh',
            'الرقة' => 'Ar-Raqqa',
            'إدلب' => 'Idleb',
        ];

        // 🎨 تلوين
        foreach ($map as $ar => $en) {

            $count = $byGov[$ar] ?? 0;

            $color = $this->getColor($count);

            $svg = str_replace(
                'id="'.$en.'"',
                'id="'.$en.'" fill="'.$color.'" stroke="#1f2937" stroke-width="1"',
                $svg
            );
        }

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    private function getColor($count)
    {
        if ($count >= 30) return '#7f0000'; // 🔴 أحمر داكن (خطر شديد)
        if ($count >= 20) return '#b91c1c'; // 🔴 أحمر قوي
        if ($count >= 10) return '#ef4444'; // 🔴 أحمر متوسط
        if ($count >= 5)  return '#f97316'; // 🟠 برتقالي
        if ($count >= 2)  return '#facc15'; // 🟡 أصفر
        if ($count >= 1)  return '#fde68a'; // 🟡 أصفر فاتح
        return '#374151'; // ⚫ رمادي (لا أحداث)
    }
}