<?php

namespace Modules\LearningManagement\Http\Controllers\School;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Base\QuestionController as BaseQuestionController;
use Modules\LearningManagement\Http\Requests\School\CreateQuestionBulkRequest;
use Modules\LearningManagement\Http\Requests\School\CreateQuestionOptionRequest;
use Modules\LearningManagement\Services\School\QuestionService;
use Modules\LearningManagement\Http\Requests\School\CreateQuestionRequest;
use Modules\LearningManagement\Http\Requests\School\UpdateQuestionRequest;

class QuestionController extends BaseQuestionController
{
    public function __construct()
    {
        $this->service = new QuestionService;
        $this->middleware(['role_or_permission:Super Admin|Lihat Bank Soal'])->only(['index', 'show']);
        $this->middleware(['role_or_permission:Super Admin|Tambah Bank Soal'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Bank Soal'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Bank Soal'])->only('destroy');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateQuestionRequest $request)
    {
        if ($data = $this->service->create($request, $request->user()->id))
            return $this->ok($data, 'Berhasil menyimpan data');
        return $this->error('Gagal menyimpan data');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBulk(CreateQuestionBulkRequest $request)
    {
        if ($this->service->createBulk($request, $request->user()->id))
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
    public function update(UpdateQuestionRequest $request)
    {
        if ($data = $this->service->update($request->route('question'), $request, $request->route('schoolId'), $request->user()->id))
            return $this->ok($data, 'Berhasil mengubah data');
        return $this->error('Anda tidak diizinkan mengubah data ini', 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($this->service->delete(
            $request->route('question'),
            $request->user()->id
        ))
            return $this->ok([], 'Berhasil menghapus data');
        return $this->error('Anda tidak diizinkan mengubah data ini', 403);
    }
}
