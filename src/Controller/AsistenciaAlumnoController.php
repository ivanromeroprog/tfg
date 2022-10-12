<?php

namespace App\Controller;

use App\Entity\TomaDeAsistencia;
use App\Repository\AlumnoRepository;
use App\Repository\TomaDeAsistenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use function dump;

class AsistenciaAlumnoController extends AbstractController
{

    private EntityManagerInterface $em;
    private TomaDeAsistenciaRepository $cr;

    private Session $session;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cr = $this->em->getRepository(TomaDeAsistencia::class);

        $this->session = new Session();
    }

    #[Route('/a/{code}', name: 'app_asistencia_alumno')]
    public function index(string $code): Response
    {
        //TODO: usar https://github.com/nayzo/NzoUrlEncryptorBundle para encriptar urls
        $idtomaasistencia = TomaDeAsistencia::urlDecode($code);

        if (is_numeric($idtomaasistencia)) {
            $idtomaasistencia = intval($idtomaasistencia);
        } else {
            throw new AccessDeniedHttpException();
        }

        $tomaasitencia = $this->cr->find($idtomaasistencia);
        if (is_null($tomaasitencia) || $tomaasitencia->getEstado() != TomaDeAsistencia::ESTADO_INICIADO) {
            $this->session->remove('alumno');
            throw new AccessDeniedHttpException();
        }

        if ($this->session->get('alumno', null) <> null) {
            dump($this->session->get('alumno'));
        } else {
            return $this->redirectToRoute('app_login_alumno_asistencia', ['code' => $code, 'tipo_ingreso' => 'asistencia']);
        }
        return $this->render('asistencia_alumno/index.html.twig', [
            //'tomaassitencia' => $tomaasitencia,
        ]);
    }
}
