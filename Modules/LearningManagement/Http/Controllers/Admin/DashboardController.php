<?php

namespace Modules\LearningManagement\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Base\DashboardController as BaseDashboardController;
//use Modules\LearningManagement\Services\Admin\DashboardService;

class DashboardController extends BaseDashboardController
{
    public function __construct()
    {
        //$this->service = new DashboardService();
    }
    public function index(Request $request)
    {
        //return $this->ok($this->service->index($request), 'Data dashboard lms');
    }
}
