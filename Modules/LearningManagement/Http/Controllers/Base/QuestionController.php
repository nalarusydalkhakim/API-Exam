<?php

namespace Modules\LearningManagement\Http\Controllers\Base;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Controller;

class QuestionController extends Controller
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
            $this->service->getPaginate(
                $request,
                $request->user()->id
            ),
            'Data Bank Soal'
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
            $request->route('question'),
            $request->user()->id
        );
        if ($data)
            return $this->ok($data, 'Data detail soal');
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
            $request->route('question'),
            $request->user()->id
        ))
            return $this->ok([], 'Berhasil menghapus data');
        return $this->error('Data tidak ditemukan', 404);
    }
}
