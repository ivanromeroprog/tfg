<?php

namespace App\Controller;

use App\Entity\PresentacionActividad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ActividadAlumnoController extends AbstractController
{

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cr = $this->em->getRepository(PresentacionActividad::class);
        //$this->ar = $this->em->getRepository(Asistencia::class);
    }

    #[Route('/c/{code}', name: 'app_actividad_alumno')]
    public function index(string $code, Request $request, HubInterface $hub): Response
    {
        $this->session = $request->getSession();

        //TODO: usar https://github.com/nayzo/NzoUrlEncryptorBundle para encriptar urls
        $idpresentacionactividad = PresentacionActividad::urlDecode($code);

        if (is_numeric($idpresentacionactividad)) {
            $idpresentacionactividad = intval($idpresentacionactividad);
        } else {
            $this->session->remove('alumno');
            throw new AccessDeniedHttpException();
        }

        $presentacionactividad = $this->cr->find($idpresentacionactividad);
        if (is_null($presentacionactividad)) {
            $this->session->remove('alumno');
            throw new AccessDeniedHttpException();
        }

        if ($presentacionactividad->getEstado() != PresentacionActividad::ESTADO_INICIADO) {
            $this->session->remove('alumno');
            return $this->redirectToRoute('app_actividad_alumno_no', ['code' => $code]);
        }

        if (!is_null($this->session->get('alumno', null))) {
            /*
            $alumno = $this->session->get('alumno');
            $asistencia = $this->ar->findOneBy(['alumno' => $alumno, 'tomaDeAsistencia' => $tomaasitencia]);
            $asistencia->setPresente(true);
            //$this->em->persist($asistencia);
            $this->em->flush();
*/
            //inseguro
            /*
            $update = new Update(
                'asistencia/' . $tomaasitencia->getId(),
                json_encode([
                    'id' => $asistencia->getId(),
                    'estado' => $asistencia->isPresente()
                ])
            );
            */
            //Seguro
            /*
            $update = new Update(
                'asistencia/' . $tomaasitencia->getId(),
                json_encode([
                    'id' => $asistencia->getId(),
                    'estado' => $asistencia->isPresente()
                ]),
                true
            );

            $hub->publish($update);
            */
        } else {
            return $this->redirectToRoute('app_login_alumno', ['destino' => 'c', 'code' => $code]);
        }
        return $this->render('actividad_alumno/index.html.twig', [
            //'tomaassitencia' => $tomaasitencia,
        ]);
    }
}
