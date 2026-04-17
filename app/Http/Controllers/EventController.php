<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Spatie\Browsershot\Browsershot;
use Imagick;
use ImagickDraw;
use ImagickPixel;
use ArPHP\I18N\Arabic;


class EventController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->only([
            'range', 'from', 'to', 'governorate', 'north', 'south', 'east', 'west'
        ]);
        ksort($params);
        $cacheKey = 'events_' . md5(json_encode($params));
        $query = Event::where('status', 'verified');

        // ===== فلتر المحافظة =====
        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }

        // 🔥 1. فلترة مخصصة (أولوية)
        if ($request->filled('from') && $request->filled('to')) {

            $query->whereBetween('event_date', [
                $request->from,
                $request->to
            ]);

        }

        // 🔥 2. الفلاتر القديمة
        elseif ($request->filled('range')) {

            if ($request->range === 'today') {
                $query->whereBetween('event_date', [
                    now()->startOfDay(),
                    now()->endOfDay()
                ]);
            }
            elseif ($request->range === 'week') {
                $query->where('event_date', '>=', now()->subDays(7));
            }
            elseif ($request->range === 'month') {
                $query->where('event_date', '>=', now()->subDays(30));
            }
        }
        
        $globalTotal = (clone $query)->count();
        
        // ===== فلتر حسب حدود الخريطة =====
        if ($request->has(['north', 'south', 'east', 'west'])) {
            $query->whereBetween('lat', [$request->south, $request->north])
                ->whereBetween('lng', [$request->west, $request->east]);
        }

        $countQuery = clone $query;
        $total = $countQuery->count();

        $realTotal = (clone $query)->count();

        $events = cache()->remember($cacheKey, now()->addMinutes(2), function () use ($query) {
            return $query->orderBy('event_date', 'desc')->paginate(150, [
                'id',
                'title',
                'lat',
                'lng',
                'category',
                'event_date',
                'governorate',
                'confidence_level',
                'confidence_score',
                'sources_count'
            ]);
        });

        return response()->json([
            'data' => $events->items(),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage()
            ],
            'meta' => [
                'count' => count($events->items()),
                'total' => $total,
                'global_total' => $globalTotal,
                'real_total' => $realTotal
            ]
        ]);
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);

        return response()->json($event);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'category' => 'required|string',
            'event_date' => 'required|date',
            'governorate' => 'required|string',
            'city' => 'required|string',
            'sources_count' => 'nullable|integer|min:0|max:10',
            'sources_diverse' => 'nullable|boolean',
            'video_url' => 'nullable|url'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

        // =========================
        // 🔥 حساب الموثوقية
        // =========================

        $request->merge([
            'sources_diverse' => $request->sources_diverse ? 1 : 0
        ]);

        $confidence = $this->calculateConfidence(
            $request,
            $imagePath ? true : false
        );

        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'category' => $request->category,
            'status' => 'verified',
            'image' => $imagePath,
            'governorate' => $request->governorate,
            'city' => $request->city,
            'event_date' => $request->event_date,
            'sources_count' => $request->sources_count ?? 0,
            'sources_diverse' => $request->sources_diverse ?? 0,
            'video_url' => $request->video_url,
            'confidence_score' => $confidence['score'],
            'confidence_level' => $confidence['level']
        ]);
        return redirect(
            url(app()->getLocale() . '/map?lat=' . $request->lat . '&lng=' . $request->lng)
        )->with('success', 'تمت إضافة الحدث بنجاح');
    }

    public function destroy($locale, $id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return redirect(
            url(app()->getLocale() . '/map')
        )->with('success', 'تم الحذف بنجاح');
    }

    public function edit($locale, $id)
    {
        $event = Event::findOrFail($id);
        return view('add-event', compact('event'));
    }

    public function update($locale, Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'category' => 'required|string',
            'event_date' => 'required|date',
            'governorate' => 'required|string',
            'city' => 'required|string',
            'sources_count' => 'nullable|integer|min:0|max:10',
            'sources_diverse' => 'nullable|boolean',
            'video_url' => 'nullable|url'
        ]);

        $event = Event::findOrFail($id);
        // =========================
        // 🔥 حساب الموثوقية
        // =========================

        $request->merge([
            'sources_diverse' => $request->sources_diverse ? 1 : 0
        ]);

        $confidence = $this->calculateConfidence(
            $request,
            $request->hasFile('image') || $event->image
        );

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'event_date' => $request->event_date,
            'governorate' => $request->governorate,
            'city' => $request->city,
            'confidence_score' => $confidence['score'],
            'confidence_level' => $confidence['level'],
            'sources_count' => $request->sources_count ?? 0,
            'sources_diverse' => $request->sources_diverse ?? 0,
            'video_url' => $request->video_url,
        ];
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }
        $event->update($data);
        return redirect(
            url(app()->getLocale() . '/map?lat=' . $event->lat . '&lng=' . $event->lng)
        )->with('success', 'تم التحديث بنجاح');
    }

    public function stats(Request $request)
    {
        $query = Event::where('status', 'verified');

        // =========================
        // 🎯 فلتر المحافظة
        // =========================
        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }

        // =========================
        // 🎯 فلتر النوع
        // =========================
        if ($request->filled('category')) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }

        // =========================
        // 🎯 فلتر الزمن (مرن)
        // =========================
        if ($request->filled('days')) {
            $query->where('event_date', '>=', now()->subDays($request->days));
        } else if ($request->filled('range')) {

            if ($request->range === 'today') {
                $query->whereBetween('event_date', [
                    now()->startOfDay(),
                    now()->endOfDay()
                ]);
            }

            if ($request->range === 'week') {
                $query->where('event_date', '>=', now()->subDays(7));
            }

            if ($request->range === 'month') {
                $query->where('event_date', '>=', now()->subDays(30));
            }
        }

        // =========================
        // 📊 إجمالي
        // =========================
        $total = (clone $query)->count();

        // =========================
        // 🔥 مقارنة زمنية (جديد)
        // =========================
        $previousPeriod = null;

        if ($request->filled('days')) {

            $previousPeriod = (clone $query)
                ->whereBetween('event_date', [
                    now()->subDays($request->days * 2),
                    now()->subDays($request->days)
                ])
                ->count();
        }

        // =========================
        // 📊 حسب النوع
        // =========================
        $byCategory = (clone $query)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');

        // =========================
        // 📈 Timeline
        // =========================
        $timeline = (clone $query)
            ->selectRaw('DATE(event_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // =========================
        // 🔥 المحافظات
        // =========================
        $topGovernorates = (clone $query)
            ->selectRaw('governorate, COUNT(*) as count')
            ->whereNotNull('governorate')
            ->groupBy('governorate')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $topGovernorate = (clone $query)
            ->selectRaw('governorate, COUNT(*) as count')
            ->whereNotNull('governorate')
            ->groupBy('governorate')
            ->orderByDesc('count')
            ->first();
        
        $byCategoryPercent = (clone $query)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->map(function ($item) use ($total) {
                return [
                    'category' => $item->category,
                    'count' => $item->count,
                    'percent' => $total > 0 ? round(($item->count / $total) * 100, 1) : 0
                ];
            });

        return response()->json([
            'total' => $total,
            'previous' => $previousPeriod,
            'by_category' => $byCategory,
            'timeline' => $timeline,
            'top_governorates' => $topGovernorates,
            'top_governorate' => $topGovernorate,
            'by_category_percent' => $byCategoryPercent
        ]);
    }

    private function calculateConfidence($request, $hasImage = false)
    {
        $score = 0;

        // عدد المصادر
        $score += min($request->sources_count ?? 0, 3);

        // تنوع المصادر
        if ($request->sources_diverse) {
            $score += 2;
        }

        // صورة
        if ($hasImage) {
            $score += 2;
        }

        // فيديو
        if ($request->video_url) {
            $score += 3;
        }

        // تحديد المستوى
        if ($score <= 3) {
            $level = 'low';
        } elseif ($score <= 6) {
            $level = 'medium';
        } else {
            $level = 'high';
        }

        return [
            'score' => $score,
            'level' => $level
        ];
    }

    public function summary(Request $request)
    {
        $query = Event::where('status', 'verified');

        // =========================
        // 🎯 فلتر المحافظة
        // =========================
        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }

        // =========================
        // 🎯 فلتر الزمن
        // =========================
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('event_date', [
                $request->from,
                $request->to
            ]);
        } elseif ($request->filled('range')) {

            if ($request->range === 'today') {
                $query->whereBetween('event_date', [
                    now()->startOfDay(),
                    now()->endOfDay()
                ]);
            }

            if ($request->range === 'week') {
                $query->where('event_date', '>=', now()->subDays(7));
            }

            if ($request->range === 'month') {
                $query->where('event_date', '>=', now()->subDays(30));
            }
        }

        $data = $query
            ->selectRaw('
                ROUND(lat, 1) as lat,
                ROUND(lng, 1) as lng,
                COUNT(*) as count
            ')
            ->groupByRaw('ROUND(lat,1), ROUND(lng,1)')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    // ========================================
    // ===== OG IMAGE (Dynamic)
    // ========================================
    public function ogImage($locale, $id)
    {
        $event = \DB::selectOne("
            SELECT title, image FROM h96737_news.events WHERE id = ?
        ", [$id]);

        if (!$event) {
            abort(404);
        }

        // 🔥 إصلاح العربي
        $title = $event->title ?? 'حدث غير معروف';

        $arabic = new Arabic();
        $title = $arabic->utf8Glyphs($title);

        // 🔥 ناخد أول كلمتين فقط
        $words = explode(' ', $event->title);

        $line1 = implode(' ', array_slice($words, 0, 2));
        $line1 = $arabic->utf8Glyphs($line1);

        $imagePath = $event->image
            ? public_path('storage/' . $event->image)
            : public_path('images/og-default.jpg');

        // إنشاء الصورة
        $image = new Imagick($imagePath);
        $image->resizeImage(1200, 630, Imagick::FILTER_LANCZOS, 1, true);
        $image->cropImage(1200, 630, 0, 0);

        // تغميق
        $overlay = new Imagick();
        $overlay->newImage(1200, 630, new ImagickPixel('rgba(0,0,0,0.5)'));
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, 0, 0);

        // الخط
        $draw = new ImagickDraw();
        $draw->setFont(storage_path('app/fonts/Tajawal-Bold.ttf'));
        $draw->setFillColor('white');
        $draw->setTextAlignment(Imagick::ALIGN_CENTER);
        $draw->setFontSize(120);


        // ظل
        $shadow = new ImagickDraw();
        $shadow->setFont(storage_path('app/fonts/Tajawal-Bold.ttf'));
        $shadow->setFillColor('rgba(0,0,0,1)');
        $shadow->setStrokeWidth(0);
        $shadow->setTextAlignment(Imagick::ALIGN_CENTER);
        $shadow->setFontSize(120);

        $overlay2 = new Imagick();
        $overlay2->newImage(1200, 400, new ImagickPixel('rgba(0,0,0,0.9)'));
        $image->compositeImage($overlay2, Imagick::COMPOSITE_OVER, 0, 300);
        

        $image->annotateImage($shadow, 520, 430, 0, $line1);
        $image->annotateImage($draw, 518, 428, 0, $line1);

        // Badge
        $badge = new ImagickDraw();
        $badge->setFillColor('#ef4444');
        $badge->roundRectangle(200, 80, 460, 160, 25, 25);
        $image->drawImage($badge);

        $badgeText = new ImagickDraw();
        $badgeText->setFont(storage_path('app/fonts/Tajawal-Bold.ttf'));
        $badgeText->setFillColor('white');
        $badgeText->setFontSize(40);
        $badgeText->setTextAlignment(Imagick::ALIGN_CENTER);

        $badgeTextFixed = $arabic->utf8Glyphs('عاجل');
        $image->annotateImage($badgeText, 300, 120, 0, $badgeTextFixed);

        // الموقع
        $footer = new ImagickDraw();
        $footer->setFont(storage_path('app/fonts/Tajawal-Bold.ttf'));
        $footer->setFillColor('#cbd5f5');
        $footer->setFontSize(85);
        $footer->setTextAlignment(Imagick::ALIGN_CENTER);

        $image->annotateImage($footer, 520, 580, 0, 'thealawites.com');

        $image->setImageFormat('png');

        return response($image)->header('Content-Type', 'image/png');
    }
}