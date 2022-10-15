<?php

namespace App\Controller;

use function dump;
use App\Entity\Asistencia;
use App\Entity\TomaDeAsistencia;
use App\Repository\AlumnoRepository;
use Symfony\Component\Mercure\Update;
use App\Repository\AsistenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TomaDeAsistenciaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AsistenciaAlumnoController extends AbstractController
{

    private EntityManagerInterface $em;
    private TomaDeAsistenciaRepository $cr;
    private AsistenciaRepository $ar;


    private Session $session;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cr = $this->em->getRepository(TomaDeAsistencia::class);
        $this->ar = $this->em->getRepository(Asistencia::class);
    }

    #[Route('/a/{code}', name: 'app_asistencia_alumno')]
    public function index(string $code, Request $request, HubInterface $hub): Response
    {
        $this->session = $request->getSession();
        //$this->session->remove('alumno');

        //TODO: usar https://github.com/nayzo/NzoUrlEncryptorBundle para encriptar urls
        $idtomaasistencia = TomaDeAsistencia::urlDecode($code);

        if (is_numeric($idtomaasistencia)) {
            $idtomaasistencia = intval($idtomaasistencia);
        } else {
            $this->session->remove('alumno');
            throw new AccessDeniedHttpException();
        }

        $tomaasitencia = $this->cr->find($idtomaasistencia);
        if (is_null($tomaasitencia)) {
            $this->session->remove('alumno');
            throw new AccessDeniedHttpException();
        }

        if ($tomaasitencia->getEstado() != TomaDeAsistencia::ESTADO_INICIADO) {
            $this->session->remove('alumno');
            return $this->redirectToRoute('app_asistencia_alumno_no', ['code' => $code]);
        }

        if (!is_null($this->session->get('alumno', null))) {
            $alumno = $this->session->get('alumno');
            $asistencia = $this->ar->findOneBy(['alumno' => $alumno, 'tomaDeAsistencia' => $tomaasitencia]);
            $asistencia->setPresente(true);
            //$this->em->persist($asistencia);
            $this->em->flush();

            $update = new Update(
                'asistencia/' . $tomaasitencia->getId(),
                json_encode([
                    'id' => $asistencia->getId(),
                    'estado' => $asistencia->isPresente()
                ])
            );

            //dd($update);

            $hub->publish($update);
        } else {
            return $this->redirectToRoute('app_login_alumno_asistencia', ['code' => $code]);
        }
        return $this->render('asistencia_alumno/index.html.twig', [
            //'tomaassitencia' => $tomaasitencia,
        ]);
    }

    #[Route('/noa/{code}', name: 'app_asistencia_alumno_no')]
    public function no(string $code): Response
    {
        $idtomaasistencia = TomaDeAsistencia::urlDecode($code);

        if (!is_numeric($idtomaasistencia)) {
            throw new AccessDeniedHttpException();
        }

        return $this->render('asistencia_alumno/no.html.twig', [
            'code' => $code,
        ]);
    }
}
