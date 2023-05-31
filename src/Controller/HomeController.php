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
        dd($request->get('date'));
        $date = $request->query->get('date');
        $condition = $request->query->get('condition');
        $dateEnd = $conditionReglementService->getDateEnd( $date, $condition);

        dd($dateEnd);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);

    }
}
