<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class MisDatosController extends AbstractController
{
    #[Route('/mis/datos', name: 'app_mis_datos')]
    public function index(): Response
    {
        return $this->render('mis_datos/index.html.twig', [
            'controller_name' => 'MisDatosController',
        ]);
    }
}
