<?php

namespace App\Controller;

use App\Entity\TomaDeAsistencia;
use App\Form\LoginAlumnoType;
//use App\Repository\AlumnoRepository;
use App\Repository\TomaDeAsistenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use function dd;

class LoginAlumnoController extends AbstractController {

    private EntityManagerInterface $em;
    private TomaDeAsistenciaRepository $cr;
    private AlumnoRepository $arepo;
    private Session $session;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->cr = $this->em->getRepository(TomaDeAsistencia::class);
        //$this->arepo = $this->em->getRepository(AlumnoRepository::class);
        $this->session = new Session();
    }

    #[Route('/login/alumno/asistencia/{code}', name: 'app_login_alumno_asistencia')]
    public function asistencia($code, Request $request): Response {

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

        $form = $this->createForm(LoginAlumnoType::class, null, ['curso' => $tomaasitencia->getCurso()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->get('alumno')->getData()->getCua(), $form->get('cua')->getData(), $form->get('cua')->getData() === $form->get('alumno')->getData()->getCua());
            if ($form->get('cua')->getData() === $form->get('alumno')->getData()->getCua()) {
                $this->session->set('alumno', $form->get('alumno')->getData());
                $this->redirectToRoute('app_asistencia_alumno', ['code' => $code]);
            } else {
                $this->addFlash('error', 'Los datos de acceso no son correctos, intentalo nuevamente.');
            }
        }

        return $this->render('login_alumno/index.html.twig', [
                    'form' => $form->createView()
        ]);
    }

}
