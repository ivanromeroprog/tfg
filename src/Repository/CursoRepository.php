<?php

namespace App\Repository;

use App\Entity\Curso;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Curso>
 *
 * @method Curso|null find($id, $lockMode = null, $lockVersion = null)
 * @method Curso|null findOneBy(array $criteria, array $orderBy = null)
 * @method Curso[]    findAll()
 * @method Curso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @property array $orderFields Lista de campos para ordenar las consultas.
 */
class CursoRepository extends ServiceEntityRepository {
    private array $orderFields = [
        'id',
        'grado',
        'materia',
        'anio'
    ];

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Curso::class);
    }

    public function add(Curso $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Curso $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function getOrderFields(): array {
        return $this->orderFields;
    }

    public function setOrderFields(array $orderFields): void {
        $this->orderFields = $orderFields;
    }

    /**
     * 
     * @param array $onlikecriteria
     * @param int $order orden de los registros, números diferentes de 0. Negativo significa DESC, positivo ASC
     * @param int $usuario_id
     * @return QueryBuilder
     */
    public function listQueryBuilder(array $onlikecriteria = [], int $order = -1, Usuario $usuario = null): QueryBuilder {
        $builder = $this->createQueryBuilder('c');

        foreach ($onlikecriteria as $field => $value) {
            $builder->setParameter($field, '%' . $value . '%');
            $builder->orWhere('c.' . $field . ' LIKE :' . $field);
        }

        //Si orden es negativo DESC, positivo ASC, 0 -> DESC y $order = -1
        if ($order < 0) {
            $orderdirection = 'DESC';
        } elseif($order > 0) {
            $orderdirection = 'ASC';
        } else {
            $orderdirection = 'DESC';
            $order = -1;
        }
        
        if(!is_null($usuario))
        {
            $builder->setParameter('usuario', $usuario);
            $builder->andWhere('c.usuario = :usuario');
        }
        $orderindex = abs($order) - 1;
        if (isset($this->orderFields[$orderindex])) {
            //dd($this->orderFields[$orderindex]);
            $builder->orderBy('c.' . $this->orderFields[$orderindex], $orderdirection);
        }
        
        return $builder;
    }

    public function list($onlikecriteria = [], $order = 1, $usuario_id = null): QueryBuilder {
        return $this->listQueryBuilder($onlikecriteria, $andcriteria, $orcriteria, $order)->getQuery()->getResult();
    }

//    /**
//     * @return Curso[] Returns an array of Curso objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
//    public function findOneBySomeField($value): ?Curso
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
