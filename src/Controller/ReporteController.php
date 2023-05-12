<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\Alumno;
use App\Helpers\ColorsHelper;
use App\Repository\CursoRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\AlumnoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReporteController extends AbstractController
{

    private EntityManagerInterface $em;
    private AlumnoRepository $ar;
    private CursoRepository $cr;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->ar = $em->getRepository(Alumno::class);
        $this->cr = $em->getRepository(Curso::class);
    }

    #[Route('/reporte', name: 'app_reporte')]
    public function index(): Response
    {
        return $this->render('reporte/index.html.twig', [
            'controller_name' => 'ReporteController',
        ]);
    }
    #[Route('/reporte/alumno', name: 'app_reporte_alumno')]
    public function alumno(ChartBuilderInterface $chartBuilder, Request $request): Response
    {
        //Cargar listado de cursos de este usuario
        $usuario = $this->getUser();
        $cursos  = $this->cr->findBy(['usuario' => $this->getUser()]);

        //Cargar el curso actual. Por defecto o el seleccionado por form
        $formdata = $request->request->all();
        $curso = null;
        if (isset($formdata['form']['id_curso']) && is_numeric($formdata['form']['id_curso'])) {
            $curso = $this->cr->find(intval($formdata['form']['id_curso']));
            //dump($curso);
        }
        $curso = is_null($curso) ? $cursos[0] : $curso;

        //Cargar alumnos de este curso
        $alumnos = $curso->getAlumnos();

        //Cargar el alumno actual si se envió
        $alumno = null;
        if (isset($formdata['form']['id_alumno']) && is_numeric($formdata['form']['id_alumno'])) {
            $aid = intval($formdata['form']['id_alumno']);
            foreach ($alumnos as $a) {
                //dump($a, $aid);
                if ($a->getId() == $aid) {
                    $alumno = $a;
                    break;
                }
            }
        }

        //Obtener datos para el gráfico
        $datos = $this->ar->getDatosReporte($curso, $alumno);

        //Preparar campos del formulario
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('id_curso', EntityType::class, [
            'class' => Curso::class,
            'label' => 'Curso',
            'multiple' => false,
            'required' => true,
            'autocomplete' => true,
            'query_builder' => function (CursoRepository $er) use ($usuario) {
                return $er->createQueryBuilder('c')
                    ->innerJoin('c.usuario', 'u')
                    ->setParameter('usuario', $usuario)
                    ->where('u = :usuario');
            },
            'attr' => [
                'data-action' => 'change->chartform#submitForm'
            ]
        ]);
        $formBuilder->add('id_alumno', EntityType::class, [
            'class' => Alumno::class,
            'label' => 'Alumno',
            'multiple' => false,
            'required' => false,
            'autocomplete' => true,
            'choices' => $alumnos,
            'empty_data' => null,
            'data' => null,
            'placeholder' => '- Todos -',
            'attr' => [
                'data-action' => 'change->chartform#submitForm'
            ]

        ]);
        //$formBuilder->add('submit', SubmitType::class, ['label' => 'Filtrar']);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        //Preparar datos para el gráfico
        $datoschart = [];
        $labels = [];
        $colors = [];
        foreach ($datos as $k => $v) {

            do {
                $color = ColorsHelper::randomColor();
            } while (in_array($color, $colors));
            $colors[] = $color;

            $v['cantidad'] = is_null($v['cantidad']) ? 0 : $v['cantidad'];
            $v['correctos'] = is_null($v['correctos']) ? 0 : $v['correctos'];
            $datoschart[$v['id_alumno']]['label']           = $v['apellido'] . ', ' . $v['nombre'];
            $datoschart[$v['id_alumno']]['backgroundColor'] = $color;
            $datoschart[$v['id_alumno']]['borderColor']     = ColorsHelper::adjustBrightness($color, -0.1);

            if ($v['cantidad'] == 0 || $v['correctos'] == 0)
                $tmpdata = 0;
            else
                $tmpdata = $v['correctos'] / $v['cantidad'] * 100;

            $datoschart[$v['id_alumno']]['data'][$v['id_presentacion_actividad']] = ['x' => date('d/m/Y', strtotime($v['fecha'])) . ' - ' . $v['titulo'], 'y' => $tmpdata];
            $labels[$v['id_presentacion_actividad']] = $v['titulo'];
        }

        //Quitar ID de las arrays
        $datoschart = array_values($datoschart);
        $labels = array_values($labels);
        foreach ($datoschart as $k => $v) {
            $datoschart[$k]['data'] = array_values($v['data']);
        }

        //Crear grafico
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $tmp = [
            //'labels' => $labels,
            /*
                'datasets' => [
                    [
                        'label' => 'My First dataset',
                        'backgroundColor' => 'rgb(255, 99, 132)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'data' => [0, 10, 5, 2, 20, 30, 45],
                    ],
                ]*/
            'datasets' => $datoschart
        ];
        $chart->setData($tmp);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('reporte/alumno.html.twig', [
            'chart' => $chart,
            'form' => $form->createView(),
        ], $response);
    }

    #[Route('/reporte/curso', name: 'app_reporte_curso')]
    public function curso(ChartBuilderInterface $chartBuilder, Request $request): Response
    {
        //Cargar listado de cursos de este usuario
        $usuario = $this->getUser();
        $cursos  = $this->cr->findBy(['usuario' => $this->getUser()]);

        //Cargar el curso actual. Por defecto o el seleccionado por form
        $formdata = $request->request->all();
        $curso = null;
        if (isset($formdata['form']['id_curso']) && is_numeric($formdata['form']['id_curso'])) {
            $curso = $this->cr->find(intval($formdata['form']['id_curso']));
            //dump($curso);
        }
        $curso = is_null($curso) ? $cursos[0] : $curso;

        //Cargar alumnos de este curso
        // $alumnos = $curso->getAlumnos();

        //Cargar el alumno actual si se envió
        // $alumno = null;
        // if (isset($formdata['form']['id_alumno']) && is_numeric($formdata['form']['id_alumno'])) {
        //     $aid = intval($formdata['form']['id_alumno']);
        //     foreach ($alumnos as $a) {
        //         //dump($a, $aid);
        //         if ($a->getId() == $aid) {
        //             $alumno = $a;
        //             break;
        //         }
        //     }
        // }

        //Obtener datos para el gráfico
        $datos = $this->ar->getDatosReporteCurso($curso);

        //Preparar campos del formulario
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('id_curso', EntityType::class, [
            'class' => Curso::class,
            'label' => 'Curso',
            'multiple' => false,
            'required' => true,
            'autocomplete' => true,
            'query_builder' => function (CursoRepository $er) use ($usuario) {
                return $er->createQueryBuilder('c')
                    ->innerJoin('c.usuario', 'u')
                    ->setParameter('usuario', $usuario)
                    ->where('u = :usuario');
            },
            'attr' => [
                'data-action' => 'change->chartform#submitForm'
            ]
        ]);

        //$formBuilder->add('submit', SubmitType::class, ['label' => 'Filtrar']);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        //Preparar datos para el gráfico
        $datoschart = [];
        $labels = [];
        $colors = [];
        foreach ($datos as $k => $v) {

            do {
                $color = ColorsHelper::randomColor();
            } while (in_array($color, $colors));
            $colors[] = $color;

            // $v['cantidad'] = is_null($v['cantidad']) ? 0 : $v['cantidad'];
            // $v['correctos'] = is_null($v['correctos']) ? 0 : $v['correctos'];
            $datoschart[0]['label'] = $curso->__toString();
            $datoschart[0]['backgroundColor'] = $color;
            $datoschart[0]['borderColor'] = ColorsHelper::adjustBrightness($color, -0.1);

            // if ($v['cantidad'] == 0 || $v['correctos'] == 0)
            //     $tmpdata = 0;
            // else
            //     $tmpdata = $v['correctos'] / $v['cantidad'] * 100;

            $datoschart[0]['data'][$v['id_presentacion_actividad']] = ['x' => date('d/m/Y', strtotime($v['fecha'])) . ' - ' . mb_strimwidth($v['titulo'], 0, 30, "..."), 'y' => $v['promedio_porcentaje']];
            $labels[$v['id_presentacion_actividad']] = $v['titulo'];
        }

        //Quitar ID de las arrays
        $datoschart = array_values($datoschart);
        $labels = array_values($labels);
        foreach ($datoschart as $k => $v) {
            $datoschart[$k]['data'] = array_values($v['data']);
        }

        //Crear grafico
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $tmp = [
            // 'labels' => $labels,
            /*
                'datasets' => [
                    [
                        'label' => 'My First dataset',
                        'backgroundColor' => 'rgb(255, 99, 132)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'data' => [0, 10, 5, 2, 20, 30, 45],
                    ],
                ]*/
            'datasets' => $datoschart
        ];
        $chart->setData($tmp);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('reporte/curso.html.twig', [
            'chart' => $chart,
            'form' => $form->createView(),
        ], $response);
    }
}
