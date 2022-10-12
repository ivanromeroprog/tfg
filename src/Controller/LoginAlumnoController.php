<?php

namespace App\Controller;

use App\Entity\TomaDeAsistencia;
use App\Form\LoginAlumnoType;
use App\Repository\AlumnoRepository;
use App\Repository\TomaDeAsistenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use function dump;

class LoginAlumnoController extends AbstractController {

    private EntityManagerInterface $em;
    private TomaDeAsistenciaRepository $cr;
    private AlumnoRepository $arepo;
    private Session $session;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->cr = $this->em->getRepository(TomaDeAsistencia::class);
        //$this->arepo = $this->em->getRepository(AlumnoRepository::class);
    }

    #[Route('/l/a/{code}', name: 'app_login_alumno_asistencia')]
    public function asistencia($code, Request $request): Response {
        $this->session = $request->getSession();
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
        if (is_null($tomaasitencia) || $tomaasitencia->getEstado() != TomaDeAsistencia::ESTADO_INICIADO) {
            throw new AccessDeniedHttpException();
        }

        $curso = $tomaasitencia->getCurso();
        
        $form = $this->createForm(LoginAlumnoType::class, null, ['curso' => $curso]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->get('alumno')->getData()->getCua(), $form->get('cua')->getData(), $form->get('cua')->getData() === $form->get('alumno')->getData()->getCua());
            //dump($this->session->get('alumno'));
            $alumno = $form->get('alumno')->getData();
            if (!is_null($alumno) && $form->get('cua')->getData() === $alumno->getCua() && $alumno->hasCurso($curso)) {
                $this->session->set('alumno', $alumno);
                //dd($this->session->get('alumno'));
                return $this->redirectToRoute('app_asistencia_alumno', ['code' => $code]);
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
