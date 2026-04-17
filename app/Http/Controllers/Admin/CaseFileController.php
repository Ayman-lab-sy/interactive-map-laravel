<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseFile;
use Illuminate\Support\Facades\Storage;

class CaseFileController extends Controller
{
    public function view($id)
    {
        $file = CaseFile::findOrFail($id);

        abort_unless(auth()->user()->can('viewCaseFile', $file), 403);

        return response()->file(
            storage_path('app/' . $file->file_path),
            ['Content-Type' => $file->mime_type]
        );
    }

    public function download($id)
    {
        $file = CaseFile::findOrFail($id);

        return response()->download(
            storage_path('app/' . $file->file_path),
            $file->original_name
        );
    }
}
