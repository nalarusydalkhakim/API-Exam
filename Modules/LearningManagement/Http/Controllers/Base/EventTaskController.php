<?php

namespace Modules\LearningManagement\Http\Controllers\Base;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Controller;

class EventTaskController extends Controller
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
            $this->service->getAll(
                $request
            ),
            'Data Ujian'
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
            $request->route('event_task'),
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
            $request->route('event_task')
        ))
            return $this->ok([], 'Berhasil menghapus data');
        return $this->error('Data tidak ditemukan', 404);
    }
}
