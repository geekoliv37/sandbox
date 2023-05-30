<?php

namespace App\Service;

use DateTime;
use Exception;

class ConditionReglementService
{
    const FM15 = 15;
    const FM30 = 30;
    const FM45 = 45;

    public function getConst($condition): int|string
    {
        public function addDays($date, $condition)
    {
        return $date->add(new \DateInterval('P'.$condition.'D'));
    }
        switch ($condition)
        {
            case 'FM15' :
                return ConditionsReglement::FM15;
            case 'FM30':
                return ConditionsReglement::FM30;
            case 'FM45' :
                return ConditionsReglement::FM45;
            default:
                return 'Aucune condition valable';
        }
    }
}