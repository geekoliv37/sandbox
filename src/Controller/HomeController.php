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
     * @throws \Exception
     */
    #[NoReturn] #[Route('/', name: 'app_home', methods: ['GET','POST'])]
    public function index(ConditionReglementService $conditionReglementService, Request $request): Response
    {

        $data = json_decode($request->getContent(),true);  // récupère les paramètres de calcul de la date d'échéance dans un array( date de départ , condition de règlement )
        $dateEnd = $conditionReglementService->getDateEnd( $data["date"], $data["condition"]); // Calcul de la date d'échéance

        //TODO Revérifier le format de date dans mon contrôle d'erreur
     //  dd($dateEnd);

        /**
         * Retourne un fichier JSON avec la date d'échéance calculée
         */
        $dateEnd = array_map('json_encode', $dateEnd);
        $jsonDateEnd = json_encode($dateEnd);
        dd($jsonDateEnd);
        $response = new Response($jsonDateEnd);
        dd($response);
        $response->headers->set('Content-Type', 'application/json');



        return $response;


        /**
         * Pour un affichage des conditions de règlements et du résultat du calcul de la date d'échéance directement dans la vue index.html.twig
         */
/*
        $constantList = $conditionReglementService->getAllConstant();       // Récupère un tableau avec la liste des conditions de règlement
        // $constantName = list($constantList);

        //  $constant = $conditionReglementService->getConstantByName($condition);      // Retourne le nom de la constante (code règlement) en fonction de la key 'name' de cette constante

        $date = $request->query->get('date');
        $condition = $request->query->get('condition');                         // Récupère la condition de règlement figurant en paramètre de la requête URL// Récupère la date figurant en paramètre de la requête URL
        $dateEnd = $conditionReglementService->getDateEnd($date, $condition);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'inputdate' => $date,
            'exampleSelect1' => $condition,
            'dateEnd' => $dateEnd,
            'constantList' => $constantList,
        ]);
*/

    }

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
