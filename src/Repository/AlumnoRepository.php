<?php

namespace App\Repository;

use App\Entity\Curso;
use App\Entity\Alumno;
use App\Entity\DetalleActividad;
use App\Entity\Organizacion;
use App\Entity\PresentacionActividad;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Alumno>
 *
 * @method Alumno|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alumno|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alumno[]    findAll()
 * @method Alumno[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlumnoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alumno::class);
    }

    public function add(Alumno $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Alumno $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /*
    Rettorna true si ya existe el CUA en la organización
    */
    public function checkCUA(Organizacion $organizacion, string $cua)
    {
        $builder = $this->createQueryBuilder('a');
        $builder
            ->setParameter('organizacion', $organizacion)
            ->setParameter('cua', $cua)
            ->innerJoin('a.cursos', 'c')
            ->where('c.organizacion = :organizacion')
            ->andWhere('a.cua = :cua');


        $querry = $builder->getQuery();

        //dd($querry->getSQL());

        //$querry->setFetchMode(DetallePresentacionActividad::class, "detallesPresentacionActividad", ClassMetadata::FETCH_EAGER);

        return $querry->getOneOrNullResult();
    }


    public function getDatosReporte(Curso $curso, Alumno $alumno = null)
    {
        $conn = $this->getEntityManager()->getConnection();

        $optsql = (is_null($alumno)) ? '' : " AND alumno.id = ?";

        $sql = "SELECT

        alumno.id AS id_alumno, alumno.nombre, alumno.apellido,
        presentacion_actividad.id AS id_presentacion_actividad, presentacion_actividad.titulo,
        presentacion_actividad.fecha,
        SUM(interaccion.correcto) AS correctos,
        COUNT(interaccion.id) AS cantidad
        
        FROM presentacion_actividad
        
        LEFT JOIN detalle_presentacion_actividad ON presentacion_actividad.id = detalle_presentacion_actividad.presentacion_actividad_id
        LEFT JOIN interaccion ON interaccion.detalle_presentacion_actividad_id = detalle_presentacion_actividad.id
        LEFT JOIN alumno ON alumno.id = interaccion.alumno_id
        
        WHERE presentacion_actividad.curso_id = ?
        AND presentacion_actividad.estado <> ?
        AND detalle_presentacion_actividad.tipo = ?
        
        $optsql
        
        GROUP BY presentacion_actividad.id, alumno.id
        ORDER BY presentacion_actividad.fecha ASC, alumno.apellido ASC";

        $statement = $conn->prepare($sql);

        $statement->bindValue(1, $curso->getId());
        $statement->bindValue(2, PresentacionActividad::ESTADO_ANULADO);
        $statement->bindValue(3, DetalleActividad::TIPO_CUESTIONARIO_PREGUNTA);
        if (!is_null($alumno))
            $statement->bindValue(4, $alumno->getId());

        return $statement->executeQuery()->fetchAllAssociative();
    }

    //    /**
    //     * @return Alumno[] Returns an array of Alumno objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Alumno
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
