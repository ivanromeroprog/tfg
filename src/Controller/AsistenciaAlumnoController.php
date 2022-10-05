<?php

namespace App\Controller;

use App\Entity\TomaDeAsistencia;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AsistenciaAlumnoController extends AbstractController
{
    #[Route('/a/{code}', name: 'app_asistencia_alumno')]
    public function index(string $code): Response
    {
        dump(TomaDeAsistencia::urlDecode($code));
        return $this->render('asistencia_alumno/index.html.twig', [
            'controller_name' => 'AsistenciaAlumnoController',
        ]);
    }
}
