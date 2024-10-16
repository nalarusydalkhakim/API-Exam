<?php

namespace Modules\LearningManagement\Http\Controllers\Base;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected $service;
    public function index(Request $request)
    {
        $schoolId = null;
        return $this->ok($this->service->index($schoolId), 'Data dashboard');
    }
}
