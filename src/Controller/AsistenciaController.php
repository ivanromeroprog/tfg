<?php

namespace App\Controller;

use App\Entity\Asistencia;
use App\Entity\TomaDeAsistencia;
use App\Form\AsistenciaType;
use App\Repository\CursoRepository;
use App\Repository\TomaDeAsistenciaRepository;
use DateTime;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
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
    private CursoRepository $cur;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cr = $this->em->getRepository(TomaDeAsistencia::class);
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
            
            //TODO: Probar Agregar las asistencias de cada alumno de este curso con valor false 
            $alumnos = $tomaasis->getCurso()->getAlumnos();
            foreach($alumnos as $alumno){
                $asistencia = new Asistencia(null, $alumno, false);
                $tomaasis->addAsistencia($asistencia);
            }
            $this->em->persist($tomaasis);
            $this->em->flush();

            return $this->redirectToRoute('app_asistencia_edit', ['id' => $tomaasis->getId(), 'modal' => 'true']);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('asistencia/new.html.twig', [
            'form' => $form->createView()
        ], $response);
    }

    #[Route('/asistencia/panel/{id}/{modal}', name: 'app_asistencia_edit')]
    public function edit(int $id, Request $request, string $modal = 'false'): Response
    {
        $tomaasis = $this->cr->find($id);

        if (is_null($tomaasis))
            throw new AccessDeniedHttpException();

        $code = $tomaasis->getUrlEncoded();
        $url = $this->generateUrl(
            'app_asistencia_alumno',
            ['code' => $code],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        
        
        //Lista de alumnos
        //Si sigue abierta la toma de asistencia cargar desde alumnos del curso
        $alumnos = $tomaasis->getCurso()->getAlumnos();
        

        $form = $this->createForm(AsistenciaType::class, $tomaasis, [
            'usuario' => $this->getUser(),
            'modify' => true,
        ]);
        $form->handleRequest($request);

        /*

          if ($form->isSubmitted() && $form->isValid()) {

          $this->em->persist($tomaasis);
          $this->em->flush();

          //$this->addFlash('success', 'Se creo el asistencia correctamente.');

          return $this->redirectToRoute('app_asistencia_edit', ['id' => $tomaasis->getId()]);

          } */
        
       

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('asistencia/edit.html.twig', [
            'form' => $form->createView(),
            'url' => $url,
            'modal' => $modal === 'true',
            'lista_alumnos' => $alumnos
        ], $response);
    }

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

    #[Route('/asistencia/eliminar/{id}', name: 'app_asistencia_delete', methods: ['GET', 'HEAD'])]
    public function delete(int $id): Response
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

        return $this->render('asistencia/delete.html.twig', [
            'asistencia' => $asistencia,
            'form' => $form->createView()
        ]);
    }

    #[Route('/asistencia/eliminar', name: 'app_asistencia_dodelete', methods: ['DELETE'])]
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

        $asistencia = $this->cr->find($id);

        $this->em->remove($asistencia);
        try {
            $this->em->flush();
            $this->addFlash('success', 'Se eliminó el asistencia correctamente.');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar el asistencia. Ya se ha vendido.');
        }
        return $this->redirectToRoute('app_asistencia');
    }
}
