<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use App\Interfaces\ScheduleServiceInterface;

class StoreAppointment extends FormRequest
{
    private $scheduleService;

    public function __construct(ScheduleServiceInterface $scheduleService) {
        $this->scheduleService =  $scheduleService;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description' => 'required',
            'specialty_id' => 'exists:specialties,id',
            'doctor_id' => 'exists:users,id',
            'scheduled_time'  => 'required',
        ];
    }

    public function messages()
    {
        return ['scheduled_time.required' => 'Por favor seleccione una hora válida para su cita.'];
    }

    public function withValidator($validator )
    {
        $validator->after(function ($validator) {
            $date = $this->input('scheduled_date');
            $doctorId = $this->input('doctor_id');
            $scheduled_time = $this->input('scheduled_time');

            if (!$date || !$doctorId || !$scheduled_time) {

                return;
            }
            $start = new Carbon($scheduled_time);

            if (!$this->scheduleService->isAvalibleInterval($date, $doctorId, $start)) {
                $validator->errors()
                    ->add('avalible_time', 'La hora seleccionada ya se encuentra reservada');
            }
        });
    }
}
