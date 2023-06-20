<?php

namespace App\Controller;

use App\Service\ConditionReglementService;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    /**
     * @param ConditionReglementService $conditionReglementService
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    #[NoReturn] #[Route('/', name: 'app_home', methods: ['GET','POST'])]
    public function index(ConditionReglementService $conditionReglementService, Request $request): Response
    {

        $data = json_decode($request->getContent(),true);  // récupère les paramètres de calcul de la date d'échéance dans un array( date de départ , condition de règlement )

        if (!isset($data["date"]) && !isset($data["condition"])) {
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'inputdate' => $date ?? null,
                'error' => $error ?? null,
                'exampleSelect1' => $condition ?? null,
                'dateEnd' => $dateEnd ?? null,
                'constantList' => $constantList ?? null,
            ]);
        }

        $dateEnd = $conditionReglementService->getDateEnd($data["date"], $data["condition"]); // Calcul de la date d'échéance

            //TODO Revérifier le format de date dans mon contrôle d'erreur

            /**
             * Retourne un fichier JSON avec la date d'échéance calculée
             */

        $jsonDateEnd = json_encode($dateEnd);

        $jsonResult = json_decode($jsonDateEnd);   // pour test du résultat fichier JSON décodé
         dd($jsonResult);
        $response = new Response($jsonDateEnd);

        $response->headers->set('Content-Type', 'application/json');


        return $response;

    }

        /**
         * Pour un affichage des conditions de règlements et du résultat du calcul de la date d'échéance directement dans la vue index.html.twig
         */
        #[NoReturn] #[Route('/date', name: 'date', methods: ['GET','POST'])]
        public function dateEcheance(ConditionReglementService $conditionReglementService, Request $request): Response
    {

        $constantList = $conditionReglementService->getAllConstant();       // Récupère un tableau avec la liste des conditions de règlement
        // $constantName = list($constantList);

        //  $constant = $conditionReglementService->getConstantByName($condition);      // Retourne le nom de la constante (code règlement) en fonction de la key 'name' de cette constante

        $date = $request->query->get('date');
        $condition = $request->query->get('condition');                   // Récupère la condition de règlement figurant en paramètre de la requête URL// Récupère la date figurant en paramètre de la requête URL
        if(!empty($date) && !empty($condition)) {
            $error = $conditionReglementService->checkIfError($date, $condition);
        }
      //  dd($error);
      //  if(!in_array($condition,$constantList){
       // }
     //   if(!$error)){}
        if(!empty($date) && !empty($condition) && !$error )
        {
        $dateEnd = $conditionReglementService->getDateEnd($date, $condition);
        }
        return $this->render('home/date.html.twig', [
            'controller_name' => 'HomeController',
            'inputdate' => $date,
            'error' => $error ?? null,
            'exampleSelect1' => $condition,
            'dateEnd' => $dateEnd ?? null,
            'constantList' => $constantList,
        ]);
    }

    /**
     * @param ConditionReglementService $conditionReglementService
     * @param Request $request
     * @return Response
     */
    #[Route('/list', name: 'list', methods: ['GET','POST'])]
    public function getAllConditions(ConditionReglementService $conditionReglementService, Request $request):Response
    {
        $constantList = $conditionReglementService->getAllConstant();       // Récupère un tableau avec la liste des conditions de règlement

        /**
         * Création d'un array pour reprendre uniquement le nom des constantes Conditions de règlement (code règlement)
         * et le libellé des conditions de règlement
         */
        $constantNameList = [];
        foreach ($constantList as $key => $value) {
            $constantNameList[$key] = $value['name'];
        }

        /**
         *Création du fichier JSON avec le code et le libellé des différentes conditions
         * de règlement récupérables dans l'API pour
         */
        $jsonData = json_encode($constantNameList);

        $response = new Response($jsonData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }



}
