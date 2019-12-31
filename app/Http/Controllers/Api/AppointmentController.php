<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointment;

class AppointmentController extends Controller
{

    public function index()
    {
        $user = Auth::guard('api')->user();
        $appoitments = $user->asPatientAppointments()
            ->with([
                'specialty' => function ($query) {
                    $query->select('id', 'name');
                },
                'doctor' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->get([
                "id",
                "description",
                "specialty_id",
                "doctor_id",
                "scheduled_date",
                "type",
                "scheduled_time",
                "created_at",
                "status"
            ]);

        return $appoitments;
    }

    public function store(StoreAppointment $request)
    {
        $patientId = Auth::guard('api')->id();
        $appointment = Appointment::createForPatient($request, $patientId);
        if ($appointment) {
            $success = true;
        } else {
            $success = false;
        }

        return compact('success');
    }
}
