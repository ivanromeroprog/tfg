<?php

namespace App\Controller;

use App\Entity\Alumno;
use App\Entity\Actividad;
use App\Form\ActividadType;
use App\Repository\ActividadRepository;
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

        $actividad = new Actividad();
        $actividad->setUsuario($this->getUser());
        $form = $this->createForm(ActividadType::class, $actividad, [
            'tipo' => $tipo
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            dump($form->getData());
            dump($detalles);
            /*

            $actividad->setUsuario($this->getUser());

            $this->em->persist($actividad);
            $this->em->flush();

            $this->addFlash('success', 'Se creo la actividad correctamente.');

            return $this->redirectToRoute('app_actividad_edit', ['id' => $actividad->getId()]);

            */
        }
        /*<div class="row">
        <div class="col-11">
        <label class="form-label mt-2" for="detalle_preguntas_%_pid_%">Pregunta %_pnum_%</label>
		<textarea name="detalle[preguntas][%_pid_%]" id="detalle_preguntas_%_pid_%" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-1">
        <a id="eliminar_pregunta_%_pid_%" class="btn btn-danger btn-sm" title="Eliminar"><i class="bi bi-trash-fill"></i></a>
        </div>
		</div>*/
        $preguntatemplate = '<div class="pregunta_div" id="pregunta_div_%_pid_%">
		<a id="eliminar_pregunta_%_pid_%" class="btn btn-danger btn-sm" title="Eliminar Pregunta">
            <i class="bi bi-trash-fill"></i>
        </a> <label class="form-label mt-2" for="detalle_preguntas_%_pid_%">Pregunta %_pnum_%</label>
		<textarea name="detalle[preguntas][%_pid_%]" id="detalle_preguntas_%_pid_%" class="form-control" rows="3"></textarea>
		<div class="row">
			<div class="col-7">Respuesta</div>
			<div class="col-3">Correcta</div>
            <div class="col-2"></div>
		</div>
	    </div>
        <div class="text-center mt-2">
        <input type="button" value="Agregar Respuesta" class="btn btn-success">
        </div>';

        $respuestatemplate = '<div class="respuesta_div" id="respuesta_div_%_pid_%_%_rid_%">
		<div class="row mt-1">
			<div class="col-7">

				<input type="text" class="form-control" name="detalle[respuestas][%_pid_%][%_rid_%][texto]" id="detalle_respuestas_%_pid_%_%_rid_%_texto">
			</div>
			<div class="col-3 d-flex align-items-center justify-content-start">
				
            <div class="form-check form-switch form-switch-hide">
					<input class="form-check-input sino" name="detalle[respuestas][%_pid_%][%_rid_%][correcta]" type="checkbox" value="si" id="detalle_respuestas_%_pid_%_%_rid_%_correcta">
					<label for="detalle_respuestas_%_pid_%_%_rid_%_correcta"></label>
                    </div>	

			</div>
            <div class="col-2"><a id="eliminar_respuesta_%_pid_%_%_rid_%" class="btn btn-danger btn-sm" title="Eliminar">
            <i class="bi bi-trash-fill"></i>
        </a></div>
		</div>
	</div>';

        return $this->render('actividad/new.html.twig', [
            'form' => $form->createView(),
            'tipo' => $tipo,
            'respuestatemplate' => $respuestatemplate,
            'preguntatemplate' => $preguntatemplate,
            'nuevo' => true
        ]);
    }

    #[Route('/actividad/editar/{id}', name: 'app_actividad_edit')]
    public function edit(int $id, Request $request): Response
    {
        $actividad = $this->cr->find($id);

        if (is_null($actividad))
            throw new AccessDeniedHttpException();

        $form = $this->createForm(ActividadType::class, $actividad, [
            'modify' => true,
            'usuario' => $this->getUser(),
            'organizacion' => $actividad->getOrganizacion()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('alumno_agregar')->isClicked()) {
                $data = $request->request->all()['actividad'];

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
                    $actividad->addAlumno($alumno);

                    $this->addFlash('success-alumnos', 'Se agregó el alumno correctamente.');
                }

                $this->em->persist($actividad);
                $this->em->flush();
                return $this->redirect($request->getUri());
            } else {

                $this->em->persist($actividad);
                $this->em->flush();
                $this->addFlash('success', 'Se edito el actividad correctamente.');
                return $this->redirect($request->getUri());
            }
        }

        return $this->render('actividad/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/actividad/ver/{id}', name: 'app_actividad_view')]
    public function view(int $id): Response
    {
        if ($id < 1)
            throw new AccessDeniedHttpException();

        $actividad = $this->cr->find($id);

        if (is_null($actividad))
            throw new AccessDeniedHttpException();

        $form = $this->createForm(ActividadType::class, $actividad, [
            'view' => true,
            'tipo' => $actividad->getTipo()
            /*
            'usuario' => $this->getUser(),
            'organizacion' => $actividad->getOrganizacion()*/
        ]);

        return $this->render('actividad/new.html.twig', [
            'form' => $form->createView(),
            'tipo' => $actividad->getTipo(),
        ]);
    }

    #[Route('/actividad/eliminar/{id}', name: 'app_actividad_delete', methods: ['GET', 'HEAD'])]
    public function delete(int $id): Response
    {
        if ($id < 1)
            throw new AccessDeniedHttpException();

        $actividad = $this->cr->find($id);

        if (is_null($actividad))
            throw new AccessDeniedHttpException();

        $form = $this->createForm(ActividadType::class, $actividad, [
            'view' => true,
            'tipo' => $actividad->getTipo()
            /*
            'usuario' => $this->getUser(),
            'organizacion' => $actividad->getOrganizacion()*/
        ]);

        return $this->render('actividad/delete.html.twig', [
            'actividad' => $actividad,
            'tipo' => $actividad->getTipo(),
            'form' => $form->createView()
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
}
