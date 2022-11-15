<?php

namespace App\Repository;

use App\Entity\Alumno;
use App\Entity\DetallePresentacionActividad;
use App\Entity\Interaccion;
use App\Entity\PresentacionActividad;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
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
    private array $orderFields = [
        'id',
        'titulo',
        'descripcion',
        'tipo',
        'estado',
        'fecha',
        'curso'
        //'estado'
    ];

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

    /*
     3


I suggest you to change fetch mode in the specific query, as described here in the doc.

So you can describe your query as follow:

$qb =  $this->createQueryBuilder('item')
                ->addSelect('groups')->join('item.groups', 'groups'); // Not necessary anymore

        $query = $qb->getQuery();
        // Describe here all the entity and the association name that you want to fetch eager
        $query->setFetchMode("YourBundle\\Entity\\Item", "groups", ClassMetadata::FETCH_EAGER);
        $query->setFetchMode("YourBundle\\Entity\\Groups", "subscriber", ClassMetadata::FETCH_EAGER);
        $query->setFetchMode("YourBundle\\Entity\\Subscriber", "user", ClassMetadata::FETCH_EAGER);
        ...

return $qb->->getResult();
NB:

Changing the fetch mode during a query is only possible for one-to-one and many-to-one relations.

Hope this help
     */
    public function findWithDetails($id = null)
    {
        $builder = $this->createQueryBuilder('c');
        $builder->setParameter('id', $id)
            ->addSelect('d')
            ->leftJoin('c.detallesPresentacionActividad', 'd')
            ->addSelect('i')
            ->leftJoin('d.interacciones', 'i')
            ->addSelect('a')
            ->leftJoin('i.alumno', 'a')
            ->where('c.id = :id')


            ->addOrderBy('d.relacion', 'ASC')
            ->addOrderBy('d.id', 'ASC')
            ->addOrderBy('a.apellido', 'ASC');

        $querry = $builder->getQuery();

        //dd($querry->getSQL());

        //$querry->setFetchMode(DetallePresentacionActividad::class, "detallesPresentacionActividad", ClassMetadata::FETCH_EAGER);

        return $querry->getOneOrNullResult();
    }
}
