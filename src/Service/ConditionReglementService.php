<?php

namespace App\Service;

use DateTime;
use Exception;



class ConditionReglementService extends DateTime
{
    /** CR10NET => Condition de règlement 10 Jours NET  */
    const CR10NET = array('count' => 10, 'name' => '10 Jours NET');

    /** CR15NET => Condition de règlement 15 Jours NET  */
    const CR15NET = array('count' => 15, 'name' => '15 Jours NET');

    /** CR20NET => Condition de règlement 20 Jours NET  */
    const CR20NET = array('count' => 20, 'name' => '20 Jours NET');

    /** CR30NET => Condition de règlement 30 Jours calendaires  */
    const CR30NET = array('count' => 30, 'name' => '30 Jours NET');

    /** CR45NET => Condition de règlement 45 Jours calendaires  */
    const CR45NET = array('count' => 45, 'name' => '45 Jours NET');

    /** CR60NET => Condition de règlement 60 Jours calendaires  */
    const CR60NET = array('count' => 60, 'name' => '60 Jours NET');

    /** CR20FDM => Condition de règlement 20 Jours NET puis calcul de la fin de mois  */
    const CR20FDM = array('count' => 20, 'name' => '20 Jours Fin de mois');

    /** CR30FDM => Condition de règlement 30 Jours NET puis calcul de la fin de mois  */
    const CR30FDM = array('count' => 30, 'name' => '30 Jours Fin de mois');

    /** CR45FDM => Condition de règlement 45 Jours puis calcul de la fin de mois  */
    const CR45FDM = array('count' => 45, 'name' => '45 Jours Fin de mois');

    /** CR20FDM10 => Condition de règlement 20 Jours  puis calcul de la fin de mois au prochain 10 du mois  */
    const CR20FDM10= array('count' => 20, 'name' => '20 Jours Fin de mois le 10', 'dayEndMonth' => 10);

    /** CR30FDM10 => Condition de règlement 30 Jours puis calcul de la fin de mois au prochain 10 du mois  */
    const CR30FDM10 = array('count' => 30, 'name' => '30 Jours Fin de mois le 10', 'dayEndMonth' => 10);

    /** CR20FDM15 => Condition de règlement 20 Jours  puis calcul de la fin de mois au prochain 15 du mois  */
    const CR20FDM15 = array('count' => 20, 'name' => '20 Jours Fin de mois le 15', 'dayEndMonth' => 15);

    /** CR30FDM15 => Condition de règlement 30 Jours  puis calcul de la fin de mois au prochain 15 du mois  */
    const CR30FDM15 = array('count' => 30, 'name' => '30 Jours Fin de mois le 15', 'dayEndMonth' => 15);

    /** CR30FDM20 => Condition de règlement 30 Jours NET puis calcul de la fin de mois au prochain 20 du mois  */
    const CR30FDM20 = array('count' => 30, 'name' => '30 Jours Fin de mois le 20', 'dayEndMonth' => 20);

    /** FDM15 => Condition de règlement calcul à partir de la fin du mois civil + 15 Jours  */
    const FDM15 = array('count' => 15, 'name' => 'Fin de mois 15 Jours');

    /** FDM30 => Condition de règlement calcul à partir de la fin du mois civil + 30 Jours  */
    const FDM30 = array('count' => 30, 'name' => 'Fin de mois 30 Jours');

    /** FDM45 => Condition de règlement calcul à partir de la fin du mois civil + 45 Jours  */
    const FDM45 = array('count'=> 45, 'name' => 'Fin de mois 45 Jours');

    /**
     * Retourne true ou false si la date existe
     * @param string $date
     * @param string $format
     * @return bool
     */
    public function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d= DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    /**
     * * Vérifie si la condition existe dans les constantes
     * @param string $condition
     * @return bool
     */
    public function checkCondition(string $condition): bool
    {
        if (defined('self::'.$condition)){
            return true;
        }

        return false;
    }

    /**
     * Vérifie si erreur dans les données saisies par l'utilisateur et retourne
     * @param string $date
     * @param string $condition
     * @return false|string[]
     */
    public function checkIfError(string $date, string $condition): array|bool
    {

        if(!$this->validateDate($date)) {

            if(preg_match('/^\\d{4}-\d{2}-\d{2}/',$date))           // Si la date est au bon format XXXX-XX-XX
            {
                return ['error' => 'La date saisie n\'existe pas'];
            }

            return ['error' => 'Date invalide, format accepté : YYYY-MM-DD / Exemple 2023-05-13'];       // Sinon retour le message d'erreur lié au format
        }
        if(!$this->checkCondition($condition)) {
            return ['error' => "La condition de règlement n'existe pas"];
        }
        if (!$this->getNbJourOfConst($condition)){
            return ['error' => 'La constante condition de règlement n\'a pas de nombre de jours de délai renseigné dans la constante'];
        }
        if (!$this->getDayEndMonthConst($condition)){
            return ['error' => 'La constante condition de règlement n\'a pas de jour de fin de mois de renseignée'];
        }
        return false;
    }

