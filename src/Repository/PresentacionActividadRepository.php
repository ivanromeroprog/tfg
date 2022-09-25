<?php

namespace App\Repository;

use App\Entity\PresentacionActividad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PresentacionActividad>
 *
 * @method PresentacionActividad|null find($id, $lockMode = null, $lockVersion = null)
 * @method PresentacionActividad|null findOneBy(array $criteria, array $orderBy = null)
 * @method PresentacionActividad[]    findAll()
 * @method PresentacionActividad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresentacionActividadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PresentacionActividad::class);
    }

    public function add(PresentacionActividad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PresentacionActividad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PresentacionActividad[] Returns an array of PresentacionActividad objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PresentacionActividad
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
