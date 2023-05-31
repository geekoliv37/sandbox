<?php

namespace App\Service;

use DateTime;
use Exception;


class ConditionReglementService extends DateTime
{
    /** CR15NET => Condition de règlement 15 Jours NET  */
    const CR15NET = array('count'=> 15 , 'name' => '15 Jours NET');

    /** CR30NET => Condition de règlement 30 Jours NET  */
    const CR30NET = array('count'=> 30, 'name' => '30 Jours NET');

    /** CR45NET => Condition de règlement 45 Jours NET  */
    const CR45NET = array('count'=> 45, 'name' => '45 Jours NET');

    /** CR60NET => Condition de règlement 60 Jours NET  */
    const CR60NET = array('count'=> 60, 'name' => '60 Jours NET');

    /** CR30FDM => Condition de règlement 30 Jours NET calcul fin de mois  */
    const CR30FDM = array('count'=> 30, 'name' => '30 Jours Fin de mois');
    const CR45FDM = array('count'=> 45, 'name' => '45 Jours Fin de mois');
    const CR30FDM10 = array('count'=> 30, 'name' => '30 Jours Fin de mois le 10', 'dayEndMonth' => 10 );
    const CR30FDM15 = array('count'=> 30, 'name' => '30 Jours Fin de mois le 15', 'dayEndMonth' => 15 );
    const FDM15 = array('count'=> 15, 'name' => 'Fin de mois 15 Jours');
    const FDM30 = array('count'=> 30, 'name' => 'Fin de mois 30 Jours');
    const FDM45 = array('count'=> 45, 'name' => 'Fin de mois 45 Jours');




    /**
     * Envoie la date de fin depuis une date de début et une condtion
     * @param string $date
     * @param string $condition
     * @return DateTime
     * @throws Exception
     */

    public function getDateEnd(string $date, string $condition): DateTime
    {
        $conditionReglement = $condition;   // pour conserver la variable $condition d'origine
        $condition = $this->getNbJourOfConst($condition);
        $lastDay = $this->getDayEndMonthConst($conditionReglement);
        $date = new DateTime($date);



        /**
         *  Si la condition de réglement commence par 'FDM',
         * on affecte la date de fin de mois comme date de départ pour l'ajout du nb de jour
         */
        if (str_starts_with($conditionReglement, 'FDM')) {
            $date = new DateTime($date->format('Y-m-t'));
            return $this->addDays($date, $condition);
        }
        /**
         * Si le délai de paiement est calculé avant le calcul FDM ex: 30 Jours FDM ou 30 Jours FDM le 15
         *
         */

        if (preg_match('/CR\d{1,2}FDM/',$conditionReglement))
        {
            $date = $this->addDays($date, $condition);

            if (isset($lastDay) && $lastDay !== 'Aucune condition valable') // Si la date de FDM est != du mois civil
            {
                $date = $this-> calcDayOfEndMonth($date,$lastDay);
            }else{
                $date = new DateTime($date->format('Y-m-t')); // retourne le dernier jour du mois civil
            }
        }

        /**
         * si le délai de paiement est calculé en jours NET
         */
        if (preg_match('/CR\SNET/',$conditionReglement))
        {
            return $this->addDays($date, $condition);
        }
        return $date;
    }


    /**
     * si un jour de fin de mois != du mois civil est renseigné dans la constante,
     * On crée une date de contrôle avec le jour renseigné et le mois + année de la variable date
     * @throws Exception
     */
    public function calcDayOfEndMonth($date, $lastDay){

        $checkDateEndOfMonth = new DateTime($date->format('Y-m-'.$lastDay)) ;
        if ($checkDateEndOfMonth >= $date) {    //  si la date de fin de mois de la condition de règlement sur le mois en cours n'est pas passée
            $date = $checkDateEndOfMonth;    // alors la date de base de calcul = jour de FDM de la condition sur le mois du document
            return $date;
        } else {
            $date = new DateTime($checkDateEndOfMonth->format('Y-m-d'));
            $date->modify('+1 month');   // Sinon la date de base de calcul = jour de FDM de la condition sur le mois m+1 du document
            return $date;
        }
    }

    /**
     * Ajoute des jours à une date
     * @throws Exception
     */
    public function addDays($date, $condition): DateTime
    {
        $date->add(new \DateInterval('P'.$condition.'D'));
        return $date;
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
            case 'CR30FDM10' :
                return ConditionReglementService::CR30FDM10['count'];
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
            case 'CR30FDM10' :
                return ConditionReglementService::CR30FDM10['dayEndMonth'];
            case 'CR30FDM15' :
                return ConditionReglementService::CR30FDM15['dayEndMonth'];
            default:
                return 'Aucune condition valable';
        }
    }
}