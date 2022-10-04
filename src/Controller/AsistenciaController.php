<?php

namespace App\Controller;

use App\Entity\Alumno;
use App\Entity\TomaDeAsistencia;
use App\Form\AsistenciaType;
use App\Repository\TomaDeAsistenciaRepository;
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

#[IsGranted('ROLE_DOCENTE')]
class AsistenciaController extends AbstractController
{

    private EntityManagerInterface $em;
    private TomaDeAsistenciaRepository $cr;

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
        $tomaasis->setFecha(new \DateTime());

        
        $form = $this->createForm(AsistenciaType::class, $tomaasis, ['usuario' => $this->getUser()]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            //$tomaasis->setUsuario($this->getUser());
            
            //$this->em->persist($tomaasis);
            //$this->em->flush();

            //$this->addFlash('success', 'Se creo el asistencia correctamente.');

            //return $this->redirectToRoute('app_asistencia_edit', ['id' => $tomaasis->getId()]);
            
        }
        
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('asistencia/new.html.twig', [
          'form' => $form->createView()
        ], $response);
    }

    #[Route('/asistencia/editar/{id}', name: 'app_asistencia_edit')]
    public function edit(int $id, Request $request): Response
    {
        $asistencia = $this->cr->find($id);

        if (is_null($asistencia))
            throw new AccessDeniedHttpException();

        $form = $this->createForm(AsistenciaType::class, $asistencia, [
            'modify' => true,
            'usuario' => $this->getUser(),
            'organizacion' => $asistencia->getOrganizacion()
        ]);
        $form->handleRequest($request);

        //TODO: Validar que el CUA no se repita en la organización
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('alumno_agregar')->isClicked()) {
                $data = $request->request->all()['asistencia'];

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
                    $asistencia->addAlumno($alumno);

                    $this->addFlash('success', 'Se agregó el alumno correctamente.');
                }

                $this->em->persist($asistencia);
                $this->em->flush();
                return $this->redirect($request->getUri());
            } else {

                $this->em->persist($asistencia);
                $this->em->flush();
                $this->addFlash('success', 'Se edito el asistencia correctamente.');
                return $this->redirect($request->getUri());
            }
        }

        return $this->render('asistencia/edit.html.twig', [
            'form' => $form->createView()
        ]);
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