    /**
     * *Envoie la date de fin depuis une date de début et une condition
     * @param string $date
     * @param string $condition
     * @return array|bool|DateTime|string[]
     * @throws Exception
     */
    public function getDateEnd(string $date, string $condition): array|DateTime|bool
    {
        $error = $this->checkIfError($date, $condition);   // Vérifie si les données saisies par l'utilisateur sont valides
        if ($error){
            return $error;     // Si une erreur est retournée alors
        }
        $conditionReglement = $condition;   // pour conserver la variable $condition d'origine pour critères de selection
        $condition = $this->getNbJourOfConst($condition);  // pour récupérer le nombre de jours compris dans le délai
        $lastDay = $this->getDayEndMonthConst($conditionReglement);  // Pour récupérer la date de fin de mois spécifique si existante
        $date = new DateTime($date);
        dump($date);

        /**
         *  Si la condition de réglement commence par 'FDM',
         * on affecte la date de fin de mois civil comme date de départ pour le calcul du délai de règlement
         */
        if (str_starts_with($conditionReglement, 'FDM')) {
            $date = new DateTime($date->format('Y-m-t'));
            return $this->addDays($date, $condition,$conditionReglement);
        }

        /**
         * Si le délai de paiement est calculé avant le calcul FDM ex: 30 Jours FDM ou 30 Jours FDM le 15
         *
         */

        if (preg_match('/CR\d{1,2}FDM/', $conditionReglement)) {
            $date = $this->addDays($date, $condition,$conditionReglement);

            if (isset($lastDay) && (!preg_match('/CR\d{1,2}FDM\b/', $conditionReglement))) // Si la date de FDM est != du mois civil
            {

                $date = $this->calcDayOfEndMonth($date, intval($lastDay));
            } else {
                $date = new DateTime($date->format('Y-m-t')); // retourne le dernier jour du mois civil
            }
        }

        /**
         * si le délai de paiement est calculé en jours NET
         */
        if (preg_match('/CR\S{2}NET/', $conditionReglement)) // nbre de jours du délai de paiement à 2 chiffres impératifs
        {
            return $this->addDays($date, $condition, $conditionReglement);
        }
        return $date;
    }


    /**
     * Détermine la date de fin de mois ( mois civil ou fin de mois spécifique) selon
     * si un jour de fin de mois != du mois civil est renseigné dans la constante,
     * On crée une date de contrôle avec le jour renseigné et le mois + année de la variable date
     * @throws Exception
     */
    public function calcDayOfEndMonth(DateTime $date, $lastDay): DateTime
    {
        $checkDateEndOfMonth = new DateTime($date->format('Y-m-'.$lastDay));
        if ($checkDateEndOfMonth >= $date) {    //  si la date de fin de mois de la condition de règlement sur le mois en cours n'est pas passée
            $date = $checkDateEndOfMonth;    // alors la date de base de calcul = jour de FDM de la condition sur le mois du document
        } else {
            $date = new DateTime($checkDateEndOfMonth->format('Y-m-d'));
            $date->modify('+1 month');   // Sinon la date de base de calcul = jour de FDM de la condition sur le mois m+1 du document
        }
        return $date;
    }

    /**
     * Ajoute des jours à une date
     * @param DateTime $date
     * @param int $condition
     * @param string $conditionReglement
     * @return DateTime
     * @throws Exception
     */
    public function addDays(DateTime $date, int $condition, string $conditionReglement): DateTime
    {
        /**
         *  Ajoute le nombre
         *  un délai de 30 Jours correspond à 1 mois sauf pour les conditions de règlement en Jours NET et XX Jours FDM
         *  pour gestion de la différence de nbre de jours dans un mois notamment le mois de février
         *
         */
        while($condition >= 30 &&  (!preg_match('/CR\S{2}NET/',$conditionReglement)) && (preg_match('/CR\S{2}FDM/',$conditionReglement)))
        {
            $start_day = $date->format('j');
            $date->modify('+1 month');  // ajout d'un mois
            $end_day = $date->format('j');

                // Si le numéro du jour de la date de fin calculée est != du numéro du jour de la date de début alors l'ajout du mois à eu comme impact de basculer sur le mois m+2
            if ($start_day != $end_day) {

                $date->modify('last day of previous month');  // Alors on ajuste la date en fixant le jour au dernier jour du mois précédent
            }
            $condition -= 30;  // On soustrait les 30 jours correspondant au mois du délai pour ajout du complément de jours uniquement
        }

        $date->add(new \DateInterval('P' . $condition . 'D'));
        return $date;
    }


