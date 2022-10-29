<?php

namespace App\Controller;

use App\Entity\Actividad;
use App\Entity\Alumno;
use App\Entity\DetalleActividad;
use App\Form\ActividadType;
use App\Repository\ActividadRepository;
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
use function dump;

#[IsGranted('ROLE_DOCENTE')]
class ActividadController extends AbstractController {

    private EntityManagerInterface $em;
    private ActividadRepository $cr;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->cr = $this->em->getRepository(Actividad::class);
    }

    #[Route('/actividad', name: 'app_actividad')]
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
    public function new(Request $request): Response {

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

        //Gestion normal del form
        $actividad = new Actividad();
        $actividad->setUsuario($this->getUser());
        $form = $this->createForm(ActividadType::class, $actividad, [
            'tipo' => $tipo
        ]);

        //Si se enviaron los datos correctos al form y se hizo clic en Guardar...
        //Guardar la nueva actividad y terminar
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $form->get('guardar')->isClicked()) {
            $error = $this->guardarCuestionario($actividad, $detalles);
            if ($error == '') {
                $this->addFlash('success', 'Se guardó la actividad correctamente.');
                //return $this->redirectToRoute('app_actividad_edit', ['id' => $actividad->getId()]);
                return $this->redirectToRoute('app_actividad_new');
            } else {
                $this->addFlash('error', $error);
            }
        }

        //Generar HTML de preguntas enviadas por Post
        $preguntatemplate = str_replace(["\n", "\t", "\r"], '', $this->renderView('actividad/tipo/cuestionario/pregunta.html.twig'));
        $respuestatemplate = str_replace(["\n", "\t", "\r"], '', $this->renderView('actividad/tipo/cuestionario/respuesta.html.twig'));
        $detalleshtml = $this->generarPreguntasHtml($detalles, $preguntatemplate, $respuestatemplate);

        //Respuesta
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('actividad/new.html.twig', [
                    'form' => $form->createView(),
                    'tipo' => $tipo,
                    'respuestatemplate' => $respuestatemplate,
                    'preguntatemplate' => $preguntatemplate,
                    'detalleshtml' => $detalleshtml,
                    'nuevo' => empty($detalleshtml) ? 1 : 0,
                    'nocache' => true
                        ], $response);
    }

    #[Route('/actividad/editar/{id}', name: 'app_actividad_edit')]
    public function edit(int $id, Request $request): Response {
        $actividad = $this->cr->find($id);

        if (is_null($actividad) || $actividad->getUsuario() != $this->getUser())
            throw new AccessDeniedHttpException();
        
        $form = $this->createForm(ActividadType::class, $actividad, [
            'tipo' => $tipo
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*
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
             * 
             */
        }

        return $this->render('actividad/edit.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    #[Route('/actividad/ver/{id}', name: 'app_actividad_view')]
    public function view(int $id): Response {
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
                  'organizacion' => $actividad->getOrganizacion() */
        ]);

        return $this->render('actividad/new.html.twig', [
                    'form' => $form->createView(),
                    'tipo' => $actividad->getTipo(),
        ]);
    }

    #[Route('/actividad/eliminar/{id}', name: 'app_actividad_delete', methods: ['GET', 'HEAD'])]
    public function delete(int $id): Response {
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
                  'organizacion' => $actividad->getOrganizacion() */
        ]);

        return $this->render('actividad/delete.html.twig', [
                    'actividad' => $actividad,
                    'tipo' => $actividad->getTipo(),
                    'form' => $form->createView()
        ]);
    }

    #[Route('/actividad/eliminar', name: 'app_actividad_dodelete', methods: ['DELETE'])]
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

    /*
     * Genera el HTML del formulario de preguntas y respuestas en base a
     * los datos del array $detalles y los templates
     */

    private function generarPreguntasHtml(?array $detalles, string $preguntatemplate, string $respuestatemplate) {
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

    /*
     * Guarda en la DB el cuestionario recibe la actividad y el array $detalles
     */

    private function guardarCuestionario(Actividad $actividad, ?array $detalles) {

        $error = '';
        $this->em->getConnection()->beginTransaction(); // suspend auto-commit

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

                $detallepregunta = new DetalleActividad(null,
                        $preg,
                        DetalleActividad::TIPO_CUESTIONARIO_PREGUNTA,
                        null,
                        null,
                        $actividad
                );
                $this->em->persist($detallepregunta);
                $this->em->flush();
                $relacion = $detallepregunta->getId();
                $detallepregunta->setRelacion($detallepregunta->getId($relacion));
                $this->em->flush();

                
                //Respuestas de esta pregunta
                if (!isset($detalles['respuestas'][$k])) {
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

                    $detallerespuesta = new DetalleActividad(null,
                            $resp['texto'],
                            DetalleActividad::TIPO_CUESTIONARIO_RESPUESTA,
                            $relacion,
                            $correcto,
                            $actividad
                    );

                    $this->em->persist($detallerespuesta);
                    $this->em->flush();
                }

                if ($correctos < 1) {
                    $error = 'Cada pregunta debe tener al menos una respuesta correcta.';
                    throw new \Exception();
                } elseif ($correctos >= count($detalles['respuestas'][$k])) {
                    $error = 'Todas las respuestas de una pregunta no pueden ser correctas.';
                    throw new \Exception();
                }
            }

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {

            $this->em->getConnection()->rollBack();
            if ($error == '') {
                $error = 'Error al guardar en la base de datos. ' . $e->getMessage();
            }
            //$this->addFlash('error', $error);
        }

        return $error;
    }

}
