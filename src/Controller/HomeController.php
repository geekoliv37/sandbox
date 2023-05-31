<?php

namespace App\Controller;

use App\Service\ConditionReglementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET','POST'])]
    public function index(ConditionReglementService $conditionReglementService, Request $request): Response
    {
        //$data = json_decode($request->getContent(),true);
        $date = $request->query->get('date');
        $condition = $request->query->get('condition');
        $dateEnd = $conditionReglementService->getDateEnd($date, $condition);
       // $dateEnd = $conditionReglementService->getDateEnd( $data["date"], $data["condition"]);

        dd($dateEnd);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'dateEnd' => $dateEnd,
        ]);

    }
}
