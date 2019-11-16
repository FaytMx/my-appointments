<?php

namespace App\Services;

use App\Appointment;
use App\Interfaces\ScheduleServiceInterface;
use App\WorkDay;
use Carbon\Carbon;

class ScheduleService implements ScheduleServiceInterface
{
    private function getDayFromDate($date)
    {
        $dateCarbon =  new Carbon($date);
        $i = $dateCarbon->dayOfWeek;
        $day = ($i == 0 ? 6 : $i - 1);
        return $day;
    }

    public function getAvalibleIntervals($date, $doctorId)
    {
        $workDays = WorkDay::where('active', true)->where('day', $this->getDayFromDate($date))->where('user_id', $doctorId)->first([
            'morning_start', 'morning_end', 'afternoon_start', 'afternoon_end'
        ]);

        if (!$workDays) {
            return [];
        }

        $morningIntervals = $this->getIntervals($workDays->morning_start, $workDays->morning_end, $date, $doctorId);
        $afternoonIntervals = $this->getIntervals($workDays->afternoon_start, $workDays->afternoon_end, $date, $doctorId);

        $data = [];
        $data['morning'] = $morningIntervals;
        $data['afternoon'] = $afternoonIntervals;

        return $data;
    }

    private function getIntervals($start, $end, $date, $doctorId)
    {
        $start = new Carbon($start);
        $end = new Carbon($end);

        $intervals = [];

        while ($start < $end) {
            $interval = [];
            $interval['start'] = $start->format('g:i A');
            $avalible = $this->isAvalibleInterval($date,$doctorId,$start);
            $start->addMinute(30);
            $interval['end'] = $start->format('g:i A');


            if ($avalible) {
                $intervals[] = $interval;
            }
        }

        return $intervals;
    }

    public function isAvalibleInterval($date, $doctorId, Carbon $start)
    {
        $exists = Appointment::where('doctor_id', $doctorId)->where('scheduled_date', $date)->where('scheduled_time', $start->format('H:i:s'))->exists();

        return !$exists;
    }
}
