<?php

namespace App\Controller;

use App\Entity\Alumno;
use App\Entity\Curso;
use App\Form\CursoType;
use App\Repository\CursoRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
class CursoController extends AbstractController
{

    private EntityManagerInterface $em;
    private CursoRepository $cr;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cr = $this->em->getRepository(Curso::class);
    }

    #[Route('/curso', name: 'app_curso')]
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
                    'grado' => $search,
                    'materia' => $search,
                    'division' => $search,
                    'anio' => $search
                ] : [],
            $order,
            $this->getUser()
        );

        $pager = new Pagerfanta(new QueryAdapter($listqb));
        $pager->setMaxPerPage($perpage);
        $pager->setCurrentPage($page);

        return $this->render('curso/index.html.twig', [
            'pager' => $pager,
            'order' => $order,
            'search' => $search,
            'perpageoptions' => [
                10, 25, 50, 100
            ]
        ]);
    }

    #[Route('/curso/nuevo', name: 'app_curso_new')]
    public function new(Request $request): Response
    {
        $curso = new Curso();
        $curso->setAnio(intval(date("Y")));
        $form = $this->createForm(CursoType::class, $curso, ['usuario' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $curso->setUsuario($this->getUser());

            $this->em->persist($curso);
            $this->em->flush();

            $this->addFlash('success', 'Se creo el curso correctamente.');

            return $this->redirectToRoute('app_curso_edit', ['id' => $curso->getId()]);
        }

        return $this->render('curso/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/curso/editar/{id}', name: 'app_curso_edit')]
    public function edit(int $id, Request $request): Response
    {
        $curso = $this->cr->find($id);

        if (is_null($curso))
            throw new AccessDeniedHttpException();

        $form = $this->createForm(CursoType::class, $curso, [
            'modify' => true,
            'usuario' => $this->getUser(),
            'organizacion' => $curso->getOrganizacion()
        ]);
        $form->handleRequest($request);

        //TODO: Validar que el CUA no se repita en la organización
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('alumno_agregar')->isClicked()) {
                $data = $request->request->all()['curso'];

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
                    $curso->addAlumno($alumno);

                    $this->addFlash('success-alumnos', 'Se agregó el alumno correctamente.');
                }

                $this->em->persist($curso);
                $this->em->flush();
                return $this->redirect($request->getUri());
            } else {

                $this->em->persist($curso);
                $this->em->flush();
                $this->addFlash('success', 'Se edito el curso correctamente.');
                return $this->redirect($request->getUri());
            }
        }

        return $this->render('curso/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/curso/ver/{id}', name: 'app_curso_view')]
    public function view(int $id): Response
    {
        if ($id < 1)
            throw new AccessDeniedHttpException();

        $curso = $this->cr->find($id);

        if (is_null($curso))
            throw new AccessDeniedHttpException();

        $form = $this->createForm(CursoType::class, $curso, [
            'view' => true,
            'usuario' => $this->getUser(),
            'organizacion' => $curso->getOrganizacion()
        ]);

        return $this->render('curso/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/curso/eliminar/{id}', name: 'app_curso_delete', methods: ['GET', 'HEAD'])]
    public function delete(int $id): Response
    {
        if ($id < 1)
            throw new AccessDeniedHttpException();

        $curso = $this->cr->find($id);

        if (is_null($curso))
            throw new AccessDeniedHttpException();

        $form = $this->createForm(CursoType::class, $curso, [
            'view' => true,
            'usuario' => $this->getUser(),
            'organizacion' => $curso->getOrganizacion()
        ]);

        return $this->render('curso/delete.html.twig', [
            'curso' => $curso,
            'form' => $form->createView()
        ]);
    }

    #[Route('/curso/eliminar', name: 'app_curso_dodelete', methods: ['DELETE'])]
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
