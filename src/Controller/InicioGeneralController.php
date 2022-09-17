<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InicioGeneralController extends AbstractController
{
    #[Route('/', name: 'app_inicio_general')]
    public function index(): Response
    {
        return $this->render('inicio_general/index.html.twig', [
            'controller_name' => 'InicioGeneralController',
        ]);
    }
}
