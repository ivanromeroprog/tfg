<?php

namespace App\Controller;

use App\Entity\Actividad;
use App\Entity\DetalleActividad;
use App\Form\ActividadType;
use App\Repository\ActividadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use function dump;

#[IsGranted('ROLE_DOCENTE')]
class ActividadController extends AbstractController
{

    private EntityManagerInterface $em;
    private ActividadRepository $cr;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cr = $this->em->getRepository(Actividad::class);
    }

    #[Route('/actividad', name: 'app_actividad')]
    public function index(Request $request): Response
    {

        $perpage = $request->query->getInt('perpage', 10);
        $page = $request->query->getInt('page', 1);
        $order = $request->query->getInt('order', 0);
        $search = $request->query->get('search', '');
        if ($perpage < 1)
            $perpage = 10;

        $listqb = $this->cr->listQueryBuilder(
            $search !== '' ?
                [
                    'titulo' => $search,
                    'descripcion' => $search,
                    'tipo' => $search,
                    //'estado' => $search
                ] : [],
            $order,
            $this->getUser()
        );

        $pager = new Pagerfanta(new QueryAdapter($listqb));
        $pager->setMaxPerPage($perpage);
        $pager->setCurrentPage($page);

        return $this->render('actividad/index.html.twig', [
            'pager' => $pager,
            'order' => $order,
            'search' => $search,
            'perpageoptions' => [
                10, 25, 50, 100
            ]
        ]);
    }

    #[Route('/actividad/nuevo', name: 'app_actividad_new')]
    public function new(Request $request): Response
    {

        //Obtener datos de post por fuera del form, sino no se puede modificar los campos :(
        $tipo = null;
        $alldata = $request->request->all();
        if (isset($alldata['actividad'])) {
            $data = $alldata['actividad'];
            $tipo = isset($data['tipo']) ? $data['tipo'] : null;
        }

        //Obtener detalles por fuera del form
        if (isset($alldata['detalle'])) {
            $detalles = $alldata['detalle'];
        } else {
            $detalles = null;
        }

        //Gestión normal del form
        $actividad = new Actividad();
        $actividad->setUsuario($this->getUser());
        $form = $this->createForm(ActividadType::class, $actividad, [
            'tipo' => $tipo
        ]);

        //Seleccionar tipo de actividad y gestionar
        switch ($tipo) {
            case Actividad::TIPO_RELACIONAR_CONCEPTOS:
                return $this->gestionarNuevoRelacionarConceptos($form, $request, $detalles, $actividad);
                break;
            case Actividad::TIPO_CUESTIONARIO:
                return $this->gestionarNuevoCuestionario($form, $request, $detalles, $actividad);
                break;
            default:
                //Respuesta si no hay tipo de dato definido
                $response = new Response(null, $form->isSubmitted() ? 422 : 200);
                return $this->render('actividad/new.html.twig', [
                    'form' => $form->createView(),
                    'tipo' => null,
                    'view' => false,
                    'nuevo' => 1,
                    'nocache' => true
                ], $response);
        }
    }

    #[Route('/actividad/editar/{id}', name: 'app_actividad_edit')]
    public function edit(int $id, Request $request): Response
    {
        if ($id < 1)
            throw new AccessDeniedHttpException();

        $actividad = $this->cr->find($id);

        if (is_null($actividad) || $actividad->getUsuario() != $this->getUser())
            throw new AccessDeniedHttpException();

        //Obtener detalles por fuera del form
        $alldata = $request->request->all();
        if (isset($alldata['detalle'])) {
            $detalles = $alldata['detalle'];
        } else {
            $detalles = null;
        }

        //Tipo de actividad
        $tipo = $actividad->getTipo();

        //Crear formulario
        $form = $this->createForm(ActividadType::class, $actividad, [
            'tipo' => $tipo
        ]);
        $form->handleRequest($request);

        //Agregar a los detalles de POST los detalles de la DB      
        $detallesdb = $this->formatearPreguntasDB($actividad);
        $detalles = (is_null($detalles) ? $detallesdb : array_merge($detallesdb, $detalles));

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->guardarCuestionario($actividad, $detalles);
            if ($error == '') {
                $this->addFlash('success', 'Se modificó la actividad correctamente.');
                return $this->redirectToRoute('app_actividad_edit', ['id' => $actividad->getId()]);
            } else {
                $this->addFlash('error', $error);
            }
        }

        //Generar HTML de preguntas enviadas por Post y de la DB
        $preguntatemplate = str_replace(["\n", "\t", "\r"], '', $this->renderView('actividad/tipo/cuestionario/pregunta.html.twig', ['view' => false]));
        $respuestatemplate = str_replace(["\n", "\t", "\r"], '', $this->renderView('actividad/tipo/cuestionario/respuesta.html.twig', ['view' => false]));
        $detalleshtml = $this->generarPreguntasHtml($detalles, $preguntatemplate, $respuestatemplate);

        //Respuesta
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('actividad/edit.html.twig', [
            'form' => $form->createView(),
            'tipo' => $tipo,
            'respuestatemplate' => $respuestatemplate,
            'preguntatemplate' => $preguntatemplate,
            'detalleshtml' => $detalleshtml,
            'nuevo' => empty($detalleshtml) ? 1 : 0,
            'nocache' => true,
            'view' => false,
            'detalles_eliminar' => isset($detalles['eliminar']) ? $detalles['eliminar'] : '',
        ], $response);
    }

    #[Route('/actividad/ver/{id}', name: 'app_actividad_view')]
    public function view(int $id): Response
    {
        if ($id < 1)
            throw new AccessDeniedHttpException();

        $actividad = $this->cr->find($id);

        if (is_null($actividad) || $actividad->getUsuario() != $this->getUser())
            throw new AccessDeniedHttpException();

        //Tipo de actividad
        $tipo = $actividad->getTipo();

        //Crear formulario
        $form = $this->createForm(ActividadType::class, $actividad, [
            'tipo' => $tipo,
            'view' => true,
        ]);


        //Seleccionar tipo de actividad y gestionar
        switch ($tipo) {
            case Actividad::TIPO_RELACIONAR_CONCEPTOS:

                //Detalles de la base de datos      
                $detalles = $this->formatearParejasDB($actividad);
                return $this->gestionarNuevoRelacionarConceptos($form, null, $detalles, $actividad, true);
                break;
            case Actividad::TIPO_CUESTIONARIO:

                //Detalles de la base de datos      
                $detalles = $this->formatearPreguntasDB($actividad);
                return $this->gestionarNuevoCuestionario($form, null, $detalles, $actividad, true);
                break;
        }
    }

    #[Route('/actividad/eliminar/{id}', name: 'app_actividad_delete', methods: ['GET', 'HEAD'])]
    public function delete(int $id): Response
    {
        if ($id < 1)
            throw new AccessDeniedHttpException();

        $actividad = $this->cr->find($id);

        if (is_null($actividad) || $actividad->getUsuario() != $this->getUser())
            throw new AccessDeniedHttpException();

        //Tipo de actividad
        $tipo = $actividad->getTipo();

        //Crear formulario
        $form = $this->createForm(ActividadType::class, $actividad, [
            'tipo' => $tipo,
            'view' => true,
        ]);

        //Detalles de la base de datos      
        $detalles = $this->formatearPreguntasDB($actividad);

        //Generar HTML de preguntas enviadas por Post y de la DB
        $preguntatemplate = str_replace(["\n", "\t", "\r"], '', $this->renderView('actividad/tipo/cuestionario/pregunta.html.twig', ['view' => true]));
        $respuestatemplate = str_replace(["\n", "\t", "\r"], '', $this->renderView('actividad/tipo/cuestionario/respuesta.html.twig', ['view' => true]));
        $detalleshtml = $this->generarPreguntasHtml($detalles, $preguntatemplate, $respuestatemplate);

        return $this->render('actividad/delete.html.twig', [
            'form' => $form->createView(),
            'tipo' => $tipo,
            'respuestatemplate' => $respuestatemplate,
            'preguntatemplate' => $preguntatemplate,
            'detalleshtml' => $detalleshtml,
            'nuevo' => 0,
            'nocache' => true,
            'detalles_eliminar' => '',
            'view' => true
        ]);
    }

    #[Route('/actividad/eliminar', name: 'app_actividad_dodelete', methods: ['DELETE'])]
    public function doDelete(Request $request): Response
    {

        $submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('borrarcosa', $submittedToken)) {
            throw new AccessDeniedHttpException();
        }

        $id = $request->get('id');

        if (is_numeric($id)) {
            $id = intval($id);
            if ($id < 1) {
                throw new AccessDeniedHttpException();
            }
        } else {
            throw new AccessDeniedHttpException();
        }

        $actividad = $this->cr->find($id);

        $this->em->remove($actividad);
        try {
            $this->em->flush();
            $this->addFlash('success', 'Se eliminó la actividad correctamente.');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar la actividad. Ya se utilizó.');
        }
        return $this->redirectToRoute('app_actividad');
    }

    /**
     * Gestiona la creación de una nueva actividad cuando el tipo es Relacionar Conceptos
     *
     * @return Response respuesta
     */
    private function gestionarNuevoRelacionarConceptos(
        FormInterface $form,
        ?Request $request,
        ?array $detalles,
        Actividad $actividad,
        bool $view = false
    ): Response {
        //Si se enviaron los datos correctos al form y se hizo clic en Guardar...
        //Guardar la nueva actividad y terminar

        if (!$view) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid() && $form->get('guardar')->isClicked()) {

                $error = $this->guardarRelacionarConceptos($actividad, $detalles);
                if ($error == '') {
                    $this->addFlash('success', 'Se guardó la actividad correctamente.');
                    //return $this->redirectToRoute('app_actividad_edit', ['id' => $actividad->getId()]);
                    return $this->redirectToRoute('app_actividad');
                } else {
                    $this->addFlash('error', $error);
                }
            }
        }

        //Generar HTML de parejas enviadas por Post
        $parejatemplate = str_replace(["\n", "\t", "\r"], '', $this->renderView('actividad/tipo/relacionar/pareja.html.twig', ['view' => $view]));
        $detalleshtml = $this->generarParejasHtml($detalles, $parejatemplate);

        //Respuesta
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('actividad/new.html.twig', [
            'form' => $form->createView(),
            'tipo' => Actividad::TIPO_RELACIONAR_CONCEPTOS,
            'view' => $view,
            'parejatemplate' => $parejatemplate,
            'detalleshtml' => $detalleshtml,
            'nuevo' => empty($detalleshtml) ? 1 : 0,
            'nocache' => !$view,
            'detalles_eliminar' => '',
        ], $response);
    }

    /**
     * Gestiona la creación de una nueva actividad cuando el tipo es Cuestionario
     *
     * @return Response respuesta
     */
    private function gestionarNuevoCuestionario(
        FormInterface $form,
        ?Request $request,
        ?array $detalles,
        Actividad $actividad,
        bool $view = false
    ): Response {
        //Si se enviaron los datos correctos al form y se hizo clic en Guardar...
        //Guardar la nueva actividad y terminar
        if (!$view) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid() && $form->get('guardar')->isClicked()) {
                $error = $this->guardarCuestionario($actividad, $detalles);
                if ($error == '') {
                    $this->addFlash('success', 'Se guardó la actividad correctamente.');
                    return $this->redirectToRoute('app_actividad_edit', ['id' => $actividad->getId()]);
                    //return $this->redirectToRoute('app_actividad_new');
                } else {
                    $this->addFlash('error', $error);
                }
            }
        }

        //Generar HTML de preguntas enviadas por Post
        $preguntatemplate = str_replace(["\n", "\t", "\r"], '', $this->renderView('actividad/tipo/cuestionario/pregunta.html.twig', ['view' => $view]));
        $respuestatemplate = str_replace(["\n", "\t", "\r"], '', $this->renderView('actividad/tipo/cuestionario/respuesta.html.twig', ['view' => $view]));
        $detalleshtml = $this->generarPreguntasHtml($detalles, $preguntatemplate, $respuestatemplate);

        //Respuesta
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('actividad/new.html.twig', [
            'form' => $form->createView(),
            'tipo' => Actividad::TIPO_CUESTIONARIO,
            'view' => $view,
            'respuestatemplate' => $respuestatemplate,
            'preguntatemplate' => $preguntatemplate,
            'detalleshtml' => $detalleshtml,
            'nuevo' => empty($detalleshtml) ? 1 : 0,
            'nocache' => !$view,
            'detalles_eliminar' => '',
        ], $response);
    }

    /*
     * Genera el HTML del formulario de preguntas y respuestas en base a
     * los datos del array $detalles y los templates
     */

    private function generarParejasHtml(?array $detalles, string $parejatemplate)
    {
        $detalleshtml = '';
        $parejahtml = '';

        if ($detalles) {
            $i = 1;
            foreach ($detalles['parejas'] as $pid => $pareja) {

                //Obtener texto e ide de cada concepto de la pareja
                $rid = null;
                $rtext = '';
                $ptext = '';
                foreach ($pareja as $id => $texto) {
                    if ($pid != $id) {
                        $rid = $id;
                        $rtext = $texto;
                    } else {
                        $ptext = $texto;
                    }
                }

                $parejahtml = str_replace(
                    ['%_pid_%', '%_rid_%', '%_ptext_%', '%_rtext_%'],
                    [$pid, $rid, htmlentities($ptext), htmlentities($rtext)],
                    '<div>' . $parejatemplate . '</div>'
                );
                $i++;

                $detalleshtml .= $parejahtml;
            }
        }
        return $detalleshtml;
    }

    /*
     * Genera el HTML del formulario de preguntas y respuestas en base a
     * los datos del array $detalles y los templates
     */

    private function generarPreguntasHtml(?array $detalles, string $preguntatemplate, string $respuestatemplate)
    {
        $detalleshtml = '';
        $preguntahtml = '';
        $respuestahtml = '';

        if ($detalles) {
            $i = 1;
            foreach ($detalles['preguntas'] as $k => $preg) {
                $preguntahtml = str_replace(
                    ['%_pid_%', '%_pnum_%', '%_ptext_%'],
                    [$k, $i, htmlentities($preg)],
                    '<div>' . $preguntatemplate . '</div>'
                );
                $i++;

                $respuestahtml = '';
                foreach ($detalles['respuestas'][$k] as $kk => $resp) {
                    $respuestahtml .= str_replace(
                        ['%_pid_%', '%_rid_%', '%_rtext_%', '%_rcorr_%'],
                        [$k, $kk, htmlentities($resp['texto']), (isset($resp['correcta']) ? ' checked="checked"' : '')],
                        '<div>' . $respuestatemplate . '</div>'
                    );
                }

                $detalleshtml .= str_replace(
                    '%_resp_%',
                    $respuestahtml,
                    $preguntahtml
                );
            }
        }
        return $detalleshtml;
    }

    /**
     * Guarda en la DB la actividad y elimina parejas de ser necesario.
     * Recibe la actividad y el array $detalles
     * @param  mixed $actividad
     * @param  mixed $detalles
     * @return void
     */
    private function guardarRelacionarConceptos(Actividad $actividad, ?array $detalles)
    {
        //TODO: Modificar para guardar conceptos (copiada de cuestionario)
        $error = '';
        $ids_guardados = [];
        $this->em->getConnection()->beginTransaction(); // suspend auto-commit
        $da = $actividad->getDetallesactividad();

        try {
            //$actividad->setUsuario($this->getUser());
            $this->em->persist($actividad);
            $this->em->flush();

            if (!$detalles || !isset($detalles['parejas']) || count($detalles['parejas']) < 1) {
                $error = 'No se recibieron parejas para guardar.';
                throw new \Exception();
            }



            //Parejas
            foreach ($detalles['parejas'] as $pid => $pareja) {

                dump($pareja);

                //Obtener texto e id de cada concepto de la pareja
                $rid = null;
                $rtext = '';
                $ptext = '';
                foreach ($pareja as $id => $texto) {
                    if ($pid != $id) {
                        $rid = $id;
                        $rtext = $texto;
                    } else {
                        $ptext = $texto;
                    }
                }

                if (strlen($ptext) < 1 || strlen($rtext) < 1) {
                    $error = 'Los conceptos no pueden estar vacíos.';
                    throw new \Exception();
                }

                //dump($pid, $ptext, $rid, $rtext);

                //Si es nueva pareja
                if ($pid < 0) {

                    //Guardar concepto A
                    $detalle_concepto_a = new DetalleActividad(
                        null,
                        $ptext,
                        DetalleActividad::TIPO_RELACIONAR_CONCEPTOS_A,
                        null,
                        null,
                        $actividad
                    );
                    $this->em->persist($detalle_concepto_a);
                    $this->em->flush();

                    //Guardar concepto B
                    $detalle_concepto_b = new DetalleActividad(
                        null,
                        $rtext,
                        DetalleActividad::TIPO_RELACIONAR_CONCEPTOS_B,
                        null,
                        null,
                        $actividad
                    );
                    $this->em->persist($detalle_concepto_b);
                    $this->em->flush();

                    //Agregar relación entre los dos
                    $detalle_concepto_a->setRelacion($detalle_concepto_b->getId());
                    $detalle_concepto_b->setRelacion($detalle_concepto_a->getId());
                    $this->em->flush();
                } else {
                    //Si estamos modificando

                    //TODO: esto funciona acá pero no en eliminar... tener cuidado
                    //sino usar bucle y listo

                    //A
                    $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('id', $pid));
                    $cp = $da->matching($criteria);
                    $detalle_concepto = $cp->first();
                    //Solo modifico el texto
                    $detalle_concepto->setDato($ptext);

                    //B
                    $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('id', $rid));
                    $cp = $da->matching($criteria);
                    $detalle_concepto = $cp->first();
                    //Solo modifico el texto
                    $detalle_concepto->setDato($rtext);

                    $this->em->flush();
                }

                $ids_guardados[] = $pid;
                $ids_guardados[] = $rid;
            }

            //dd($detalles);

            //Eliminar si hay algo para eliminar
            //Solo elimino preguntas / respuestas que no se pasaron por post
            $detalles['eliminar'] = isset($detalles['eliminar']) ? $detalles['eliminar'] : '';
            if ($detalles['eliminar'] != '') {
                $eliminara = explode('|', ltrim($detalles['eliminar'], '|'));

                foreach ($eliminara as $elid) {
                    $eldi = intval($elid);
                    if (!in_array($elid, $ids_guardados)) {

                        //TODO: No funciona matching en este punto? porque?
                        foreach ($da as $det) {
                            if ($det->getId() == $elid) {
                                $actividad->removeDetallesactividad($det);
                            }
                        }
                    }
                }
                $this->em->flush();
            }

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {

            $this->em->getConnection()->rollBack();
            if ($error == '') {
                $error = 'Error al guardar en la base de datos. '; //. $e->getMessage();
            }
        }

        return $error;
    }

    /**
     * Guarda en la DB el cuestionario y elimina preguntas y respuestas de ser necesario.
     * Recibe la actividad y el array $detalles
     * @param  mixed $actividad
     * @param  mixed $detalles
     * @return void
     */
    private function guardarCuestionario(Actividad $actividad, ?array $detalles)
    {

        $error = '';
        $ids_guardados = [];
        $this->em->getConnection()->beginTransaction(); // suspend auto-commit
        $da = $actividad->getDetallesactividad();

        try {
            //$actividad->setUsuario($this->getUser());
            $this->em->persist($actividad);
            $this->em->flush();

            if (!$detalles || !isset($detalles['preguntas']) || count($detalles['preguntas']) < 1) {
                $error = 'No se recibieron preguntas para guardar.';
                throw new \Exception();
            }

            //Pregunta
            foreach ($detalles['preguntas'] as $k => $preg) {

                if (strlen($preg) < 1) {
                    $error = 'Las preguntas no pueden estar vacías.';
                    throw new \Exception();
                }

                //Si es nueva pregunta
                if ($k < 0) {
                    $detalle_concepto = new DetalleActividad(
                        null,
                        $preg,
                        DetalleActividad::TIPO_CUESTIONARIO_PREGUNTA,
                        null,
                        null,
                        $actividad
                    );
                    $this->em->persist($detalle_concepto);
                    $this->em->flush();
                    $relacion = $detalle_concepto->getId();
                    $detalle_concepto->setRelacion($detalle_concepto->getId($relacion));
                    $this->em->flush();
                } else {
                    //Si estamos modificando
                    //$da = new ArrayCollection();

                    //TODO: esto funciona acá pero no en eliminar... tener cuidado
                    //sino usar bucle y listo
                    $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('id', $k));
                    $cp = $da->matching($criteria);
                    $detalle_concepto = $cp->first();


                    //Solo modifico el texto de la pregunta
                    $detalle_concepto->setDato($preg);

                    $this->em->flush();
                    $relacion = $detalle_concepto->getId();
                }
                //guardo el id de la pregunta
                $ids_guardados[] = $relacion;


                //Respuestas de esta pregunta
                if (!isset($detalles['respuestas'][$k]) || count($detalles['respuestas'][$k]) < 2) {
                    $error = 'No se recibieron respuestas para guardar.';
                    throw new \Exception();
                }

                $correctos = 0;
                foreach ($detalles['respuestas'][$k] as $kk => $resp) {

                    if (strlen($resp['texto']) < 1) {
                        $error = 'Las respuestas no pueden estar vacías.';
                        throw new \Exception();
                    }

                    $correcto = isset($resp['correcta']);
                    if ($correcto) {
                        $correctos++;
                    }

                    //Nueva respuesta
                    if ($kk < 0) {
                        $detallerespuesta = new DetalleActividad(
                            null,
                            $resp['texto'],
                            DetalleActividad::TIPO_CUESTIONARIO_RESPUESTA,
                            $relacion,
                            $correcto,
                            $actividad
                        );
                        $this->em->persist($detallerespuesta);
                    } else {
                        //Modificamos respuesta
                        //$cp = new ArrayCollection();

                        $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('id', $kk));
                        $cp = $da->matching($criteria);
                        $detallerespuesta = $cp->first();
                        $detallerespuesta->setDato($resp['texto']);
                        $detallerespuesta->setCorrecto($correcto);
                    }

                    $this->em->flush();

                    //guardo el id de la respuesta
                    $ids_guardados[] = $detallerespuesta->getId();
                }

                if ($correctos < 1) {
                    $error = 'Cada pregunta debe tener al menos una respuesta correcta.';
                    throw new \Exception();
                } elseif ($correctos >= count($detalles['respuestas'][$k])) {
                    $error = 'Todas las respuestas de una pregunta no pueden ser correctas.';
                    throw new \Exception();
                }
            }

            //Eliminar si hay algo para eliminar
            //Solo elimino preguntas / respuestas que no se pasaron por post
            $detalles['eliminar'] = isset($detalles['eliminar']) ? $detalles['eliminar'] : '';
            if ($detalles['eliminar'] != '') {
                $eliminara = explode('|', ltrim($detalles['eliminar'], '|'));

                foreach ($eliminara as $elid) {
                    $eldi = intval($elid);
                    if (!in_array($elid, $ids_guardados)) {

                        //TODO: No funciona matching en este punto? porque?
                        foreach ($da as $det) {
                            if ($det->getId() == $elid || $det->getRelacion() == $elid) {
                                $actividad->removeDetallesactividad($det);
                            }
                        }
                    }
                }
                $this->em->flush();
            }

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {

            $this->em->getConnection()->rollBack();
            if ($error == '') {
                $error = 'Error al guardar en la base de datos. '; //. $e->getMessage();
            }
        }

        return $error;
    }

    /*
     * Toma los datos de la db y les dá el formato de Post para unificar
     * $detalles[
     * 'preguntas' => [
     *      pid => 'texto pregunta'
     * ]
     * 'respuestas' => [
     *      pid => [
     *          rid => [
     *              'texto' => 'texto respuesta',
     *              'correcta' => 'si' // se define solo si es correcta
     *          ]
     *      ]
     *  ]
     * ]
     * 
     */

    private function formatearPreguntasDB(Actividad $actividad)
    {
        $det = $actividad->getDetallesactividad();
        $d = new DetalleActividad();
        $ad = [];
        foreach ($det as $d) {
            if ($d->getTipo() == DetalleActividad::TIPO_CUESTIONARIO_PREGUNTA) {
                $ad['preguntas'][$d->getId()] = $d->getDato();
            } elseif ($d->getTipo() == DetalleActividad::TIPO_CUESTIONARIO_RESPUESTA) {
                $ad['respuestas'][$d->getRelacion()][$d->getId()]['texto'] = $d->getDato();
                if ($d->isCorrecto() === true) {
                    $ad['respuestas'][$d->getRelacion()][$d->getId()]['correcta'] = 'si';
                }
            }
        }

        return $ad;
    }

    /*
     * Toma los datos de la db y les dá el formato de Post para unificar
     * $detalles[
        * 'parejas' => [
        *      pid => 'texto concepto a'
        *      rid => 'texto concepto b
        * ]
     * ]
     * 
     */

    private function formatearParejasDB(Actividad $actividad)
    {
        $det = $actividad->getDetallesactividad();
        $d = new DetalleActividad();
        $ad = [];
        foreach ($det as $d) {
            if ($d->getTipo() == DetalleActividad::TIPO_RELACIONAR_CONCEPTOS_A) {
                $ad['parejas'][$d->getId()][$d->getId()]  = $d->getDato();
            } elseif ($d->getTipo() == DetalleActividad::TIPO_RELACIONAR_CONCEPTOS_B) {
                $ad['parejas'][$d->getRelacion()][$d->getId()] = $d->getDato();
            }
        }

        return $ad;
    }
}
