@extends('voyager::master')

@section('page_title', 'تعديل الخبر')

@section('content')
<div class="page-content container-fluid">

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <h3 class="panel-title">✏️ تعديل الخبر</h3>
                </div>

                <div class="panel-body">

                    <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- العنوان --}}
                        <div class="form-group">
                            <label>العنوان (عربي)</label>
                            <input type="text" name="title" class="form-control"
                                   value="{{ old('title', $news->title) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Title (English)</label>
                            <input type="text" name="title_en" class="form-control"
                                   value="{{ old('title_en', $news->title_en) }}">
                        </div>

                        {{-- التاريخ --}}
                        <div class="form-group">
                            <label>التاريخ</label>
                            <input type="date" name="date" class="form-control"
                                   value="{{ old('date', $news->date) }}" required>
                        </div>

                        {{-- الملخص --}}
                        <div class="form-group">
                            <label>الملخص (عربي)</label>
                            <textarea name="summary" class="form-control" rows="3" required>{{ old('summary', $news->summary) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Summary (English)</label>
                            <textarea name="summary_en" class="form-control" rows="3">{{ old('summary_en', $news->summary_en) }}</textarea>
                        </div>

                        {{-- المحتوى --}}
                        <div class="form-group">
                            <label>نص الخبر الكامل (عربي)</label>
                            <textarea name="content" class="form-control" rows="6" required>{{ old('content', $news->content) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Full Content (English)</label>
                            <textarea name="content_en" class="form-control" rows="6">{{ old('content_en', $news->content_en) }}</textarea>
                        </div>

                        {{-- الصورة الحالية --}}
                        @if($news->image)
                            <div class="form-group">
                                <label>الصورة الحالية</label><br>
                                <img src="{{ asset('storage/'.$news->image) }}" style="max-width:200px;">
                            </div>
                        @endif

                        {{-- تغيير الصورة --}}
                        <div class="form-group">
                            <label>تغيير صورة الخبر</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        {{-- حالة النشر --}}
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="published" value="1"
                                    {{ $news->published ? 'checked' : '' }}>
                                الخبر منشور
                            </label>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                💾 حفظ التعديلات
                            </button>

                            <a href="{{ route('admin.news.index') }}" class="btn btn-default">
                                ↩️ رجوع
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection
