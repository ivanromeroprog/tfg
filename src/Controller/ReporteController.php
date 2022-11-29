<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\Alumno;
use App\Helpers\ColorsHelper;
use App\Repository\CursoRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\AlumnoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilder;

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
    public function alumno(ChartBuilderInterface $chartBuilder): Response
    {



        $usuario = $this->getUser();
        $cursos = $this->cr->findBy(['usuario' => $this->getUser()]);
        $curso = $cursos[0];
        $datos = $this->ar->getDatosReporte($curso);
        $alumnos = $curso->getAlumnos();

        //Preparar campos del formulario
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('id_curso', EntityType::class, [
            'class' => Curso::class,
            'label' => 'Curso',
            'multiple' => false,
            'required' => true,
            'autocomplete' => false,
            'query_builder' => function (CursoRepository $er) use ($usuario) {
                return $er->createQueryBuilder('c')
                    ->innerJoin('c.usuario', 'u')
                    ->setParameter('usuario', $usuario)
                    ->where('u = :usuario');
            }
        ]);

        $formBuilder->add('id_alumno', EntityType::class, [
            'class' => Alumno::class,
            'label' => 'Alumno',
            'multiple' => false,
            'required' => false,
            'autocomplete' => true,
            /*'query_builder' => function (AlumnoRepository $er) use ($curso) {
                return $er->createQueryBuilder('c')
                    ->innerJoin('c.curso', 'u')
                    ->setParameter('curso', $curso)
                    ->where('u = :curso');
                    }*/
            'choices' => $alumnos,
            'empty_data' => '[Todos]',
            'data' => null

        ]);

        $formBuilder->add('submit', SubmitType::class, ['label' => 'Filtrar']);

        $form = $formBuilder->getForm();

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
            $datoschart[$v['id_alumno']]['borderColor']     = ColorsHelper::adjustBrightness($color, -0.5);

            if ($v['cantidad'] == 0 || $v['correctos'] == 0)
                $datoschart[$v['id_alumno']]['data'][$v['id_presentacion_actividad']] = 0;
            else
                $datoschart[$v['id_alumno']]['data'][$v['id_presentacion_actividad']] = $v['correctos'] / $v['cantidad'] * 100;

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
            'labels' => $labels,
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

        return $this->render('reporte/alumno.html.twig', [
            'chart' => $chart,
            'form' => $form->createView(),
        ]);
    }
}
