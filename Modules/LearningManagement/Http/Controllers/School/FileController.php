<?php

namespace Modules\LearningManagement\Http\Controllers\School;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Modules\LearningManagement\Http\Controllers\Controller;
use Modules\LearningManagement\Http\Requests\School\CreateFileRequest;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|Lihat Soal'])->only(['index', 'show']);
        $this->middleware(['role_or_permission:Super Admin|Tambah Soal'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Soal'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Soal'])->only('destroy');
    }

    public function store(CreateFileRequest $request)
    {
        $file = $request->file('file');
        $storedFile = $file->storeAs(
            'schools/' . $request->route('schoolId') . '/lms/uploads/' . $request->user()->id,
            Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-') . '-' . time() . '.' . $file->getClientOriginalExtension()
        );

        $url = Storage::url($storedFile);
        return $this->ok(["location" => $url], 'Berhasil menyimpan data');
    }
}

