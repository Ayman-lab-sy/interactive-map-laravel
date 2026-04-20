<?php

namespace App\Services\Content;

class SocialBuilder
{
    public function build($stats, $lang = 'ar')
    {
        return $lang === 'en'
            ? $this->buildEnglish($stats)
            : $this->buildArabic($stats);
    }

    // =========================
    // 🇸🇦 عربي (Viral)
    // =========================
    private function buildArabic($stats)
    {
        $total = $stats['total'] ?? 0;
        $gov = $stats['top_governorate']->governorate ?? 'عدة مناطق';

        $hooks = [
            "🚨 تصاعد لافت في الأحداث اليوم!",
            "⚠️ الوضع يتجه نحو مزيد من التوتر!",
            "📢 أرقام مقلقة تسجل اليوم!",
            "🔥 تطورات متسارعة على الأرض!",
        ];

        $bodies = [
            "تم تسجيل {$total} حادثة، معظمها في {$gov}.",
            "{$total} حدث موثق اليوم، مع تصدر {$gov}.",
            "الأحداث وصلت إلى {$total} حالة، و{$gov} في الصدارة.",
            "{$gov} تسجل أعلى عدد من الأحداث ضمن {$total} حالة."
        ];

        $hook = $this->pick($hooks);
        $body = $this->pick($bodies);

        $text = "{$hook}\n\n{$body}";

        return [
            'type' => 'social',
            'priority' => 'normal',
            'data' => [
                'title' => "📢 تحديث سريع",
                'content' => $text,
                'hashtags' => $this->hashtagsAr($gov)
            ]
        ];
    }

    // =========================
    // 🇬🇧 English (Viral)
    // =========================
    private function buildEnglish($stats)
    {
        $total = $stats['total'] ?? 0;
        $gov = $stats['top_governorate']->governorate ?? 'multiple areas';

        $hooks = [
            "🚨 Sharp escalation in events today!",
            "⚠️ Situation is becoming increasingly tense!",
            "📢 Concerning numbers reported today!",
            "🔥 Rapid developments on the ground!",
        ];

        $bodies = [
            "{$total} incidents recorded, most in {$gov}.",
            "{$total} verified events today, with {$gov} leading.",
            "Events reached {$total}, with {$gov} at the top.",
            "{$gov} recorded the highest share out of {$total} events."
        ];

        $hook = $this->pick($hooks);
        $body = $this->pick($bodies);

        $text = "{$hook}\n\n{$body}";

        return [
            'type' => 'social',
            'priority' => 'normal',
            'data' => [
                'title' => "📢 Quick Update",
                'content' => $text,
                'hashtags' => $this->hashtagsEn($gov)
            ]
        ];
    }

    private function hashtagsAr($gov)
    {
        return [
            '#سوريا',
            "#{$gov}",
            '#عاجل',
            '#أخبار',
            '#متابعة'
        ];
    }

    private function hashtagsEn($gov)
    {
        return [
            '#Syria',
            "#{$gov}",
            '#Breaking',
            '#News',
            '#Update'
        ];
    }

    private function pick($arr)
    {
        return $arr[array_rand($arr)];
    }
}