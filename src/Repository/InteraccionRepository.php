<?php

namespace App\Repository;

use App\Entity\Alumno;
use App\Entity\DetallePresentacionActividad;
use App\Entity\Interaccion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Interaccion>
 *
 * @method Interaccion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Interaccion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Interaccion[]    findAll()
 * @method Interaccion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InteraccionRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Interaccion::class);
    }

    public function add(Interaccion $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Interaccion $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByPregunta(Alumno $alumno, DetallePresentacionActividad $detallepreguna, string $tipo = null) {
        $builder = $this->createQueryBuilder('i');
        $builder
                ->setParameter('alumno', $alumno)
                ->setParameter('relacion', $detallepreguna->getRelacion())
                ->addSelect('d')
                ->leftJoin('i.detallePresentacionActividad', 'd', Join::WITH, 'd.relacion = :relacion')
                ->where('i.alumno = :alumno');
        
        if (!is_null($tipo)) {
            $builder
                    ->setParameter('tipo', $tipo)
                    ->andWhere('d.tipo = :tipo')
            ;
        }
        
        $querry = $builder->getQuery();

        //dump($querry->getSQL());

        $querry->setFetchMode(DetallePresentacionActividad::class, "detallesPresentacionActividad", ClassMetadata::FETCH_EAGER);

        return $querry->getResult();
    }

//    /**
//     * @return Interaccion[] Returns an array of Interaccion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
//    public function findOneBySomeField($value): ?Interaccion
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
