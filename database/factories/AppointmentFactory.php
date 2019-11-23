<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Appointment;
use App\Model;
use App\User;
use Faker\Generator as Faker;

$factory->define(Appointment::class, function (Faker $faker) {
    $doctorIds = User::doctors()->pluck('id');
    $patientIds =  User::patients()->pluck('id');

    $scheduled = $faker->dateTimeBetween('-1 years', 'now');
    $scheduled_date = $scheduled->format('Y-m-d');
    $scheduled_time = $scheduled->format('H:i:s');
    $type = ['Consulta', 'Examen', 'Operación'];
    $statuses = ['Atendida', 'Cancelada']; //'Reservada','Confirmada',

    return [
        'description' => $faker->sentence(6),
        'specialty_id' => $faker->numberBetween(1, 3),
        'doctor_id' => $faker->randomElement($doctorIds),
        'patient_id' => $faker->randomElement($patientIds),
        'scheduled_date' => $scheduled_date,
        'scheduled_time' => $scheduled_time,
        'type' => $faker->randomElement($type),
        'status' => $faker->randomElement($statuses),
    ];
});
