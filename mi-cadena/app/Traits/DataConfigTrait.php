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

    public function chains_status()
    {

        return  [
            "A" => "Activa",
            "C" => "Completada",
            "N" => "Cancelada",
        ];
    }

    public function frecuency_text()
    {
        return [
            7=>'Semanal',
            15=>'Quincenal',
            30=>'Mensual'
        ];
    }
}
