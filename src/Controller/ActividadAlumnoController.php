<?php

namespace App\Controller;

use App\Entity\Actividad;
use App\Entity\DetalleActividad;
use App\Entity\Interaccion;
use App\Entity\PresentacionActividad;
use App\Repository\InteraccionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use function dump;

class ActividadAlumnoController extends AbstractController {

    private EntityManager $em;
    private InteraccionRepository $ir;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->cr = $this->em->getRepository(PresentacionActividad::class);
        $this->ir = $this->em->getRepository(Interaccion::class);
    }

    #[Route('/c/{code}', name: 'app_actividad_alumno')]
    public function index(string $code, Request $request, HubInterface $hub): Response {
        $presentacionactividad = $this->validarAcceso($code, $request);
        $alumno = $this->obtenerAlumno($request);

        if (is_null($alumno) || is_null($presentacionactividad)) {
            return $this->redirectToRoute('app_login_alumno', ['destino' => 'c', 'code' => $code]);
        } else {
            if ($presentacionactividad->getTipo() == Actividad::TIPO_CUESTIONARIO) {
                return $this->redirectToRoute('app_actividad_alumno_cuestionario', ['code' => $code]);
            } elseif ($presentacionactividad->getTipo() == Actividad::TIPO_RELACIONAR_CONCEPTOS) {
                //return $this->redirectToRoute('app_actividad_alumno_cuestionario', ['code' => $code]);
            } elseif ($presentacionactividad->getTipo() == Actividad::TIPO_COMPLETAR_TEXTO) {
                //return $this->redirectToRoute('app_actividad_alumno_cuestionario', ['code' => $code]);
            } elseif ($presentacionactividad->getTipo() == Actividad::TIPO_NUBE_DE_PALABRAS) {
                //return $this->redirectToRoute('app_actividad_alumno_cuestionario', ['code' => $code]);
            } else {
                throw new AccessDeniedHttpException();
            }
        }
    }

    #[Route('/c/{code}/cuestionario/{pregunta}', name: 'app_actividad_alumno_cuestionario')]
    public function cuestionario(Request $request, HubInterface $hub, string $code, int $pregunta = 0): Response {
        $presentacionactividad = $this->validarAcceso($code, $request);
        $alumno = $this->obtenerAlumno($request);

        if (is_null($alumno) || is_null($presentacionactividad)) {
            return $this->redirectToRoute('app_login_alumno', ['destino' => 'c', 'code' => $code]);
        } else {


            /*
             * DETALLES ACTIVIDAD
             */
            //Obtener todas las preguntas y respuestas y pasar a un formato array
            $detalles = $presentacionactividad->getDetallesPresentacionActividad();
            $lista_destalles = [];
            $i = -1;
            foreach ($detalles as $k => $detalle) {
                if ($detalle->getTipo() == DetalleActividad::TIPO_CUESTIONARIO_PREGUNTA) {
                    $i++;
                    $lista_destalles['preguntas'][$i] = $detalle;
                } else {
                    $lista_destalles['respuestas'][$i][] = $detalle;
                }
            }

            //Si el indice de pregunta pasado no se encuentra, error
            if ($pregunta < 0 || $pregunta > count($lista_destalles['preguntas']) - 1) {
                throw new AccessDeniedHttpException();
            }

            //Detalle Presentacion de la Pregunta Actual
            $preguntadetalle = $lista_destalles['preguntas'][$pregunta];

            /*
             * INTERACCIONES DEL ALUMNO
             */
            //Si existen, Buscar las respuestas correspondientes a la pregunta actual
            $interacciones_respuestas = [];
            $tmp = $this->ir->findByPregunta($alumno, $preguntadetalle, DetalleActividad::TIPO_CUESTIONARIO_RESPUESTA);
            foreach ($tmp as $interaccion) {
                $interacciones_respuestas[$interaccion->getDetallePresentacionActividad()->getId()] = $interaccion;
            }
            

            /*
             * FORM
             */

            //Preparo las opciones de respuesta y guardo si ya hay respuesta del alumno
            $choices = [];
            $marcadas = [];
            $correctas = 0;
            foreach ($lista_destalles['respuestas'][$pregunta] as $respuesta) {
                $choices[$respuesta->getDato()] = $respuesta->getId();
                if (isset($interacciones_respuestas[$respuesta->getId()])) {
                    $marcadas[] = $respuesta->getId();
                }
                if ($respuesta->isCorrecto()) {
                    $correctas++;
                }
            }
            if (!isset($marcadas[0]))
                $marcadas[0] = '';

            //Armo el form para la pregunta actual
            $defaultData = ['pregunta' => $preguntadetalle->getDato()];
            $builder = $this->createFormBuilder($defaultData);
            $builder->add('pregunta', TextareaType::class,
                    [
                        'attr' => ['readonly' => 'readonly'],
                        'label' => 'Pregunta º' . ($pregunta + 1)
            ]);
            $builder->add('respuestas', ChoiceType::class, [
                'choices' => $choices,
                'multiple' => ($correctas > 1),
                'expanded' => true,
                'data' => ($correctas > 1) ? $marcadas : $marcadas[0],
                            'constraints' => [
                new NotBlank()
            ],
            ]);
            $builder->add('Submit', SubmitType::class,
                    [
                        'label' => ($pregunta >= count($lista_destalles['preguntas']) - 1) ? 'Finalizar' : 'Siguiente',
                        'attr' => ['style' => 'float: right']
                    ]
            );
            $form = $builder->getForm();
            $form->handleRequest($request);

            /*
             * FORMULARIO ENVIADO
             */
            //dump($alumno);
            //dump($presentacionactividad);
            //dump($interacciones_respuestas);
            
            if ($form->isSubmitted() && $form->isValid()) {
                //dump($form->getData());

                //Si no existe la interaccion con esta pregunta la creamos
                $interaccion_pregunta = $this->ir->findOneBy(['alumno' => $alumno, 'detallePresentacionActividad' => $preguntadetalle]);
                if (is_null($interaccion_pregunta)) {
                    //Si la interaccion de la pregunta no existe, nunca se respondio, crearla
                    $interaccion_pregunta = new Interaccion(null, $alumno, $preguntadetalle);
                    $this->em->persist($interaccion_pregunta);
                }

                //Obtengo las respuestas enviadas por el form
                $respuestas_form = $form->getData()['respuestas'];
                if (!is_array($respuestas_form)) {
                    $respuestas_form = [$respuestas_form];
                }

                //TODO: Insertar/Eliminar usando transaccion
                //Despues redireccionar a la siguiente pregunta
                //Comparo las resp del form con las de la db para ver que eliminar/crear
                $respuestas_db = [];
                foreach ($interacciones_respuestas as $inter) {
                    $respuestas_db[] = $inter->getDetallePresentacionActividad()->getId();
                    if (!in_array($inter->getDetallePresentacionActividad()->getId(), $respuestas_form)) {
                        dump('no esta ' . $inter->getDetallePresentacionActividad()->getId() . ' borrar');
                    }
                }
                foreach ($respuestas_form as $resp) {
                    if (!in_array($resp, $respuestas_db)) {
                        dump('no esta ' . $resp . ' insertar');
                    }
                }
            }

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
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('actividad_alumno/index.html.twig', [
                    'presentacionactividad' => $presentacionactividad,
                    'form' => $form->createView(),
            'code' => $code,
            'preguntaanterior' => $pregunta-1
                        ], $response);
    }

    private function validarAcceso(string $code, Request $request): PresentacionActividad {

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
            return null;
        }

        return $presentacionactividad;
    }

    private function obtenerAlumno($request) {
        $this->session = $request->getSession();
        return $this->session->get('alumno', null);
    }

}
