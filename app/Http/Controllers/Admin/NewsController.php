<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class NewsController extends Controller
{
    // عرض قائمة الأخبار
    public function index(Request $request)
    {
        $news = News::orderBy('date', 'desc')->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    // صفحة إضافة خبر
    public function create()
    {
        return view('admin.news.create');
    }

    // حفظ خبر جديد
    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'title'        => 'required|string|max:255',
                'title_en'     => 'nullable|string|max:255',
                'date'         => 'required|date',
                'summary'      => 'required|string',
                'summary_en'   => 'nullable|string',
                'content'      => 'required|string',
                'content_en'   => 'nullable|string',
                'image'        => 'nullable|image|max:5120',
                'published'    => 'nullable|boolean',
            ], [
                'title.required'   => 'العنوان مفقود',
                'summary.required' => 'الملخص مفقود',
                'content.required' => 'نص الخبر مفقود',
                'date.required'    => 'التاريخ مفقود',
                'image.image'      => 'الملف المرفوع ليس صورة',
                'image.max'        => 'حجم الصورة أكبر من المسموح',
            ]);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('news', 'public');
            }

            $data['slug'] = Str::slug($data['title']) . '-' . time();
            $data['published'] = $request->boolean('published', true);

            News::create($data);

            return redirect()
                ->route('admin.news.index')
                ->with('success', 'تمت إضافة الخبر بنجاح');

        } catch (ValidationException $e) {

            // Validation فشل → Laravel يعرض الرسائل تلقائيًا
            throw $e;

        } catch (Exception $e) {

            // أي فشل آخر (DB / Connection / Constraint)
            Log::error('News store failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'فشل حفظ الخبر: ' . $e->getMessage());
        }
    }

    // صفحة تعديل خبر
    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    // تحديث خبر
    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'title_en'     => 'nullable|string|max:255',
            'date'         => 'required|date',
            'summary'      => 'required|string',
            'summary_en'   => 'nullable|string',
            'content'      => 'required|string',
            'content_en'   => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'published'    => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $data['published'] = $request->boolean('published', $news->published);

        $news->update($data);

        return redirect()->route('admin.news.index')
            ->with('success', 'تم تحديث الخبر بنجاح');
    }

    // حذف خبر
    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'تم حذف الخبر');
    }
}
