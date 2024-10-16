<?php

namespace Modules\Dashboard\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Event\Entities\Banner;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventPayment;
use Modules\LearningManagement\Services\Student\DashboardService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $lmsDashboardService = new DashboardService();
        $data = [
            'events' => [
                'avalable' => Event::where('is_visible', true)
                    ->count(),
                'owned' => Event::whereHas('participants', function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                })
                    ->count()
            ],
            'payments' => [
                'pending' => EventPayment::where('user_id', $request->user()->id)
                    ->whereIn('status', ['Belum Dibayar', 'Menunggu Pembayaran'])
                    ->count(),
                'success' => EventPayment::where('user_id', $request->user()->id)
                    ->where('status', 'Sukses')
                    ->count()
            ],
            'banners' => Banner::orderBy('sequence', 'asc')->get(),
            'tasks' => $lmsDashboardService->getStudentDashboardById($request->user()->id),
        ];
        return $this->ok($data, 'Data dashboard');
    }
}
