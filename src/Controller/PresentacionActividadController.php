<?php

namespace App\Controller;

use App\Entity\Alumno;
use App\Entity\PresentacionActividad;
use App\Form\PresentacionActividadType;
use App\Repository\PresentacionActividadRepository;
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

#[IsGranted('ROLE_DOCENTE')]
class PresentacionActividadController extends AbstractController
{

    private EntityManagerInterface $em;
    private PresentacionActividadRepository $cr;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cr = $this->em->getRepository(PresentacionActividad::class);
    }

    #[Route('/presentacion/actividad', name: 'app_presentacion_actividad')]
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

    /*
    #[Route('/presentacion/actividad/nuevo', name: 'app_presentacion_actividad_new')]
    public function new(Request $request): Response
    {
        $presentacion_actividad = new PresentacionActividad();
        $presentacion_actividad->setAnio(intval(date("Y")));
        $form = $this->createForm(PresentacionActividadType::class, $presentacion_actividad, ['usuario' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $presentacion_actividad->setUsuario($this->getUser());

            $this->em->persist($presentacion_actividad);
            $this->em->flush();

            $this->addFlash('success', 'Se creo el presentacion_actividad correctamente.');

            return $this->redirectToRoute('app_presentacion_actividad_edit', ['id' => $presentacion_actividad->getId()]);
        }

        return $this->render('presentacion_actividad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
*/
    #[Route('/presentacion/actividad/nuevo', name: 'app_presentacion_actividad_new')]
    public function new(Request $request): Response
    {
        $presactividad = new PresentacionActividad();
        $presactividad->setFecha(new DateTime());

        $form = $this->createForm(PresentacionActividadType::class, $presactividad, ['usuario' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->getConnection()->beginTransaction(); 
            $actividad = $form->get('actividad')->getData();
            
            try {
                $presactividad->setUsuario($this->getUser());
                $presactividad->setEstado(PresentacionActividad::ESTADO_INICIADO);
                $presactividad->setTitulo($titulo);
                $presactividad->setDescripcion($descripcion);
                $presactividad->setTipo($tipo);
                
                //$presactividad->setTitulo()
                $this->em->persist($presactividad);
                $this->em->flush();

                $this->em->getConnection()->commit();
            } catch (\Exception $e) {

                $this->em->getConnection()->rollBack();
                if ($error == '') {
                    $error = 'Error al guardar en la base de datos. ' . $e->getMessage();
                }
            }

        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('presentacion_actividad/new.html.twig', [
            'form' => $form->createView()
        ], $response);
    }
    
    #[Route('/presentacion/actividad/editar/{id}', name: 'app_presentacion_actividad_edit')]
    public function edit(int $id, Request $request): Response
    {
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
    public function view(int $id): Response
    {
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
    public function delete(int $id): Response
    {
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
}
