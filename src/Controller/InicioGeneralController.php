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
        if($this->isGranted('ROLE_DOCENTE')){
            return $this->redirectToRoute('app_inicio_docente');
        }else if($this->isGranted('ROLE_RESPONSABLE')){
            //return $this->redirectToRoute('app_inicio_responsable');
        }
        
        
        return $this->render('inicio_general/index.html.twig', [
            'controller_name' => 'InicioGeneralController',
        ]);
    }
}
