<?php

namespace Modules\LearningManagement\Http\Controllers\Base;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Controller;

class TaskQuestionController extends Controller
{
    protected $service;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->ok(
            $this->service->getGroupBySection(
                $request->route('task')
            ),
            'Data Soal'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = $this->service->findById(
            $request->route('task_question'),
            $request->route('task')
        );
        if ($data)
            return $this->ok($data, 'Data detail Ujian');
        return $this->error('Data tidak ditemukan', 404);
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
            $request->route('task'),
            $request->user()->id
        ))
            return $this->ok([], 'Berhasil menghapus data');
        return $this->error('Data tidak ditemukan', 404);
    }
}
