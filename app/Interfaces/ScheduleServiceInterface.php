<?php

namespace App\Interfaces;

use Carbon\Carbon;

interface ScheduleServiceInterface
{
    public function getAvalibleIntervals($date, $doctorId);

    public function isAvalibleInterval($date,$doctorId,Carbon $start);
}
