<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\CancelledAppointment;
use App\Http\Requests\StoreAppointment;
use App\Interfaces\ScheduleServiceInterface;
use App\Specialty;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Validator;

class AppointmentController extends Controller
{
    public function index()
    {
        $role = \auth()->user()->role;
        if ($role == 'admin') {
            $pendingAppointments = Appointment::where('status', 'Reservada')->paginate(10);
            $confirmedAppointments = Appointment::where('status', 'Confirmada')->paginate(10);
            $oldAppointments = Appointment::whereIn('status', ['Atendida', 'Cancelada'])->paginate(10);
        } else if ($role == 'doctor') {
            $pendingAppointments = Appointment::where('status', 'Reservada')->where('doctor_id', \auth()->id())->paginate(10);
            $confirmedAppointments = Appointment::where('status', 'Confirmada')->where('doctor_id', \auth()->id())->paginate(10);
            $oldAppointments = Appointment::whereIn('status', ['Atendida', 'Cancelada'])->where('doctor_id', \auth()->id())->paginate(10);
        } else if ($role = 'patient') {
            $pendingAppointments = Appointment::where('status', 'Reservada')->where('patient_id', \auth()->id())->paginate(10);
            $confirmedAppointments = Appointment::where('status', 'Confirmada')->where('patient_id', \auth()->id())->paginate(10);
            $oldAppointments = Appointment::whereIn('status', ['Atendida', 'Cancelada'])->where('patient_id', \auth()->id())->paginate(10);
        }
        //patien
        //doctor
        //admin => all
        // $appointments = Appointment::paginate(10);


        return view('appointments.index', \compact('pendingAppointments', 'confirmedAppointments', 'oldAppointments', 'role'));
    }

    public function show(Appointment $appointment)
    {
        $role = \auth()->user()->role;
        return \view('appointments.show', \compact('appointment', 'role'));
    }

    public function create(ScheduleServiceInterface $scheduleService)
    {
        $specialties = Specialty::all();
        $specialtyId = old('specialty_id');
        if ($specialtyId) {
            $specialty = Specialty::find($specialtyId);
            $doctors = $specialty->users;
        } else {
            $doctors = \collect();
        }

        $date = old('scheduled_date');
        $doctorId =  old('doctor_id');

        if ($date && $doctorId) {
            $intervals =  $scheduleService->getAvalibleIntervals($date, $doctorId);
        } else {
            $intervals = null;
        }

        return view('appointments.create', \compact('specialties', 'doctors', 'intervals'));
    }

    public function store(StoreAppointment $request)
    {
        $created = Appointment::createForPatient($request, auth()->id());

        if ($created) {
            $notification = 'La cita se ha registrado correctamente';
        } else {
            $notification = 'Ocurrio un problema al registrar la cita medica';
        }

        return back()->with(\compact('notification'));
    }

    public function showCancelForm(Appointment $appointment)
    {
        $role = auth()->user()->role;
        if ($appointment->status == 'Confirmada') {
            return view('appointments.cancel', \compact('appointment', 'role'));
        }

        return \redirect('appointments');
    }

    public function postCancel(Appointment $appointment, Request $request)
    {
        if ($request->has('justification')) {
            $cancellation = new CancelledAppointment();
            $cancellation->justification = $request->input('justification');
            $cancellation->cancelled_by = \auth()->id();
            $appointment->cancellation()->save($cancellation);
        }

        $appointment->status = 'Cancelada';
        $saved = $appointment->save();

        if ($saved)
            $appointment->patient->sendFCM("Su cita ha sido cancelada");

        $notification = 'La cita se ha cancelado correctamente';

        return \redirect('/appointments')->with(\compact('notification'));
    }

    public function postCanfirm(Appointment $appointment)
    {
        $appointment->status = 'Confirmada';
        $saved = $appointment->save();

        if ($saved)
            $appointment->patient->sendFCM("Su cita se ha confirmado");


        $notification = 'La cita se ha confirmado correctamente';

        return \redirect('/appointments')->with(\compact('notification'));
    }
}
