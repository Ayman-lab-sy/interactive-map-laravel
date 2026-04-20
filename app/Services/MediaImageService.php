<?php

namespace App\Services;

use Illuminate\Http\Request;
use Imagick;
use ImagickDraw;
use ImagickPixel;
use ArPHP\I18N\Arabic;

class MediaImageService
{
    public function generate(Request $request, $post = null)
    {
        $type = $post['type'] ?? 'report';
        $stats = app(StatsService::class)->getStats($request);

        $total = $stats['total'] ?? 0;
        $gov = $stats['top_governorate']->governorate ?? 'سوريا';
        $arabic = new \ArPHP\I18N\Arabic();

        // 🎨 Canvas
        $image = new Imagick();
        $image->newImage(1200, 630, new ImagickPixel('#020617'));

        // 🗺️ جلب الخريطة الديناميكية (SVG)
        $mapSvg = file_get_contents(url('/api/media/map?range=week'));

        // حفظ مؤقت
        $tmpSvg = storage_path('app/temp_map.svg');
        file_put_contents($tmpSvg, $mapSvg);

        // تحويل إلى Imagick
        $map = new \Imagick();
        $map->readImage($tmpSvg);
        $map->setImageBackgroundColor(new \ImagickPixel('transparent'));
        $map = $map->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
        $map->transparentPaintImage(
            new \ImagickPixel('white'),
            0,
            10,
            false
        );

        // تحسين الجودة
        $map->setImageFormat('png');
        $map->resizeImage(800, 0, \Imagick::FILTER_LANCZOS, 1);
        $map->gaussianBlurImage(0.5, 0.5);

        // نحسب أبعاد الخريطة
        $mapWidth = $map->getImageWidth();
        $mapHeight = $map->getImageHeight();

        // نحسب مكان التوسيط
        $x = (1200 - $mapWidth) / 2;
        $y = (630 - $mapHeight) / 2;

        // دمج الخريطة بالمنتصف
        $image->compositeImage($map, \Imagick::COMPOSITE_OVER, $x, $y);

        //طبقة شفافة
        $overlay = new Imagick();
        $overlay->newImage(1200, 630, new ImagickPixel('rgba(0,0,0,0.15)'));
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, 0, 0);

        // 🎯 لون حسب نوع المحتوى
        switch ($type) {
            case 'alert':
                $color = '#ef4444'; // 🔴
                break;
            case 'insight':
                $color = '#8b5cf6'; // 🟣
                break;
            case 'social':
                $color = '#f59e0b'; // 🟡
                break;
            default:
                $color = '#3b82f6'; // 🔵 report
        }

        //نقرير
        $badge = new ImagickDraw();
        $badge->setFillColor($color);
        $badge->roundRectangle(50, 50, 350, 130, 20, 20);
        $image->drawImage($badge);

        $badgeText = new ImagickDraw();
        $badgeText->setFont(storage_path('app/fonts/Tajawal-Bold.ttf'));
        $badgeText->setFillColor('white');
        $badgeText->setFontSize(40);
        $badgeText->setTextAlignment(Imagick::ALIGN_CENTER);

        switch ($type) {
            case 'alert':
                $label = "🚨 عاجل";
                break;
            case 'insight':
                $label = "📈 تحليل";
                break;
            case 'social':
                $label = "📢 تحديث";
                break;
            default:
                $label = "📊 تقرير";
        }
        $badgeLabel = $arabic->utf8Glyphs($label);
        $image->annotateImage($badgeText, 200, 105, 0, $badgeLabel);

        // 🧾 النص
        $draw = new ImagickDraw();
        $draw->setFillColor('white');
        $draw->setFont(storage_path('app/fonts/Tajawal-Bold.ttf'));
        $draw->setTextAlignment(Imagick::ALIGN_CENTER);

        // 🔥 معالجة العربي
        $title = $arabic->utf8Glyphs("الوضع الميداني");
        $govText = $arabic->utf8Glyphs("📍 {$gov}");
        $totalText = $arabic->utf8Glyphs((string)$total);
        $subtitle = $arabic->utf8Glyphs("خلال الفترة الحالية");

        // العنوان
        $draw->setFontSize(60);
        $shadow = clone $draw;
        $shadow->setFillColor('rgba(0,0,0,0.7)');
        $image->annotateImage($shadow, 602, 152, 0, $title);
        $image->annotateImage($draw, 600, 150, 0, $title);

        $textBg = new ImagickDraw();
        $textBg->setFillColor('rgba(0,0,0,0.6)');
        $textBg->roundRectangle(300, 250, 900, 550, 30, 30);
        $image->drawImage($textBg);
        // الرقم
        $draw->setFontSize(180);
        $shadow = clone $draw;
        $shadow->setFillColor('rgba(0,0,0,0.9)');
        $image->annotateImage($shadow, 602, 362, 0, $totalText);
        $image->annotateImage($draw, 600, 360, 0, $totalText);
        $glow = clone $draw;
        $glow->setFillColor('rgba(255,255,255,0.15)');
        $image->annotateImage($glow, 600, 360, 0, $totalText);

        //الفترة
        $draw->setFontSize(35);
        $image->annotateImage($draw, 600, 430, 0, $subtitle);

        // المحافظة
        $draw->setFontSize(70);
        $draw->setFillColor($color);
        $image->annotateImage($draw, 600, 520, 0, $govText);

        $image->setImageFormat('png');
        $tmpPath = storage_path('app/report.png');
        $image->writeImage($tmpPath);

        return $tmpPath;
    }
}