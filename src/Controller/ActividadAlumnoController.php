<?php

namespace App\Controller;

use Exception;
use function dump;
use App\Entity\Alumno;
use App\Entity\Actividad;
use App\Entity\Interaccion;
use App\Helpers\ArraysHelper;
use Doctrine\ORM\EntityManager;
use App\Entity\DetalleActividad;
use Symfony\Component\Form\Form;
use App\Repository\AlumnoRepository;
use App\Entity\PresentacionActividad;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InteraccionRepository;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ActividadAlumnoController extends AbstractController
{

    private EntityManager $em;
    private InteraccionRepository $ir;
    private AlumnoRepository $ar;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cr = $this->em->getRepository(PresentacionActividad::class);
        $this->ir = $this->em->getRepository(Interaccion::class);
        $this->ar = $this->em->getRepository(Alumno::class);
    }

    #[Route('/c/{code}', name: 'app_actividad_alumno')]
    public function index(string $code, Request $request, HubInterface $hub): Response
    {
        $presentacionactividad = $this->validarAcceso($code, $request);
        $alumno = $this->obtenerAlumno($request);
        if (is_null($presentacionactividad)) {
            return $this->redirectToRoute('app_actividad_alumno_no', ['code' => $code]);
        } elseif (is_null($alumno)) {
            return $this->redirectToRoute('app_login_alumno', ['destino' => 'c', 'code' => $code]);
        } else {
            if ($presentacionactividad->getTipo() == Actividad::TIPO_CUESTIONARIO) {
                return $this->redirectToRoute('app_actividad_alumno_cuestionario', ['code' => $code]);
            } elseif ($presentacionactividad->getTipo() == Actividad::TIPO_RELACIONAR_CONCEPTOS) {
                return $this->redirectToRoute('app_actividad_alumno_relacionar', ['code' => $code]);
            } elseif ($presentacionactividad->getTipo() == Actividad::TIPO_COMPLETAR_TEXTO) {
                //return $this->redirectToRoute('app_actividad_alumno_cuestionario', ['code' => $code]);
            } elseif ($presentacionactividad->getTipo() == Actividad::TIPO_NUBE_DE_PALABRAS) {
                //return $this->redirectToRoute('app_actividad_alumno_cuestionario', ['code' => $code]);
            } else {
                throw new AccessDeniedHttpException();
            }
        }
    }
    #[Route('/c/fin/{url}', name: 'app_actividad_alumno_fin')]
    public function fin(string $url): Response
    {
        return $this->render('actividad_alumno/fin.html.twig', [
            'url' => base64_decode(urldecode($url))
        ]);
    }

    #[Route('/c/{code}/cuestionario/{pregunta}', name: 'app_actividad_alumno_cuestionario')]
    public function cuestionario(Request $request, HubInterface $hub, string $code, int $pregunta = 0): Response
    {

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
                    $lista_destalles['respuestas'][$i][$detalle->getId()] = $detalle;
                }
            }
            unset($detalles);

            //Si el indice de pregunta pasado no se encuentra, error
            if ($pregunta < 0) {
                throw new AccessDeniedHttpException();
            }

            if ($pregunta > count($lista_destalles['preguntas']) - 1) {
                return $this->redirectToRoute('app_actividad_alumno_fin', [
                    'url' => urlencode(base64_encode(
                        $this->generateUrl('app_actividad_alumno_cuestionario', ['code' => $code, 'pregunta' => $pregunta - 1])
                    ))
                ]);
            }

            //Detalle Presentacion de la Pregunta Actual
            $preguntadetalle_actual = $lista_destalles['preguntas'][$pregunta];
            //Detalles Presentacion de las respuestas a la Pregunta Actual
            $respuestasdetalle_actual = $lista_destalles['respuestas'][$pregunta];

            /*
             * INTERACCIONES DEL ALUMNO
             */
            //Si existen, Buscar las respuestas correspondientes a la pregunta actual
            $interacciones_respuestas = [];
            $tmp = $this->ir->findByPregunta($alumno, $preguntadetalle_actual, DetalleActividad::TIPO_CUESTIONARIO_RESPUESTA);
            foreach ($tmp as $interaccion) {
                $interacciones_respuestas[$interaccion->getDetallePresentacionActividad()->getId()] = $interaccion;
            }
            unset($tmp);


            /*
             * FORM
             */
            //Preparo las opciones de respuesta y guardo en marcadas si ya hay respuesta del alumno
            $choices = [];
            $marcadas = [];
            $correctas = 0;
            foreach ($respuestasdetalle_actual as $respuesta) {
                $choices[$respuesta->getDato()] = $respuesta->getId();
                if (isset($interacciones_respuestas[$respuesta->getId()]) && !is_null($interacciones_respuestas[$respuesta->getId()]->isCorrecto())) {
                    $marcadas[] = $respuesta->getId();
                }
                if ($respuesta->isCorrecto()) {
                    $correctas++;
                }
            }
            if (!isset($marcadas[0]))
                $marcadas[0] = '';

            //Armo el form para la pregunta actual
            $defaultData = ['pregunta' => $preguntadetalle_actual->getDato()];
            $builder = $this->createFormBuilder($defaultData);
            $builder->add(
                'pregunta',
                TextareaType::class,
                [
                    'attr' => ['readonly' => 'readonly'],
                    'label' => 'Pregunta º' . ($pregunta + 1)
                ]
            );
            $builder->add('respuestas', ChoiceType::class, [
                'choices' => $choices,
                'multiple' => ($correctas > 1),
                'expanded' => true,
                'data' => ($correctas > 1) ? $marcadas : $marcadas[0],
                /*'constraints' => [
                    new NotBlank()
                ],*/
                'required' => false,
                'placeholder' => false,
                'choice_attr' => function () {
                    return ['class' => 'falserequired'];
                }
            ]);
            $builder->add(
                'Submit',
                SubmitType::class,
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
            if ($form->isSubmitted() && $form->isValid()) {
                //dump($form->getData());

                $this->em->getConnection()->beginTransaction(); // suspend auto-commit
                $error = '';

                try {

                    //Si no existe la interaccion con esta pregunta la creamos
                    $interaccion_pregunta = $this->ir->findOneBy(['alumno' => $alumno, 'detallePresentacionActividad' => $preguntadetalle_actual]);
                    if (is_null($interaccion_pregunta)) {
                        //Si la interaccion de la pregunta no existe, nunca se respondio, crearla
                        $interaccion_pregunta = new Interaccion(null, $alumno, $preguntadetalle_actual);
                        $this->em->persist($interaccion_pregunta);
                    }

                    //Obtengo las respuestas enviadas por el form
                    $respuestas_form = $form->getData()['respuestas'];
                    if (!is_array($respuestas_form)) {
                        $respuestas_form = [$respuestas_form];
                    }

                    //Comparo las resp del form con las de la db para ver que eliminar/crear
                    $respuestas_db = [];

                    foreach ($interacciones_respuestas as $inter) {

                        $respuestas_db[] = $inter->getDetallePresentacionActividad()->getId();

                        if (!in_array($inter->getDetallePresentacionActividad()->getId(), $respuestas_form) && !is_null($inter->isCorrecto())) {
                            //$this->em->remove($inter);
                            $inter->setCorrecto(null);
                            //dump('no esta ' . $inter->getDetallePresentacionActividad()->getId() . ' establecer null', $inter);
                        }
                    }

                    //dump($alumno);

                    foreach ($respuestas_form as $resp) {
                        if ($resp == '')
                            continue;

                        //Si no esta en el array de respiestas db crear nueva
                        if (!in_array($resp, $respuestas_db)) {

                            $inter = new Interaccion(
                                null,
                                $alumno,
                                $respuestasdetalle_actual[$resp],
                                $respuestasdetalle_actual[$resp]->isCorrecto()
                                //si es correcta la respuesta de este detalle pres actividad, es correcta la inter
                            );
                            //$this->em->persist($alumno);
                            $this->em->persist($inter);
                            $this->em->flush();

                            //->addInteraccion($inter);

                            //dump('no esta ' . $resp . ' insertar', $inter);

                            //Si ya esta solo modificar si es correcta o no
                        } else {
                            $interacciones_respuestas[$resp]->setCorrecto($respuestasdetalle_actual[$resp]->isCorrecto());
                            //dump('esta ' . $resp . ' modificar', $interacciones_respuestas[$resp]);
                        }
                    }

                    //Determinar si la pregunta esta respondida correctamente
                    $todas_correctas = true;
                    foreach ($respuestasdetalle_actual as $resp => $respuesta) {
                        if ($respuesta->isCorrecto() && !in_array($resp, $respuestas_form)) {
                            $todas_correctas = false;
                            break;
                        }
                    }
                    $interaccion_pregunta->setCorrecto($todas_correctas);

                    // dump($interaccion_pregunta);

                    //throw (new Exception());

                    //Mercure
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

                    $update = new Update(
                        'actividad/' . $presentacionactividad->getId(),
                        json_encode([
                            'idpregunta' => $preguntadetalle_actual->getId(),
                            'idalumno' => $alumno->getId(),
                            'correcto' => $interaccion_pregunta->isCorrecto()
                        ]),
                        true
                    );

                    $hub->publish($update);

                    // dump('actividad/' . $presentacionactividad->getId(), json_encode([
                    //     'id' => $interaccion_pregunta->getId(),
                    //     'estado' => $interaccion_pregunta->isCorrecto()
                    // ]));

                    //Si todo salio bien hago el commit
                    $this->em->flush();
                    $this->em->getConnection()->commit();
                } catch (Exception $e) {
                    $this->em->getConnection()->rollBack();
                    if ($error == '') {
                        $error = 'Error al guardar en la base de datos. '; // . $e->getMessage() . $e->getLine();
                    }
                }
                if ($error == '') {
                    //Redireccionar a la siguiente pregunta
                    return $this->redirectToRoute('app_actividad_alumno_cuestionario', ['code' => $code, 'pregunta' => $pregunta + 1]);
                } else {
                    $this->addFlash('error', $error);
                }
            }
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('actividad_alumno/index.html.twig', [
            'presentacionactividad' => $presentacionactividad,
            'form' => $form->createView(),
            'code' => $code,
            //'fin' => $fin,
            'preguntaanterior' => $pregunta - 1
        ], $response);
    }

    #[Route('/c/{code}/relacionar', name: 'app_actividad_alumno_relacionar')]
    public function relacionar(Request $request, HubInterface $hub, string $code): Response
    {

        $presentacionactividad = $this->validarAcceso($code, $request);
        $alumno = $this->obtenerAlumno($request);

        if (is_null($alumno) || is_null($presentacionactividad)) {
            return $this->redirectToRoute('app_login_alumno', ['destino' => 'c', 'code' => $code]);
        } else {


            /*
             * DETALLES ACTIVIDAD
             */
            //Obtener los conceptos y pasar a un formato array
            $detalles = $presentacionactividad->getDetallesPresentacionActividad();
            $lista_conceptos = [];
            $i = -1;
            foreach ($detalles as $k => $detalle) {
                //Si el concepto es tipo imagen, debo mostrar el HTML de la imagen
                //Sino debo limpiar el texto usando htmlentities
                if($detalle->isCorrecto())
                {
                    $detalle->setDato(
                        "<img class='img-thumbnail thumb-relacionar' src='/uploads/relacionar/".
                         $detalle->getDato() 
                         ."' alt='Imagen'>"
                    );
                }
                else
                {
                    $detalle->setDato('<div class="card"><div class="card-body">'.htmlentities($detalle->getDato()).'</div></div>');
                }

                if ($detalle->getTipo() == DetalleActividad::TIPO_RELACIONAR_CONCEPTOS_A) {
                    $i++;
                    $lista_conceptos['A'][$detalle->getRelacion()] = $detalle;
                } else {
                    $lista_conceptos['B'][$detalle->getRelacion()] = $detalle;
                }
            }
            //Mezclar los conceptos
            ArraysHelper::shuffle($lista_conceptos['A'], 458796);
            ArraysHelper::shuffle($lista_conceptos['B'], 458796);
            unset($detalles);

            // //Si el indice de pregunta pasado no se encuentra, error
            // if ($pregunta < 0) {
            //     throw new AccessDeniedHttpException();
            // }

            // if ($pregunta > count($lista_destalles['preguntas']) - 1) {
            //     return $this->redirectToRoute('app_actividad_alumno_fin', [
            //         'url' => urlencode(base64_encode(
            //             $this->generateUrl('app_actividad_alumno_relacionar', ['code' => $code, 'pregunta' => $pregunta - 1])
            //         ))
            //     ]);
            // }

            // //Detalle Presentacion de la Pregunta Actual
            // $preguntadetalle_actual = $lista_destalles['preguntas'][$pregunta];
            // //Detalles Presentacion de las respuestas a la Pregunta Actual
            // $respuestasdetalle_actual = $lista_destalles['respuestas'][$pregunta];

            /*
             * INTERACCIONES DEL ALUMNO
             */
            //Si existen, Buscar las respuestas correspondientes a la pregunta actual
            // $interacciones_respuestas = [];
            // $tmp = $this->ir->findByPregunta($alumno, $preguntadetalle_actual, DetalleActividad::TIPO_CUESTIONARIO_RESPUESTA);
            // foreach ($tmp as $interaccion) {
            //     $interacciones_respuestas[$interaccion->getDetallePresentacionActividad()->getId()] = $interaccion;
            // }
            // unset($tmp);


            /*
             * FORM
             */
            //Preparo las opciones de respuesta y guardo en marcadas si ya hay respuesta del alumno
            // $choices = [];
            // $marcadas = [];
            // $correctas = 0;
            // foreach ($respuestasdetalle_actual as $respuesta) {
            //     $choices[$respuesta->getDato()] = $respuesta->getId();
            //     if (isset($interacciones_respuestas[$respuesta->getId()]) && !is_null($interacciones_respuestas[$respuesta->getId()]->isCorrecto())) {
            //         $marcadas[] = $respuesta->getId();
            //     }
            //     if ($respuesta->isCorrecto()) {
            //         $correctas++;
            //     }
            // }
            // if (!isset($marcadas[0]))
            //     $marcadas[0] = '';

            //Armo el form para la pregunta actual
            // $defaultData = ['pregunta' => $preguntadetalle_actual->getDato()];
            // $builder = $this->createFormBuilder($defaultData);
            // $builder->add(
            //     'pregunta',
            //     TextareaType::class,
            //     [
            //         'attr' => ['readonly' => 'readonly'],
            //         'label' => 'Pregunta º' . ($pregunta + 1)
            //     ]
            // );
            // $builder->add('respuestas', CollectionType::class, [
            //     'entry_type'   => TextType::class,
            //     'multiple' => ($correctas > 1),
            //     'expanded' => true,
            //     'data' => ($correctas > 1) ? $marcadas : $marcadas[0],
            //     /*'constraints' => [
            //         new NotBlank()
            //     ],*/
            //     'required' => false,
            //     'placeholder' => false,
            //     'choice_attr' => function () {
            //         return ['class' => 'falserequired'];
            //     }
            // ]);
            // $builder->add(
            //     'Submit',
            //     SubmitType::class,
            //     [
            //         'label' => 'Finalizar',
            //         'attr' => ['style' => 'float: right']
            //     ]
            // );

            // $form = $builder->getForm();
            // $form->handleRequest($request);

            /*
             * FORMULARIO ENVIADO
             */
            // if ($form->isSubmitted() && $form->isValid()) {
            //     //dump($form->getData());

            //     $this->em->getConnection()->beginTransaction(); // suspend auto-commit
            //     $error = '';

            //     try {

            //         //Si no existe la interaccion con esta pregunta la creamos
            //         $interaccion_pregunta = $this->ir->findOneBy(['alumno' => $alumno, 'detallePresentacionActividad' => $preguntadetalle_actual]);
            //         if (is_null($interaccion_pregunta)) {
            //             //Si la interaccion de la pregunta no existe, nunca se respondio, crearla
            //             $interaccion_pregunta = new Interaccion(null, $alumno, $preguntadetalle_actual);
            //             $this->em->persist($interaccion_pregunta);
            //         }

            //         //Obtengo las respuestas enviadas por el form
            //         $respuestas_form = $form->getData()['respuestas'];
            //         if (!is_array($respuestas_form)) {
            //             $respuestas_form = [$respuestas_form];
            //         }

            //         //Comparo las resp del form con las de la db para ver que eliminar/crear
            //         $respuestas_db = [];

            //         foreach ($interacciones_respuestas as $inter) {

            //             $respuestas_db[] = $inter->getDetallePresentacionActividad()->getId();

            //             if (!in_array($inter->getDetallePresentacionActividad()->getId(), $respuestas_form) && !is_null($inter->isCorrecto())) {
            //                 //$this->em->remove($inter);
            //                 $inter->setCorrecto(null);
            //                 //dump('no esta ' . $inter->getDetallePresentacionActividad()->getId() . ' establecer null', $inter);
            //             }
            //         }

            //         //dump($alumno);

            //         foreach ($respuestas_form as $resp) {
            //             if ($resp == '')
            //                 continue;

            //             //Si no esta en el array de respiestas db crear nueva
            //             if (!in_array($resp, $respuestas_db)) {

            //                 $inter = new Interaccion(
            //                     null,
            //                     $alumno,
            //                     $respuestasdetalle_actual[$resp],
            //                     $respuestasdetalle_actual[$resp]->isCorrecto()
            //                     //si es correcta la respuesta de este detalle pres actividad, es correcta la inter
            //                 );
            //                 //$this->em->persist($alumno);
            //                 $this->em->persist($inter);
            //                 $this->em->flush();

            //                 //->addInteraccion($inter);

            //                 //dump('no esta ' . $resp . ' insertar', $inter);

            //                 //Si ya esta solo modificar si es correcta o no
            //             } else {
            //                 $interacciones_respuestas[$resp]->setCorrecto($respuestasdetalle_actual[$resp]->isCorrecto());
            //                 //dump('esta ' . $resp . ' modificar', $interacciones_respuestas[$resp]);
            //             }
            //         }

            //         //Determinar si la pregunta esta respondida correctamente
            //         $todas_correctas = true;
            //         foreach ($respuestasdetalle_actual as $resp => $respuesta) {
            //             if ($respuesta->isCorrecto() && !in_array($resp, $respuestas_form)) {
            //                 $todas_correctas = false;
            //                 break;
            //             }
            //         }
            //         $interaccion_pregunta->setCorrecto($todas_correctas);

            //         // dump($interaccion_pregunta);

            //         //throw (new Exception());

            //         //Mercure
            //         //inseguro
            //         /*
            //         $update = new Update(
            //         'asistencia/' . $tomaasitencia->getId(),
            //         json_encode([
            //         'id' => $asistencia->getId(),
            //         'estado' => $asistencia->isPresente()
            //         ])
            //         );
            //         */
            //         //Seguro

            //         $update = new Update(
            //             'actividad/' . $presentacionactividad->getId(),
            //             json_encode([
            //                 'idpregunta' => $preguntadetalle_actual->getId(),
            //                 'idalumno' => $alumno->getId(),
            //                 'correcto' => $interaccion_pregunta->isCorrecto()
            //             ]),
            //             true
            //         );

            //         $hub->publish($update);

            //         // dump('actividad/' . $presentacionactividad->getId(), json_encode([
            //         //     'id' => $interaccion_pregunta->getId(),
            //         //     'estado' => $interaccion_pregunta->isCorrecto()
            //         // ]));

            //         //Si todo salio bien hago el commit
            //         $this->em->flush();
            //         $this->em->getConnection()->commit();
            //     } catch (Exception $e) {
            //         $this->em->getConnection()->rollBack();
            //         if ($error == '') {
            //             $error = 'Error al guardar en la base de datos. '; // . $e->getMessage() . $e->getLine();
            //         }
            //     }
            //     if ($error == '') {
            //         //Redireccionar a la siguiente pregunta
            //         return $this->redirectToRoute('app_actividad_alumno_relacionar', ['code' => $code, 'pregunta' => $pregunta + 1]);
            //     } else {
            //         $this->addFlash('error', $error);
            //     }
            // }
        }

        //TODO: definir el status en base a si debo volver a mostrar el form o no
        $response = new Response(null, false ? 422 : 200);
        return $this->render('actividad_alumno/index.html.twig', [
            'presentacionactividad' => $presentacionactividad,
            'lista_conceptos' => $lista_conceptos,
            // 'form' => $form->createView(),
            'code' => $code,
            //'fin' => $fin,
            // 'preguntaanterior' => $pregunta - 1
        ], $response);
    }


    #[Route('/noc/{code}', name: 'app_actividad_alumno_no')]
    public function no(string $code): Response
    {
        $idpresentacionactividad = PresentacionActividad::urlDecode($code);

        if (!is_numeric($idpresentacionactividad)) {
            throw new AccessDeniedHttpException();
        }

        return $this->render('actividad_alumno/no.html.twig', [
            'code' => $code,
        ]);
    }

    private function validarAcceso(string $code, Request $request): ?PresentacionActividad
    {

        $this->session = $request->getSession();

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

        $alumno = $this->session->get('alumno', null);
        $curso = $presentacionactividad->getCurso();
        if (!is_null($alumno) && !$curso->hasAlumno($alumno)) {
            throw new AccessDeniedHttpException();
        }

        if ($presentacionactividad->getEstado() != PresentacionActividad::ESTADO_INICIADO) {
            $this->session->remove('alumno');
            return null;
        }

        return $presentacionactividad;
    }

    private function obtenerAlumno($request)
    {
        $this->session = $request->getSession();
        $alumno = $this->session->get('alumno', null);
        return
            is_null($alumno)
            ?
            null
            :
            $this->ar->find($alumno->getId());
    }
}
