<?php

namespace App\Repository;

use App\Entity\TomaDeAsistencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TomaDeAsistencia>
 *
 * @method TomaDeAsistencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method TomaDeAsistencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method TomaDeAsistencia[]    findAll()
 * @method TomaDeAsistencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TomaDeAsistenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TomaDeAsistencia::class);
    }

    public function add(TomaDeAsistencia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TomaDeAsistencia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TomaDeAsistencia[] Returns an array of TomaDeAsistencia objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TomaDeAsistencia
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
