@extends('voyager::master')

@section('page_title', 'إضافة خبر جديد')

@section('content')
<div class="page-content container-fluid">

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <h3 class="panel-title">➕ إضافة خبر جديد</h3>
                </div>

                <div class="panel-body">
                    {{-- رسائل الأخطاء --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>حدث خطأ أثناء حفظ الخبر:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- رسالة فشل عام --}}
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- رسالة نجاح --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- العنوان --}}
                        <div class="form-group">
                            <label>العنوان (عربي)</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Title (English)</label>
                            <input type="text" name="title_en" class="form-control">
                        </div>

                        {{-- التاريخ --}}
                        <div class="form-group">
                            <label>التاريخ</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        {{-- الملخص --}}
                        <div class="form-group">
                            <label>الملخص (عربي)</label>
                            <textarea name="summary" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Summary (English)</label>
                            <textarea name="summary_en" class="form-control" rows="3"></textarea>
                        </div>

                        {{-- المحتوى --}}
                        <div class="form-group">
                            <label>نص الخبر الكامل (عربي)</label>
                            <textarea name="content" class="form-control" rows="6" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Full Content (English)</label>
                            <textarea name="content_en" class="form-control" rows="6"></textarea>
                        </div>

                        {{-- الصورة --}}
                        <div class="form-group">
                            <label>صورة الخبر</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        {{-- حالة النشر --}}
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="published" value="1" checked>
                                نشر الخبر مباشرة
                            </label>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                💾 حفظ الخبر
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
