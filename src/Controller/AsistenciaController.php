<?php

namespace App\Controller;

use App\Entity\Asistencia;
use App\Entity\TomaDeAsistencia;
use App\Form\AsistenciaType;
use App\Repository\AsistenciaRepository;
use App\Repository\CursoRepository;
use App\Repository\TomaDeAsistenciaRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
class AsistenciaController extends AbstractController
{

    private EntityManagerInterface $em;
    private TomaDeAsistenciaRepository $cr;
    private AsistenciaRepository $ar;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cr = $this->em->getRepository(TomaDeAsistencia::class);
        $this->ar = $this->em->getRepository(Asistencia::class);
    }

    #[Route('/asistencia', name: 'app_asistencia')]
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
                    'c.grado' => $search,
                    'c.materia' => $search,
                    'c.division' => $search,
                    't.fecha' => $search
                ] : [],
            $order,
            $this->getUser()
        );

        $pager = new Pagerfanta(new QueryAdapter($listqb));
        $pager->setMaxPerPage($perpage);
        $pager->setCurrentPage($page);

        return $this->render('asistencia/index.html.twig', [
            'pager' => $pager,
            'order' => $order,
            'search' => $search,
            'perpageoptions' => [
                10, 25, 50, 100
            ]
        ]);
    }

    #[Route('/asistencia/nuevo', name: 'app_asistencia_new')]
    public function new(Request $request): Response
    {
        $tomaasis = new TomaDeAsistencia();
        $tomaasis->setFecha(new DateTime());

        $form = $this->createForm(AsistenciaType::class, $tomaasis, ['usuario' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tomaasis->setEstado(TomaDeAsistencia::ESTADO_INICIADO);

            //TODO: Probar si funciona bien Agregar las asistencias de cada alumno de este curso con valor false 
            $alumnos = $tomaasis->getCurso()->getAlumnos();

            if (count($alumnos) > 0) {
                foreach ($alumnos as $alumno) {
                    $asistencia = new Asistencia(null, $alumno, false);
                    $this->em->persist($asistencia);
                    $tomaasis->addAsistencia($asistencia);
                }
                $this->em->persist($tomaasis);
                $this->em->flush();

                return $this->redirectToRoute('app_asistencia_edit', ['id' => $tomaasis->getId(), 'modal' => 'true']);
            } else {
                $this->addFlash('error', 'El curso seleccionado no tiene alumnos cargados, no puede tomar asistencia.');
            }
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('asistencia/new.html.twig', [
            'form' => $form->createView()
        ], $response);
    }

    #[Route('/asistencia/panel/{id}/{modal}/{pregunta}/{idasistencia}/{presente}', name: 'app_asistencia_edit')]
    public function edit(int $id, Request $request, string $modal = 'false', string $pregunta = '', int $idasistencia = 0, ?bool $presente = null): Response
    {

        /*
        Preparar valores necesarios
        */
        //Toma de asistencia
        $tomaasis = $this->cr->find($id);
        if (is_null($tomaasis) || $tomaasis->getCurso()->getUsuario() != $this->getUser())
            throw new AccessDeniedHttpException();

        //Url para compartir la toma de asistencia
        $code = $tomaasis->getUrlEncoded();
        $url = $this->generateUrl(
            'app_asistencia_alumno',
            ['code' => $code],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        //Lista de asistencias de alumnos de esta toma de asistencia
        $lista_asistencias = $tomaasis->getAsistencias();

        //Pregunta para el usuario, filtrar para solo aceptar los valores válidos
        $pregunta = (in_array($pregunta, ['anular', 'iniciar', 'finalizar']) ? $pregunta : 'f');


        /*
        Formulario
        */
        $form = $this->createForm(AsistenciaType::class, $tomaasis, [
            'usuario' => $this->getUser(),
            'modify' => true,
            'pregunta' => $pregunta
        ]);
        $form->handleRequest($request);

        //Se envio el form desde alguno de los botones de submit
        if ($form->isSubmitted() && $form->isValid()) {

            //Determinar que boton se uso, se usan los valores de 
            //TomaDeAsistencia::ESTADOS como nombres de los botones
            $estado = '';
            foreach (TomaDeAsistencia::ESTADOS as $e) {
                if ($form->has($e) && $form->get($e)->isClicked()) {
                    $estado = $e;
                }
            }

            if ($estado != '') {
                $tomaasis->setEstado($estado);
                $this->em->persist($tomaasis);
                $this->em->flush();

                $this->addFlash('success', 'Se ' .
                    ($estado == TomaDeAsistencia::ESTADO_ANULADO ? 'Anuló' : ($estado == TomaDeAsistencia::ESTADO_INICIADO ? 'Inició' : ($estado == TomaDeAsistencia::ESTADO_FINALIZADO ? 'Finalizó' :
                        '')))
                    . ' la toma de asistencia correctamente.');
                return $this->redirectToRoute(
                    'app_asistencia_edit',
                    [
                        'id' => $tomaasis->getId(),
                        'modal' => ($estado == TomaDeAsistencia::ESTADO_INICIADO ? 'true' : 'f')
                    ]
                );
            }
        } elseif ($idasistencia > 0) {
            //Turboframes: No se envio form y Docente cambia el estado de asistencia de un alumno
            $asistencia = $this->ar->find($idasistencia);
            if (is_null($asistencia) || !($asistencia->getTomaDeAsistencia() === $tomaasis) || is_null($presente)) {
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
        }

        //Renderizar página normalmente
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('asistencia/edit.html.twig', [
            'form' => $form->createView(),
            'url' => $url,
            'modal' => $modal === 'true',
            'lista_asistencias' => $lista_asistencias,
            'pregunta' => $pregunta,
            'tomaasis' => $tomaasis,
        ], $response);
    }

    /*
      #[Route('/asistencia/ver/{id}', name: 'app_asistencia_view')]
      public function view(int $id): Response
      {
      if ($id < 1)
      throw new AccessDeniedHttpException();

      $asistencia = $this->cr->find($id);

      if (is_null($asistencia))
      throw new AccessDeniedHttpException();

      $form = $this->createForm(AsistenciaType::class, $asistencia, [
      'view' => true,
      'usuario' => $this->getUser(),
      'organizacion' => $asistencia->getOrganizacion()
      ]);

      return $this->render('asistencia/new.html.twig', [
      'form' => $form->createView(),
      ]);
      }


      #[Route('/asistencia/anular/{id}', name: 'app_asistencia_delete', methods: ['GET', 'HEAD'])]
      public function delete(int $id): Response {
      if ($id < 1)
      throw new AccessDeniedHttpException();

      $asistencia = $this->cr->find($id);
      $anulado = ($asistencia->getEstado() === TomaDeAsistencia::ESTADO_ANULADO);

      if (is_null($asistencia))
      throw new AccessDeniedHttpException();

      $form = $this->createForm(AsistenciaType::class, $asistencia, [
      'view' => true,
      'usuario' => $this->getUser(),
      'organizacion' => $asistencia->getOrganizacion()
      ]);

      return $this->render('asistencia/delete.html.twig', [
      'asistencia' => $asistencia,
      'form' => $form->createView(),
      'anulado' => $anulado,
      ]);
      }

      #[Route('/asistencia/anular', name: 'app_asistencia_dodelete', methods: ['DELETE'])]
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

      $asistencia = $this->cr->find($id);
      $anulado = ($asistencia->getEstado() === TomaDeAsistencia::ESTADO_ANULADO);
      if($anulado){
      $asistencia->setEstado(TomaDeAsistencia::ESTADO_INICIADO);
      }
      else
      {
      $asistencia->setEstado(TomaDeAsistencia::ESTADO_ANULADO);
      }


      //$this->em->remove($asistencia);
      try {
      $this->em->flush();
      $this->addFlash('success', 'Se anuló la asistencia correctamente.');
      } catch (Exception $e) {
      $this->addFlash('error', 'No se puede eliminar la toma de asistencia.');
      }
      return $this->redirectToRoute('app_asistencia');
      }
     */
}
