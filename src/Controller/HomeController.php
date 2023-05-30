<?php

namespace App\Controller;

use App\Service\ConditionReglementService;
use ContainerFxGJ40q\getConditionReglementServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ConditionReglementService $conditionReglementService, Request $request): Response
    {
       // $conditionReglement = new ConditionReglementService();
      //  dd ($conditionReglement);
        dd($conditionReglementService->getDateEnd($request->get('date'), $request->get('condition')));
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);

    }
}
