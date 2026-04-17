<?php

namespace App\Services\Assistant;

class IntentResolver
{
    /**
     * ترتيب الأولوية (الأعلى أولًا)
     */
    private array $priority = [
        'emergency'   => 100,
        'frustration' => 95,
        'legal'       => 90,
        'humanitarian'=> 80,
        'documentation'=>70,
        'definition'  => 60,
        'join'        => 50,
    ];

    /**
     * قاموس النوايا (نسخة أولى – بسيطة)
     */
    private array $intents = [
        'emergency' => [
            'keywords' => ['خطر','طارئ','عاجل','مساعدة فورية','تهديد','انقذوني','ساعدوني'],
        ],
        'legal' => [
            'keywords' => ['قانوني','محامي','دعوى','قضية','دعم قانوني'],
        ],
        'humanitarian' => [
            'keywords' => ['مساعدة انسانية','غذاء','دواء','مأوى','إغاثة'],
        ],
        'documentation' => [
            'keywords' => ['توثيق','انتهاك','انتهاكات','شهادة','بلاغ'],
        ],
        'definition' => [
            'keywords' => ['تعريف','من انتم','من أنتم','عن المنظمة','هدف المنظمة','دور المنظمة'],
        ],
        'join' => [
            'keywords' => ['انضمام','الانضمام','كيف انضم','تطوع','عضوية'],
        ],
        'frustration' => [
            'keywords' => ['حاسس ما حدا سامعنا','ما حدا عم يسمع','ما في تجاوب','تعبت','محبط','يئست','جربت احكي وما لقيت تجاوب','يأس','ما عاد بعرف شو اعمل','ما في فايدة'],
        ],
    ];

    /**
     * يحلل السؤال ويرجع النية الأعلى أولوية إن وُجدت
     */
    public function resolve(string $normalizedQuestion): ?string
    {
        $found = [];

        foreach ($this->intents as $intent => $data) {
            foreach ($data['keywords'] as $kw) {
                $kwNorm = $this->normalize($kw);
                if (mb_stripos($normalizedQuestion, $kwNorm) !== false) {
                    $found[$intent] = $this->priority[$intent] ?? 0;
                }
            }
        }

        if (empty($found)) {
            return null;
        }

        arsort($found);
        return array_key_first($found);
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[\x{064B}-\x{065F}]/u', '', $text);
        $text = str_replace(['أ','إ','آ'], 'ا', $text);
        $text = str_replace(['ى'], 'ي', $text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }
}
