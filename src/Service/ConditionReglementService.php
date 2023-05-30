<?php

namespace App\Service;

use DateTime;
use Exception;
use Symfony\Component\Validator\Constraints\Date;

class ConditionReglementService extends DateTime
{

    const CR15NET = array('count'=> 15 , 'name' => '15 Jours NET');
    const CR30NET = array('count'=> 30, 'name' => '30 Jours NET');
    const CR45NET = array('count'=> 45, 'name' => '45 Jours NET');
    const CR60NET = array('count'=> 60, 'name' => '60 Jours NET');
    const CR30FDM = array('count'=> 30, 'name' => '30 Jours Fin de mois');
    const CR45FDM = array('count'=> 45, 'name' => '45 Jours Fin de mois');
    const CR30FDM15 = array('count'=> 30, 'name' => '30 Jours Fin de mois le 15', 'dayFinishedMonth' => 15 );
    const FDM15 = array('count'=> 15, 'name' => 'Fin de mois 15 Jours');
    const FDM30 = array('count'=> 30, 'name' => 'Fin de mois 30 Jours');
    const FDM45 = array('count'=> 45, 'name' => 'Fin de mois 45 Jours');

        public function __construct(string $datetime = 'now' , DateTimeZone $timezone = null)
        {
            parent::__construct($datetime, $timezone);
        }


    /**
     * Envoie la date de fin depuis une date de début et une condtion
     * @param string $date
     * @param string $condition
     * @return DateTime
     * @throws Exception
     */

    public function getDateEnd(string $date, string $condition): DateTime
    {
        //  $this->
        $conditionReglement = $condition;   // pour conserver la variable $condition d'origine
        $condition = $this->getNbJourOfConst($condition);
        $date = new DateTime($date);


        /**
         *  Si la condition de réglement commence par 'FDM',
         * on affecte la date de fin de mois comme date de départ pour l'ajout du nb de jour
         */
        if (str_starts_with($conditionReglement, 'FDM')) {
            $lastDayOfMonth = $date->format('Y-m-t');
            var_dump($lastDayOfMonth);
            $date = new DateTime($lastDayOfMonth);
            var_dump($date);
        }

            $lastDay = $this->getDayEndMonthConst($conditionReglement);
            if (isset($lastDay))  // si le jour de fin de mois de la condition de règlement est != du mois civil et donc renseigné dans la constante
            {
                $checkDateEndOfMonth = $date->format('Y-m-' . $lastDay);
                if ($checkDateEndOfMonth >= $date) {
                    $date = new DateTime($checkDateEndOfMonth);

                } else {
                    $date = new DateTime($checkDateEndOfMonth);
                    $date->modify('+1 month');


                    return $date;
                }
               // $lastDayOfMonth = $date->format('Y-m-d');

                var_dump($date);
            }



        return $this->addDays($date, $condition);



    }

    /**
     * Ajoute des jours à une date
     * @throws Exception
     */
    public function addDays($date, $condition)
    {
        return $date->add(new \DateInterval('P'.$condition.'D'));
    }

    /**
     * Recherche la constante disponible de condition et récupère le nombre de jours de délai
     * @param $condition
     * @return int|string
     */
    public function getNbJourOfConst($condition): int|string
    {
        switch ($condition)
        {
            case 'CR15NET' :
                return ConditionReglementService::CR15NET['count'];
            case 'CR30NET' :
                return ConditionReglementService::CR30NET['count'];
            case 'CR45NET' :
                return ConditionReglementService::CR45NET['count'];
            case 'CR60NET' :
                return ConditionReglementService::CR60NET['count'];
            case 'CR30FDM' :
                return ConditionReglementService::CR30FDM['count'];
            case 'CR30FDM15' :
                return ConditionReglementService::CR30FDM15['count'];
            case 'CR45FDM' :
                return ConditionReglementService::CR45FDM['count'];
            case 'FDM15' :
                return ConditionReglementService::FDM15['count'];
            case 'FDM30':
                return ConditionReglementService::FDM30['count'];
            case 'FDM45' :
                return ConditionReglementService::FDM45['count'];
            default:
                return 'Aucune condition valable';
        }
    }

    /**
     * Recherche la valeur du jour de fin de mois si différent du mois civil
     * @param $condition
     * @return int|string
     */
    public function getDayEndMonthConst($condition): int|string
    {
        switch ($condition)
        {
            case 'CR30FDM15' :
                return ConditionReglementService::CR30FDM15['dayFinishedMonth'];

            default:
                return 'Aucune condition valable';
        }
    }
}