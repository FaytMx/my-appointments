<?php

namespace App\Http\Controllers;

use App\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $appointments = Appointment::select([
        //     'id',
        //     DB::raw('DAYOFWEEK(scheduled_date)')
        //  ])->where('status', 'Confirmada')->get();

        $minutes = 60;
        $appointmentsByDay = Cache::remember('appointments_by_day', $minutes, function () {
            $results = Appointment::select([
                DB::raw('DAYOFWEEK(scheduled_date) as day'),
                DB::raw('COUNT(*) as count')
            ])->groupBy(DB::raw('DAYOFWEEK(scheduled_date)'))->get('day', 'count')
                ->mapWithKeys(function ($item) {
                    return [$item['day'] => $item['count']];
                })->toArray();

            $appointmentsByDay = [];

            for ($i = 1; $i <= 7; $i++) {
                if (array_key_exists($i, $results)) {
                    $appointmentsByDay[] = $results[$i];
                } else {
                    $appointmentsByDay[] = 0;
                }
            }

            return $appointmentsByDay;
        });


        // dd($appointmentsByDay);
        return view('home', compact('appointmentsByDay'));
    }
}
