<?php

namespace App\Controller;

use function dump;
use App\Form\LoginAlumnoType;
use App\Entity\TomaDeAsistencia;
use App\Repository\AlumnoRepository;
use App\Entity\PresentacionActividad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TomaDeAsistenciaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LoginAlumnoController extends AbstractController
{

    private EntityManagerInterface $em;
    private TomaDeAsistenciaRepository $cr;
    private AlumnoRepository $arepo;
    private Session $session;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cr = $this->em->getRepository(TomaDeAsistencia::class);
        $this->pa = $this->em->getRepository(PresentacionActividad::class);
    }

    #[Route('/l/{destino}/{code}', name: 'app_login_alumno')]
    public function asistencia($destino, $code, Request $request): Response
    {
        $this->session = $request->getSession();
        if ($destino == 'a') {

            if (!is_null($this->session->get('alumno', null))) {
                return $this->redirectToRoute('app_asistencia_alumno', ['code' => $code]);
            }

            $idtomaasistencia = TomaDeAsistencia::urlDecode($code);

            if (is_numeric($idtomaasistencia)) {
                $idtomaasistencia = intval($idtomaasistencia);
            } else {
                throw new AccessDeniedHttpException();
            }

            $tomaasitencia = $this->cr->find($idtomaasistencia);
            if (is_null($tomaasitencia)) {
                $this->session->remove('alumno');
                throw new AccessDeniedHttpException();
            } elseif ($tomaasitencia->getEstado() != TomaDeAsistencia::ESTADO_INICIADO) {
                return $this->redirectToRoute('app_asistencia_alumno_no', ['code' => $code]);
            }

            $curso = $tomaasitencia->getCurso();
        } else {
            if (!is_null($this->session->get('alumno', null))) {
                return $this->redirectToRoute('app_actividad_alumno', ['code' => $code]);
            }

            $idpresentacionactividad = PresentacionActividad::urlDecode($code);

            if (is_numeric($idpresentacionactividad)) {
                $idpresentacionactividad = intval($idpresentacionactividad);
            } else {
                $this->session->remove('alumno');
                throw new AccessDeniedHttpException();
            }

            $presentacionactividad = $this->pa->find($idpresentacionactividad);
            if (is_null($presentacionactividad)) {
                $this->session->remove('alumno');
                throw new AccessDeniedHttpException();
            } elseif ($presentacionactividad->getEstado() != PresentacionActividad::ESTADO_INICIADO) {
                return $this->redirectToRoute('app_actividad_alumno_no', ['code' => $code]);
            }

            $curso = $presentacionactividad->getCurso();
        }

        $form = $this->createForm(LoginAlumnoType::class, null, ['curso' => $curso]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->get('alumno')->getData()->getCua(), $form->get('cua')->getData(), $form->get('cua')->getData() === $form->get('alumno')->getData()->getCua());
            //dump($this->session->get('alumno'));
            $alumno = $form->get('alumno')->getData();
            if (!is_null($alumno) && $form->get('cua')->getData() === $alumno->getCua() && $alumno->hasCurso($curso)) {
                $this->session->set('alumno', $alumno);
                //dd($this->session->get('alumno'));
                if ($destino === 'a')
                    return $this->redirectToRoute('app_asistencia_alumno', ['code' => $code]);
                else
                    return $this->redirectToRoute('app_actividad_alumno', ['code' => $code]);
            } else {
                $this->session->remove('alumno');
                $this->addFlash('error', 'Los datos de acceso no son correctos, intentalo nuevamente.');
            }
        }



        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('login_alumno/index.html.twig', [
            'form' => $form->createView()
        ], $response);
    }
}
