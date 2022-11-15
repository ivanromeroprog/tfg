<?php

namespace App\Repository;

use App\Entity\Alumno;
use App\Entity\Organizacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
