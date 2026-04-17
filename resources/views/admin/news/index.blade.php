@extends('voyager::master')

@section('page_title', 'إدارة الأخبار')

@section('content')
<div class="page-content container-fluid">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <h3 class="panel-title">قائمة الأخبار</h3>
                    <div class="panel-actions">
                        <a href="{{ route('admin.news.create') }}" class="btn btn-success">
                            ➕ إضافة خبر جديد
                        </a>
                    </div>
                </div>

                <div class="panel-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th width="180">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($news as $item)
                                <tr>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->date }}</td>
                                    <td>
                                        @if($item->published)
                                            <span class="label label-success">منشور</span>
                                        @else
                                            <span class="label label-default">غير منشور</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-sm btn-primary">
                                            ✏️ تعديل
                                        </a>

                                        <form action="{{ route('admin.news.destroy', $item->id) }}"
                                              method="POST"
                                              style="display:inline-block"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الخبر؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                🗑 حذف
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        لا يوجد أخبار بعد
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="text-center">
                        {{ $news->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection
