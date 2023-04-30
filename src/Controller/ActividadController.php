<?php

namespace App\Controller;

use function dump;
use App\Entity\Actividad;
use Pagerfanta\Pagerfanta;
use App\Form\ActividadType;
use App\Entity\DetalleActividad;
use App\Repository\ActividadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\Form\FormInterface;
use App\Services\ContainerParametersHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
    public function new(Request $request, ContainerParametersHelper $pathHelpers, SluggerInterface $slugger): Response
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
                return $this->gestionarRelacionarConceptos($form, $request, $detalles, $actividad, $pathHelpers, $slugger, 'new');
                break;
            case Actividad::TIPO_CUESTIONARIO:
                return $this->gestionarCuestionario($form, $request, $detalles, $actividad, 'new');
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
    public function edit(int $id, Request $request, ContainerParametersHelper $pathHelpers, SluggerInterface $slugger): Response
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
            'tipo' => $tipo,
            'view' => false,
        ]);

        //Seleccionar tipo de actividad y gestionar
        switch ($tipo) {
            case Actividad::TIPO_RELACIONAR_CONCEPTOS:
                //Agregar a los detalles de POST los detalles de la DB      
                $detallesdb = $this->formatearParejasDB($actividad);
                $detalles = (is_null($detalles) ? $detallesdb : array_merge($detallesdb, $detalles));

                return $this->gestionarRelacionarConceptos($form, $request, $detalles, $actividad, $pathHelpers, $slugger, 'modify');
                break;

            case Actividad::TIPO_CUESTIONARIO:
                //Agregar a los detalles de POST los detalles de la DB      
                $detallesdb = $this->formatearPreguntasDB($actividad);
                $detalles = (is_null($detalles) ? $detallesdb : array_merge($detallesdb, $detalles));

                return $this->gestionarCuestionario($form, $request, $detalles, $actividad, 'modify');
                break;
        }
    }

    #[Route('/actividad/ver/{id}', name: 'app_actividad_view')]
    public function view(int $id, ContainerParametersHelper $pathHelpers, SluggerInterface $slugger): Response
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
                return $this->gestionarRelacionarConceptos($form, null, $detalles, $actividad, $pathHelpers, $slugger, 'view');
                break;
            case Actividad::TIPO_CUESTIONARIO:

                //Detalles de la base de datos      
                $detalles = $this->formatearPreguntasDB($actividad);
                return $this->gestionarCuestionario($form, null, $detalles, $actividad, 'view');
                break;
        }
    }

    #[Route('/actividad/eliminar/{id}', name: 'app_actividad_delete', methods: ['GET', 'HEAD'])]
    public function delete(int $id, ContainerParametersHelper $pathHelpers, SluggerInterface $slugger): Response
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
                return $this->gestionarRelacionarConceptos($form, null, $detalles, $actividad, $pathHelpers, $slugger, 'delete');
                break;
            case Actividad::TIPO_CUESTIONARIO:

                //Detalles de la base de datos      
                $detalles = $this->formatearPreguntasDB($actividad);
                return $this->gestionarCuestionario($form, null, $detalles, $actividad, 'delete');
                break;
        }

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

    //////////////////////////////////////////////////
    // 
    // Gestionar formularios para cada tipo de actividad
    //
    //////////////////////////////////////////////////

    /**
     * Gestiona la creación de una nueva actividad cuando el tipo es Relacionar Conceptos
     *
     * @return Response respuesta
     */
    private function gestionarRelacionarConceptos(
        FormInterface $form,
        ?Request $request,
        ?array $detalles,
        Actividad $actividad,
        ContainerParametersHelper $pathHelpers,
        SluggerInterface $slugger,
        string $mode = 'new' //modify,view, delete
    ): Response {
        $view = $modify = $delete = false;
        switch ($mode) {
            case 'view':
                $view = true;
                break;
            case 'modify':
                $modify = true;
                break;
            case 'delete':
                $delete = true;
                $view = true;
                break;
        }


        if (!$view) {
            $form->handleRequest($request);
            //Si se enviaron los datos correctos al form y se hizo clic en Guardar...
            //Guardar la nueva actividad y terminar

            if ($form->isSubmitted() && $form->isValid() && ($form->get('guardar')->isClicked() || $modify)) {
                $error = $this->guardarRelacionarConceptos($actividad, $detalles, $request, $pathHelpers, $slugger);
                if ($error == '') {
                    $this->addFlash('success', 'Se ' . ($modify ? 'modificó' : 'guardó') . ' la actividad correctamente.');
                    return $this->redirectToRoute('app_actividad_edit', ['id' => $actividad->getId()]);
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
        return $this->render('actividad/' . ($delete ? 'delete' : 'new') . '.html.twig', [
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
    private function gestionarCuestionario(
        FormInterface $form,
        ?Request $request,
        ?array $detalles,
        Actividad $actividad,
        string $mode
    ): Response {

        $view = $modify = $delete = false;
        switch ($mode) {
            case 'view':
                $view = true;
                break;
            case 'modify':
                $modify = true;
                break;
            case 'delete':
                $delete = true;
                $view = true;
                break;
        }

        if (!$view) {
            //Si se enviaron los datos correctos al form y se hizo clic en Guardar...
            //Guardar la nueva actividad y terminar
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid() && ($form->get('guardar')->isClicked() && $modify)) {
                $error = $this->guardarCuestionario($actividad, $detalles);
                if ($error == '') {
                    $this->addFlash('success', 'Se ' . ($modify ? 'guardó' : 'modificó') . ' la actividad correctamente.');
                    return $this->redirectToRoute('app_actividad_edit', ['id' => $actividad->getId()]);
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
        return $this->render('actividad/' . ($delete ? 'delete' : 'new') . '.html.twig', [
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


    //////////////////////////////////////////////////
    // 
    // Generar HTML de form. de cada tipo de actividad
    //
    //////////////////////////////////////////////////

    /*
     * Genera el HTML del formulario de relacionar conceptos en base a
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
                        $rtextdisabled = $detalles['tipo'][$pid][$rid] == 1 ? 'disabled' : '';
                        $rimgdisabled  = $detalles['tipo'][$pid][$rid] == 0 ? 'disabled' : '';
                        $rtextcheck    = $detalles['tipo'][$pid][$rid] == 0 ? 'checked' : '';
                        $rimgcheck     = $detalles['tipo'][$pid][$rid] == 1 ? 'checked' : '';
                        $rtexthidden   = $detalles['tipo'][$pid][$rid] == 1 ? 'd-none' : '';
                        $rimghidden    = $detalles['tipo'][$pid][$rid] == 0 ? 'd-none' : '';
                        $rfilename     = $detalles['tipo'][$pid][$rid] == 1 ? $texto : '';

                    } else {
                        $ptext = $texto;
                        $ptextdisabled = $detalles['tipo'][$pid][$pid] == 1 ? 'disabled' : '';
                        $pimgdisabled  = $detalles['tipo'][$pid][$pid] == 0 ? 'disabled' : '';
                        $ptextcheck    = $detalles['tipo'][$pid][$pid] == 0 ? 'checked' : '';
                        $pimgcheck     = $detalles['tipo'][$pid][$pid] == 1 ? 'checked' : '';
                        $ptexthidden   = $detalles['tipo'][$pid][$pid] == 1 ? 'd-none' : '';
                        $pimghidden    = $detalles['tipo'][$pid][$pid] == 0 ? 'd-none' : '';
                        $pfilename     = $detalles['tipo'][$pid][$pid] == 1 ? $texto : '';
                    }
                }

                $parejahtml = str_replace(
                    [
                        '%_pid_%',
                        '%_rid_%',
                        '%_ptext_%',
                        '%_rtext_%',

                        '%_ptextdisabled_%',
                        '%_pimgdisabled_%',
                        '%_rtextdisabled_%',
                        '%_rimgdisabled_%',

                        '%_ptextcheck_%',
                        '%_pimgcheck_%',
                        '%_rtextcheck_%',
                        '%_rimgcheck_%',

                        '%_ptexthidden_%',
                        '%_pimghidden_%',
                        '%_rtexthidden_%',
                        '%_rimghidden_%',

                        '%_pfilename_%',
                        '%_rfilename_%'

                    ],
                    [
                        $pid,
                        $rid,
                        htmlentities($ptext),
                        htmlentities($rtext),

                        $ptextdisabled,
                        $pimgdisabled,
                        $rtextdisabled,
                        $rimgdisabled,

                        $ptextcheck,
                        $pimgcheck,
                        $rtextcheck,
                        $rimgcheck,

                        $ptexthidden,
                        $pimghidden,
                        $rtexthidden,
                        $rimghidden,

                        $pfilename,
                        $rfilename
                    ],
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

    //////////////////////////////////////////////////
    // 
    // Guardar datos de actividades en DB
    //
    //////////////////////////////////////////////////

    /**
     * Guarda en la DB la actividad y elimina parejas de ser necesario.
     * Recibe la actividad y el array $detalles
     * @param  mixed $actividad
     * @param  mixed $detalles
     * @return void
     */
    private function guardarRelacionarConceptos(Actividad $actividad, ?array $detalles, Request $request, ContainerParametersHelper $pathHelpers, SluggerInterface $slugger)
    {

        $error = '';
        $ids_guardados = [];
        $this->em->getConnection()->beginTransaction(); // suspend auto-commit
        $da = $actividad->getDetallesactividad();

        try {
            //$actividad->setUsuario($this->getUser());
            $this->em->persist($actividad);
            $this->em->flush();
            
            // dd($detalles,$request->files->all());

            if (!$detalles || !isset($detalles['parejas']) || count($detalles['parejas']) < 1) {
                $error = 'No se recibieron parejas para guardar.';
                throw new \Exception();
            }

            //Parejas
            foreach ($detalles['parejas'] as $pid => $pareja) {

                //Obtener texto e id de cada concepto de la pareja
                $rid = null;
                $ptext = '';
                $rtext = '';
                $ptype = '';
                $rtype = '';
                foreach ($pareja as $id => $texto) {
                    if ($pid != $id) {
                        $rid = $id;
                        $rtext = $texto;
                        $rtype = $detalles['tipo'][$pid][$rid];
                    } else {
                        $ptext = $texto;
                        $ptype = $detalles['tipo'][$pid][$pid];
                    }
                }

                //Guardar imagenes
                if($ptype == 1){
                    $pimg = $request->files->all()['detalle']['imagenes'][$pid][$pid] ?? $request->files->all()['detalle']['imagenes'][$pid][$pid];
                    if ($pimg) {
                        $originalFilename = pathinfo($pimg->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$pimg->guessExtension();

                        // Move the file to the directory
                        try {
                            $pimg->move(
                                $pathHelpers->getPublicPath().'/uploads/relacionar',
                                $newFilename
                            );

                            $ptext = $newFilename;
                        } catch (FileException $e) {
                            $error = 'Error al subir archivo.';
                            throw new \Exception();     
                        }
                    }     
                }
                if($rtype == 1){
                    $rimg = $request->files->all()['detalle']['imagenes'][$pid][$rid] ?? $request->files->all()['detalle']['imagenes'][$pid][$rid];
                    if ($rimg) {
                        $originalFilename = pathinfo($rimg->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$rimg->guessExtension();

                        // Move the file to the directory
                        try {
                            $rimg->move(
                                $pathHelpers->getPublicPath().'/uploads/relacionar',
                                $newFilename
                            );

                            $rtext = $newFilename;
                        } catch (FileException $e) {
                            $error = 'Error al subir archivo.';
                            throw new \Exception();
                        }
                    }
                }

                //Validar conceptos y archivos (el nombre esta vacío si no se subio)
                if (strlen($ptext) < 1 || strlen($rtext) < 1) {
                    $error = 'Los conceptos no pueden estar vacíos.'.$ptext.'-'.$rtext;
                    throw new \Exception();
                }

                //Si es nueva pareja
                if ($pid < 0) {

                    //Guardar concepto A
                    $detalle_concepto_a = new DetalleActividad(
                        null,
                        $ptext,
                        DetalleActividad::TIPO_RELACIONAR_CONCEPTOS_A,
                        null,
                        $ptype,
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
                        $rtype,
                        $actividad
                    );
                    $this->em->persist($detalle_concepto_b);
                    $this->em->flush();

                    //Agregar relación entre los dos
                    $detalle_concepto_a->setRelacion($detalle_concepto_a->getId());
                    $detalle_concepto_b->setRelacion($detalle_concepto_a->getId());

                    $this->em->flush();
                } else {
                    //Si estamos modificando

                    //A
                    $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('id', $pid));
                    $cp = $da->matching($criteria);
                    $detalle_concepto = $cp->first();
                    $detalle_concepto->setDato($ptext);
                    $detalle_concepto->setCorrecto($ptype);

                    //B
                    $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('id', $rid));
                    $cp = $da->matching($criteria);
                    $detalle_concepto = $cp->first();
                    $detalle_concepto->setDato($rtext);
                    $detalle_concepto->setCorrecto($rtype);

                    $this->em->flush();
                }

                $ids_guardados[] = $pid;
                $ids_guardados[] = $rid;
            }

            //Eliminar si hay algo para eliminar
            //Solo elimino preguntas / respuestas que no se pasaron por post
            $detalles['eliminar'] = isset($detalles['eliminar']) ? $detalles['eliminar'] : '';
            if ($detalles['eliminar'] != '') {
                $eliminara = explode('|', ltrim($detalles['eliminar'], '|'));

                foreach ($eliminara as $elid) {
                    $eldi = intval($elid);
                    if (!in_array($elid, $ids_guardados)) {

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

    //////////////////////////////////////////////////
    // 
    // Formatear datos de la base de datos
    //
    //////////////////////////////////////////////////

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
                $ad['tipo'][$d->getId()][$d->getId()]  = $d->isCorrecto() ? 1 : 0;
            } elseif ($d->getTipo() == DetalleActividad::TIPO_RELACIONAR_CONCEPTOS_B) {
                $ad['parejas'][$d->getRelacion()][$d->getId()] = $d->getDato();
                $ad['tipo'][$d->getRelacion()][$d->getId()]  = $d->isCorrecto() ? 1 : 0;
            }
        }

        return $ad;
    }
}
