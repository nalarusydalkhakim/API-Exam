<?php

namespace Modules\LearningManagement\Http\Controllers\School;

use Illuminate\Support\Facades\Request;
use Modules\LearningManagement\Http\Controllers\Base\QuestionOptionController as BaseQuestionOptionController;
use Modules\LearningManagement\Http\Requests\School\CreateQuestionOptionBulkRequest;
use Modules\LearningManagement\Http\Requests\School\CreateQuestionOptionRequest;
use Modules\LearningManagement\Http\Requests\School\UpdateQuestionOptionRequest;
use Modules\LearningManagement\Services\School\QuestionOptionService;

class QuestionOptionController extends BaseQuestionOptionController
{
    public function __construct()
    {
        $this->service = new QuestionOptionService;

        $this->middleware(['role_or_permission:Super Admin|Lihat Soal'])->only(['index', 'show']);
        $this->middleware(['role_or_permission:Super Admin|Tambah Soal'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Soal'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Soal'])->only('destroy');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateQuestionOptionBulkRequest $request)
    {
        if ($this->service->create(
            $request->route('question'),
            $request,
            $request->route('schoolId'),
            $request->user()->id
        ))
            return $this->created('Berhasil menyimpan data');
        return $this->error('Gagal menyimpan data');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuestionOptionRequest $request)
    {
        $teacherId = null;
        if (!$request->user()->hasRole('Admin Sekolah')) {
            $teacherId = $request->user()->id;
        }
        if ($data = $this->service->update(
            $request->route('option'),
            $request,
            $request->route('question'),
            $request->route('schoolId'),
            $teacherId
        ))
            return $this->ok($data, 'Berhasil mengubah data');
        return $this->error('Data tidak ditemukan', 404);
    }
}
