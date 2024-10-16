<?php

namespace Modules\Dashboard\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Event\Entities\Banner;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventPayment;
use Modules\LearningManagement\Services\Admin\DashboardService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $lmsDashboardService = new DashboardService();
        $date = Carbon::now();
        $year = $date->format('Y');
        $month = $date->format('m');
        $data = [
            'events' => [
                'all' => Event::count(),
                'available' => Event::where('is_visible', true)
                    ->count(),
                'ongoing' => Event::where('status', 'Sedang Berlangsung')
                    ->count(),
                'done' => Event::where('status', 'Selesai')
                    ->count(),
                'populars' => Event::withCount('participants')
                    ->orderBy('participants_count', 'desc')
                    ->take(10)
                    ->get(),
            ],
            'payments' => [
                'all' => EventPayment::count(),
                'pending' => EventPayment::whereIn('status', ['Belum Dibayar', 'Menunggu Pembayaran'])
                    ->count(),
                'failed' => EventPayment::where('status', 'Gagal')
                    ->count(),
                'success' => EventPayment::where('status', 'Sukses')
                    ->count(),
                'earn_all' => EventPayment::where('status', 'Sukses')
                    ->sum('price'),
                'earn_year' => EventPayment::where('status', 'Sukses')
                    ->whereYear('created_at', $year)
                    ->sum('price'),
                'earn_month' => EventPayment::where('status', 'Sukses')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->sum('price'),
                'earn_today' => EventPayment::where('status', 'Sukses')
                    ->whereDate('created_at', $date)
                    ->sum('price'),
            ],
            'users' => [
                'by_level' => User::groupBy('level')
                    ->pluck(User::raw('count(*) as count'), 'level')
                    ->map(function ($count, $level) {
                        return ['level' => $level, 'count' => $count];
                    })->values(),
                'by_status' => User::groupBy('status')
                    ->pluck(User::raw('count(*) as count'), 'status')
                    ->map(function ($count, $status) {
                        return ['status' => $status, 'count' => $count];
                    })->values(),
                'most_active_users' => User::with('profile')
                    ->withCount('eventParticipants')
                    ->orderBy('event_participants_count', 'desc')
                    ->take(10)
                    ->get()
            ],
            'banners' => Banner::orderBy('sequence', 'asc')->get(),
            'tasks' => $lmsDashboardService->getTaskDashboard($request->user()->id),
            'questions' => $lmsDashboardService->getQuestionDashboard($request->user()->id),
        ];
        return $this->ok($data, 'Data dashboard');
    }
}
