<?php

namespace App\Repository;

use App\Entity\Actividad;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Actividad>
 *
 * @method Actividad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Actividad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Actividad[]    findAll()
 * @method Actividad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActividadRepository extends ServiceEntityRepository
{
    private array $orderFields = [
        'id',
        'titulo',
        'descripcion',
        'tipo',
        //'estado'
    ];
        
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actividad::class);
    }

    public function add(Actividad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Actividad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * 
     * @param array $onlikecriteria
     * @param int $order orden de los registros, números diferentes de 0. Negativo significa DESC, positivo ASC
     * @param int $usuario_id
     * @return QueryBuilder
     */
    public function listQueryBuilder(array $onlikecriteria = [], int $order = -1, Usuario $usuario = null): QueryBuilder
    {
        $builder = $this->createQueryBuilder('c');

        foreach ($onlikecriteria as $field => $value) {
            $builder->setParameter($field, '%' . $value . '%');
            $builder->orWhere('c.' . $field . ' LIKE :' . $field);
        }

        //Si orden es negativo DESC, positivo ASC, 0 -> DESC y $order = -1
        if ($order < 0) {
            $orderdirection = 'DESC';
        } elseif ($order > 0) {
            $orderdirection = 'ASC';
        } else {
            $orderdirection = 'DESC';
            $order = -1;
        }

        if (!is_null($usuario)) {
            $builder->setParameter('usuario', $usuario);
            $builder->andWhere('c.usuario = :usuario');
        }
        $orderindex = abs($order) - 1;
        if (isset($this->orderFields[$orderindex])) {
            $builder->orderBy('c.' . $this->orderFields[$orderindex], $orderdirection);
        }

        return $builder;
    }

    public function list($onlikecriteria = [], $order = 1, $usuario = null)
    {
        return $this->listQueryBuilder($onlikecriteria, $order, $usuario)->getQuery()->getResult();
    }
}
