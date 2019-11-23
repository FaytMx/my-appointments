<?php

namespace App\Http\Controllers\Admin;

use App\Appointment;
use App\User;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ChartController extends Controller
{
    public function appointments()
    {

        $monthlyCounts = Appointment::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(1) as count'))->groupBy('month')->get()->toArray();

        $counts = array_fill(0, 12, 0);

        foreach ($monthlyCounts as $monthlyCount) {
            $index = $monthlyCount['month'] - 1;
            $counts[$index] = $monthlyCount['count'];
        }

        return view('charts.appointments', \compact('counts'));
    }

    public function doctors()
    {
        $now = Carbon::now();
        $end = $now->format('Y-m-d');
        $start = $now->subYear()->format('Y-m-d');

        return view('charts.doctors', \compact('start', 'end'));
    }

    public function doctorsJson(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $doctors = User::doctors()->select('id', 'name')
            ->withCount(
                [
                    'cancelledAppointments' => (function ($query) use ($start, $end) {
                        $query->whereBetween('scheduled_date', [$start, $end]);
                    }),
                    'attendedAppointments' => (function ($query) use ($start, $end) {
                        $query->whereBetween('scheduled_date', [$start, $end]);
                    })
                ]
            )
            ->orderBy('attended_appointments_count', 'desc')
            ->take(3)
            ->get();


        $data = [];
        $data['categories'] =  $doctors->pluck('name');
        $series = [];
        $series1['name'] = 'Citas atendidas';
        $series1['data'] = $doctors->pluck('attended_appointments_count');
        $series2['name'] = 'Citas canceladas';
        $series2['data'] = $doctors->pluck('cancelled_appointments_count');
        $series[] = $series1;
        $series[] = $series2;
        $data['series'] = $series;
        return $data;
    }
}
