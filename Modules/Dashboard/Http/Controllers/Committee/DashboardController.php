<?php

namespace Modules\Dashboard\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Event\Entities\Banner;
use Modules\Event\Entities\Event;
use Modules\LearningManagement\Services\School\DashboardService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $lmsDashboardService = new DashboardService();
        $data = [
            'events' => [
                'avalable' => Event::where('is_visible', true)
                    ->count(),
                'owned' => Event::whereHas('admins', function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                })
                    ->count()
            ],
            'banners' => Banner::orderBy('sequence', 'asc')->get(),
            'tasks' => $lmsDashboardService->getTaskDashboardByUserId($request->user()->id),
            'questions' => $lmsDashboardService->getQuestionDashboardByUserId($request->user()->id),
        ];
        return $this->ok($data, 'Data dashboard');
    }
}
