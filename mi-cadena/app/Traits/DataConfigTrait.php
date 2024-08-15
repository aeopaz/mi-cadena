<?php


namespace App\Traits;

trait DataConfigTrait
{

    public function participants_status()
    {

        return  [
            "A" => "Aceptada",
            "R" => "Rechazada",
            "P" => "Pendiente",
        ];
    }
}
