<?php

namespace Modules\LearningManagement\Http\Controllers\School;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Base\DashboardController as BaseDashboardController;
use Modules\LearningManagement\Services\School\DashboardService;

class DashboardController extends BaseDashboardController
{
    public function __construct()
    {
        //$this->service = new DashboardService();
    }
    public function getSchoolDashboardById(Request $request)
    {
        $schoolId = $request->route('schoolId');
        //return $this->ok($this->service->getSchoolDashboardById($request, $schoolId), 'Data dashboard sekolah');
    }
}
