<?php

namespace Modules\LearningManagement\Http\Controllers\Student;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Base\DashboardController as BaseDashboardController;
use Modules\LearningManagement\Services\Student\DashboardService;

class DashboardController extends BaseDashboardController
{
    public function __construct()
    {
        $this->service = new DashboardService();
    }
    public function getStudentDashboardById(Request $request)
    {
        $schoolId = $request->route('schoolId');
        return $this->ok($this->service->getStudentDashboardById($request, $schoolId), 'Data dashboard siswa');
    }
}
