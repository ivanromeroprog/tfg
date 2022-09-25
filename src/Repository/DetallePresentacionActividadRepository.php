<?php

namespace App\Repository;

use App\Entity\DetallePresentacionActividad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetallePresentacionActividad>
 *
 * @method DetallePresentacionActividad|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetallePresentacionActividad|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetallePresentacionActividad[]    findAll()
 * @method DetallePresentacionActividad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetallePresentacionActividadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetallePresentacionActividad::class);
    }

    public function add(DetallePresentacionActividad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetallePresentacionActividad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetallePresentacionActividad[] Returns an array of DetallePresentacionActividad objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DetallePresentacionActividad
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
