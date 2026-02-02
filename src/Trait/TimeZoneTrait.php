<?php

namespace App\Trait;

trait TimeZoneTrait
{   
    /**
     * Permet de changer le fuseau horaire de l'application
     *
     * @param string $timeZoneId
     * @return void
     */
    protected function changeTimeZone(string $timeZoneId): void
    {
        date_default_timezone_set($timeZoneId);
    }
}