    /**
     * Recherche la constante disponible de condition et récupère le nombre de jours de délai
     * @param string $condition
     * @return int|string
     */
    public function getNbJourOfConst(string $condition): int|string
    {
        switch ($condition) {
            case 'CR10NET' :
                return ConditionReglementService::CR10NET['count'];
            case 'CR15NET' :
                return ConditionReglementService::CR15NET['count'];
            case 'CR20NET' :
                return ConditionReglementService::CR20NET['count'];
            case 'CR30NET' :
                return ConditionReglementService::CR30NET['count'];
            case 'CR45NET' :
                return ConditionReglementService::CR45NET['count'];
            case 'CR60NET' :
                return ConditionReglementService::CR60NET['count'];
            case 'CR20FDM' :
                return ConditionReglementService::CR20FDM['count'];
            case 'CR30FDM' :
                return ConditionReglementService::CR30FDM['count'];
            case 'CR30FDM10' :
                return ConditionReglementService::CR30FDM10['count'];
            case 'CR20FDM10' :
                return ConditionReglementService::CR20FDM10['count'];
            case 'CR20FDM15' :
                return ConditionReglementService::CR20FDM15['count'];
            case 'CR30FDM15' :
                return ConditionReglementService::CR30FDM15['count'];
            case 'CR30FDM20' :
                return ConditionReglementService::CR30FDM20['count'];
            case 'CR45FDM' :
                return ConditionReglementService::CR45FDM['count'];
            case 'FDM15' :
                return ConditionReglementService::FDM15['count'];
            case 'FDM30':
                return ConditionReglementService::FDM30['count'];
            case 'FDM45' :
                return ConditionReglementService::FDM45['count'];
            default:
                return ' La condition de règlement n\'existe pas';
        }
    }

    /**
     * Recherche la valeur du jour de fin de mois si différent du mois civil
     * @param string $condition
     * @return int|string
     */
    public function getDayEndMonthConst(string $condition): int|string
    {
        switch ($condition)
        {
            case 'CR30FDM10' :
                return ConditionReglementService::CR30FDM10['dayEndMonth'];
            case 'CR30FDM15' :
                return ConditionReglementService::CR30FDM15['dayEndMonth'];
            default:
                return 'Il n\'y a pas de date de fin de mois spécifique à la condition de règlement' ;
        }
    }
    /**
     * @return mixed
     */

 /*   public function getAllConstant(){
        $reflect = new \ReflectionClass(get_class($this));
        return $reflect->getConstants();

    }
 */
    /**
     * Récupère un tableau avec l'ensemble des constantes de la class
     * @return array
     */
    public function getAllConstant():array
    {
        $class = new \ReflectionClass($this);
        $constants = $class->getConstants();
        $filteredConstants = [];

        foreach ($constants as $name => $value) {
            $declaringClass = $class->getReflectionConstant($name)->getDeclaringClass();
            if ($declaringClass->getName() === $class->getName()) {
                $filteredConstants[$name] = $value;
            }
        }
        return $filteredConstants;

    }

    /**
     * Retourne le nom de la constante en fonction de la key 'name'
     * @param string $name
     * @return array
     */
 /*   public function getConstantByName(string $name)
    {
        $constants = $this->getAllConstant();
        foreach ($constants as $key=>$constant) {
          //  if (in_array($name, $constant, true)) {

                return $constant;
            }


        return null;
    }
*/
}


/**
 * On contrôle que la date transmise par la requête est bien au bon format

$dateCheck = date_create_from_format('Y-m-d', $date);
if ($dateCheck === false) {
    var_dump('Invalid date format');
    dump('error_format_date');  // on retourne l'exception lié au format de date
    exit;
}
/**
 * @param $date
 * @return int
 * Fonction de contrôle pour vérifier que la date transmise existe bien ( ex: pas de 30 février ou de 32 Janvier! )


function checkDateExist( $date):int
{
    $date-> DateTime($date)->format('d');
    dump($date);
    $tempDate = explode('-', $date);  // extrait les nombres en format int (month, day, year)
    return checkdate((int)$tempDate[1], (int)$tempDate[2], (int)$tempDate[0]); // controle si date existante au format (month, day, year) et retourne true ou false
}

/**
 * Si la date n'existe pas, on retourne le message d'erreur correspondant

if (!checkDateExist($date)){
    return('error_invalid_date');
    exit;
}
/**
 * Contrôle si la condition de règlement existe et retourne un message d'erreur dans le cas contraire

function checkCondition(){
    if (!defined('self::'.$condition)){
        return false ;
    }
}
*/
