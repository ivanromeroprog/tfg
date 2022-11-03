<?php

namespace App\Controller;

use App\Entity\Actividad;
use App\Entity\DetalleActividad;
use App\Entity\DetallePresentacionActividad;
use App\Entity\Interaccion;
use App\Entity\PresentacionActividad;
use App\Form\PresentacionActividadType;
use App\Repository\PresentacionActividadRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[IsGranted('ROLE_DOCENTE')]
class PresentacionActividadController extends AbstractController {

    private EntityManagerInterface $em;
    private PresentacionActividadRepository $cr;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->cr = $this->em->getRepository(PresentacionActividad::class);
    }

    #[Route('/presentacion/actividad', name: 'app_presentacion_actividad')]
    public function index(Request $request): Response {

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
            'estado' => $search
                ] : [],
                $order,
                $this->getUser()
        );

        $pager = new Pagerfanta(new QueryAdapter($listqb));
        $pager->setMaxPerPage($perpage);
        $pager->setCurrentPage($page);

        return $this->render('presentacion_actividad/index.html.twig', [
                    'pager' => $pager,
                    'order' => $order,
                    'search' => $search,
                    'perpageoptions' => [
                        10, 25, 50, 100
                    ]
        ]);
    }

    #[Route('/presentacion/actividad/nuevo', name: 'app_presentacion_actividad_new')]
    public function new(Request $request): Response {
        $presactividad = new PresentacionActividad();
        $presactividad->setFecha(new DateTime());

        $form = $this->createForm(PresentacionActividadType::class, $presactividad, ['usuario' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->getConnection()->beginTransaction();
            //$actividad = new Actividad();
            $actividad = $form->get('actividad')->getData();
            $error = '';
            try {
                //Hago una copia de la actividad a la presentacion de actividad
                $presactividad->setUsuario($this->getUser());
                $presactividad->setEstado(PresentacionActividad::ESTADO_INICIADO);
                $presactividad->setTitulo($actividad->getTitulo());
                $presactividad->setDescripcion($actividad->getDescripcion());
                $presactividad->setTipo($actividad->getTipo());

                //Recupero los detalles actividad y hago una copia de cada uno
                // en el detalle presentacion actividad
                $detalleactividades = $actividad->getDetallesactividad();
                $alumnos = $form->get('curso')->getData()->getAlumnos();
                $primerdpa = true;
                foreach ($detalleactividades as $da) {
                    $detpresact = new DetallePresentacionActividad(
                            null,
                            $da->getDato(),
                            $da->getTipo(),
                            $da->getRelacion(),
                            $da->isCorrecto(),
                            null
                    );
                    $this->em->persist($detpresact);

                    //Agrego una interaccion por cada alumno para que quede fija
                    //la lista de alumnos al momento de la presentación de la actividad
                    //(los alumnos pueden ser eliminados/agregados al curso con el tiempo)
                    if ($primerdpa) {
                        $primerdpa = false;
                        foreach ($alumnos as $alumno) {
                            $interaccion = new Interaccion(null, $alumno);
                            $this->em->persist($interaccion);
                            $detpresact->addInteraccion($interaccion);
                        }
                    }

                    $presactividad->addDetallesPresentacionActividad($detpresact);
                }

                $this->em->persist($presactividad);
                $this->em->flush();

                $this->em->getConnection()->commit();
            } catch (Exception $e) {
                //TODO: quitar detalle de error en DB
                $this->em->getConnection()->rollBack();
                if ($error == '') {
                    $error = 'Error al guardar en la base de datos. ' . $e->getMessage();
                }
            }
            if ($error == '') {
                //$this->addFlash('success', 'Se guardó la actividad correctamente.');
                return $this->redirectToRoute('app_presentacion_actividad_edit', ['id' => $presactividad->getId(), 'modal' => 'true']);
                //return $this->redirectToRoute('app_actividad_new');
            } else {
                $this->addFlash('error', $error);
            }
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('presentacion_actividad/new.html.twig', [
                    'form' => $form->createView(),
                    'nocache' => true
                        ], $response);
    }

    #[Route('/presentacion/actividad/panel/{id}/{modal}/{pregunta}', name: 'app_presentacion_actividad_edit')] ///{idasistencia}/{presente} //, int $idasistencia = 0, ?bool $presente = null 
    public function edit(int $id, Request $request, string $modal = 'false', string $pregunta = ''): Response {
        /*
          Preparar valores necesarios
         */
        //Toma de asistencia
        //TODO: reemplazar con PresentacionActividadRepository::findWithDetails
        $presentacion_actividad = $this->cr->findWithDetails($id);
        if (is_null($presentacion_actividad) || $presentacion_actividad->getCurso()->getUsuario() != $this->getUser())
            throw new AccessDeniedHttpException();

        //Url para compartir la toma de asistencia
        $code = $presentacion_actividad->getUrlEncoded();
        $url = $this->generateUrl(
                'app_asistencia_alumno', //TODO: reemplazar con actividad_alumno
                ['code' => $code],
                UrlGeneratorInterface::ABSOLUTE_URL
        );

        //Lista de detalles de la presentación de actividad (preguntas, conceptos a relacionar, etc.)
        $lista_detalles_actividad = $presentacion_actividad->getDetallesPresentacionActividad();

        //Crear lista de detalles que debemos listar dependiendo del tipo de actividad
        $lista_detalles_actividad_mostrar = [];

        if ($presentacion_actividad->getTipo() == Actividad::TIPO_CUESTIONARIO) {
            foreach ($lista_detalles_actividad as $v) {
                if ($v->getTipo() == DetalleActividad::TIPO_CUESTIONARIO_PREGUNTA) {
                    $lista_detalles_actividad_mostrar[] = $v;
                }
            }
        }

        //Lista de alumnos, la tomo de la lista de interacciones para asi registrar alumnso que fueron eliminados del curso
        $lista_alumnos = [];

        $encontreinter = false;
        foreach ($lista_detalles_actividad as $detalle) {

            foreach ($detalle->getInteracciones() as $interaccion) {
                $lista_alumnos[] = $interaccion->getAlumno();
                $encontreinter = true;
            }
            if ($encontreinter)
                break;
        }


        //Pregunta para el usuario, filtrar para solo aceptar los valores válidos
        $pregunta = (in_array($pregunta, ['anular', 'iniciar', 'finalizar']) ? $pregunta : 'f');

        /*
          Formulario
         */
        $form = $this->createForm(PresentacionActividadType::class, $presentacion_actividad, [
            'usuario' => $this->getUser(),
            'modify' => true,
            'pregunta' => $pregunta
        ]);
        $form->handleRequest($request);

        //Se envio el form desde alguno de los botones de submit
        if ($form->isSubmitted() && $form->isValid()) {

            //Determinar que boton se uso, se usan los valores de 
            //PresentacionActividad::ESTADOS como nombres de los botones
            $estado = '';
            foreach (PresentacionActividad::ESTADOS as $e) {
                if ($form->has($e) && $form->get($e)->isClicked()) {
                    $estado = $e;
                }
            }

            if ($estado != '') {
                $presentacion_actividad->setEstado($estado);
                $this->em->persist($presentacion_actividad);
                $this->em->flush();

                $this->addFlash('success', 'Se ' .
                        ($estado == PresentacionActividad::ESTADO_ANULADO ? 'Anuló' : ($estado == PresentacionActividad::ESTADO_INICIADO ? 'Inició' : ($estado == PresentacionActividad::ESTADO_FINALIZADO ? 'Finalizó' :
                                                '')))
                        . ' la presentación de actividad correctamente.');
                return $this->redirectToRoute(
                                'app_presentacion_actividad_edit',
                                [
                                    'id' => $presentacion_actividad->getId(),
                                    'modal' => ($estado == PresentacionActividad::ESTADO_INICIADO ? 'true' : 'f')
                                ]
                );
            }
        } /* elseif ($idasistencia > 0) {
          //Turboframes: No se envio form y Docente cambia el estado de asistencia de un alumno
          $asistencia = $this->ar->find($idasistencia);
          if (is_null($asistencia) || !($asistencia->getPresentacionActividad() === $tomaasis) || is_null($presente)) {
          throw new AccessDeniedHttpException();
          } else {
          $asistencia->setPresente($presente);
          $this->em->flush();

          //Solo renderizar el frameasistencia si usamos turboframe
          if ($request->headers->get('Turbo-Frame')) {
          //dump($request->headers->get('Turbo-Frame'));
          return $this->render('asistencia/frameasistencia.html.twig', [
          'pregunta' => $pregunta,
          'tomaasis' => $tomaasis,
          'asistencia' => $asistencia,
          ]);
          }
          }
          } */


        //Renderizar página normalmente
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('presentacion_actividad/edit.html.twig', [
                    'form' => $form->createView(),
                    'url' => $url,
                    'modal' => $modal === 'true',
                    'lista_detalles_actividad_mostrar' => $lista_detalles_actividad_mostrar,
                    'lista_alumnos' => $lista_alumnos,
                    'pregunta' => $pregunta,
                    'presentacion_actividad' => $presentacion_actividad,
                        ], $response);
    }

    /*
      #[Route('/presentacion/actividad/panel/{id}', name: 'app_presentacion_actividad_edit')]
      public function edit(int $id, Request $request): Response {
      $presentacion_actividad = $this->cr->find($id);

      if (is_null($presentacion_actividad))
      throw new AccessDeniedHttpException();

      $form = $this->createForm(PresentacionActividadType::class, $presentacion_actividad, [
      'modify' => true,
      'usuario' => $this->getUser(),
      'organizacion' => $presentacion_actividad->getOrganizacion()
      ]);
      $form->handleRequest($request);

      //TODO: Validar que el CUA no se repita en la organización
      if ($form->isSubmitted() && $form->isValid()) {
      if ($form->get('alumno_agregar')->isClicked()) {
      $data = $request->request->all()['presentacion_actividad'];

      if (
      strlen($data['alumno_nombre']) < 2 || strlen($data['alumno_apellido']) < 2 || strlen($data['alumno_cua']) < 2
      ) {
      $this->addFlash('warning', 'Completa todos los datos del alumno.');
      } else {

      $alumno = new Alumno(
      null,
      $data['alumno_nombre'],
      $data['alumno_apellido'],
      $data['alumno_cua']
      );

      $this->em->persist($alumno);
      $presentacion_actividad->addAlumno($alumno);

      $this->addFlash('success-alumnos', 'Se agregó el alumno correctamente.');
      }

      $this->em->persist($presentacion_actividad);
      $this->em->flush();
      return $this->redirect($request->getUri());
      } else {

      $this->em->persist($presentacion_actividad);
      $this->em->flush();
      $this->addFlash('success', 'Se edito el presentacion_actividad correctamente.');
      return $this->redirect($request->getUri());
      }
      }

      return $this->render('presentacion_actividad/edit.html.twig', [
      'form' => $form->createView()
      ]);
      }

      #[Route('/presentacion/actividad/ver/{id}', name: 'app_presentacion_actividad_view')]
      public function view(int $id): Response {
      if ($id < 1)
      throw new AccessDeniedHttpException();

      $presentacion_actividad = $this->cr->find($id);

      if (is_null($presentacion_actividad))
      throw new AccessDeniedHttpException();

      $form = $this->createForm(PresentacionActividadType::class, $presentacion_actividad, [
      'view' => true,
      'usuario' => $this->getUser(),
      'organizacion' => $presentacion_actividad->getOrganizacion()
      ]);

      return $this->render('presentacion_actividad/new.html.twig', [
      'form' => $form->createView(),
      ]);
      }

      #[Route('/presentacion/actividad/eliminar/{id}', name: 'app_presentacion_actividad_delete', methods: ['GET', 'HEAD'])]
      public function delete(int $id): Response {
      if ($id < 1)
      throw new AccessDeniedHttpException();

      $presentacion_actividad = $this->cr->find($id);

      if (is_null($presentacion_actividad))
      throw new AccessDeniedHttpException();

      $form = $this->createForm(PresentacionActividadType::class, $presentacion_actividad, [
      'view' => true,
      'usuario' => $this->getUser(),
      'organizacion' => $presentacion_actividad->getOrganizacion()
      ]);

      return $this->render('presentacion_actividad/delete.html.twig', [
      'presentacion_actividad' => $presentacion_actividad,
      'form' => $form->createView()
      ]);
      }

      #[Route('/presentacion/actividad/eliminar', name: 'app_presentacion_actividad_dodelete', methods: ['DELETE'])]
      public function doDelete(Request $request): Response {

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

      $curso = $this->cr->find($id);

      $this->em->remove($curso);
      try {
      $this->em->flush();
      $this->addFlash('success', 'Se eliminó el curso correctamente.');
      } catch (ForeignKeyConstraintViolationException $e) {
      $this->addFlash('error', 'No se puede eliminar el curso. Ya se ha vendido.');
      }
      return $this->redirectToRoute('app_curso');
      }
     */
}
