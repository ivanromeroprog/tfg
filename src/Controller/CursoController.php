<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Form\CursoType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_DOCENTE')]
class CursoController extends AbstractController {

    private EntityManagerInterface $em;
    private EntityRepository $cr;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->cr = $this->em->getRepository(Curso::class);
    }

    #[Route('/curso', name: 'app_curso')]
    public function index(): Response {
        $cursos = $this->cr->findAll();

        return $this->render('curso/index.html.twig', [
                    'cursos' => $cursos
        ]);
    }

    #[Route('/curso/nuevo', name: 'app_curso_new')]
    public function new(Request $request): Response {
        $curso = new Curso();
        $form = $this->createForm(CursoType::class, $curso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($curso);
            $this->em->flush();

            $this->addFlash('success', 'Se creo el curso correctamente.');

            return $this->redirectToRoute('app_curso');
        }

        return $this->render('curso/new.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    #[Route('/curso/editar/{id}', name: 'app_curso_edit')]
    public function edit(int $id, Request $request): Response {
        $curso = $this->cr->find($id);

        if (is_null($curso))
            throw new AccessDeniedHttpException();

        $form = $this->createForm(CursoType::class, $curso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Se edito el curso correctamente.');

            return $this->redirectToRoute('app_curso');
        }

        return $this->render('curso/new.html.twig', [
                    'form' => $form->createView()
        ]);
    }
    
    
    #[Route('/curso/ver/{id}', name: 'app_curso_view')]
    public function view(int $id): Response {
        if ($id < 1)
            throw new AccessDeniedHttpException();

        $curso = $this->cr->find($id);

        if (is_null($curso))
            throw new AccessDeniedHttpException();

        $form = $this->createForm(CursoType::class, $curso, ['view' => true]);

        return $this->render('curso/new.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    #[Route('/curso/delete/{id}', name: 'app_curso_delete', methods: ['GET', 'HEAD'])]
    public function delete(int $id): Response {
        if ($id < 1)
            throw new AccessDeniedHttpException();

        $curso = $this->cr->find($id);

        if (is_null($curso))
            throw new AccessDeniedHttpException();

        $form = $this->createForm(CursoType::class, $curso, ['view' => true]);

        return $this->render('curso/delete.html.twig', [
                    'curso' => $curso,
                    'form' => $form->createView()
        ]);
    }

    #[Route('/curso/delete', name: 'app_curso_dodelete', methods: ['DELETE'])]
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

}
