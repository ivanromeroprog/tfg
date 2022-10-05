<?php

namespace App\Repository;

use App\Entity\TomaDeAsistencia;
use App\Entity\Usuario;
use App\Entity\Curso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
    private array $orderFields = [
        't.id',
        'c.grado',
        'c.materia',
        't.estado',
        't.fecha'
    ];

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

    /**
     * 
     * @param array $onlikecriteria
     * @param int $order orden de los registros, números diferentes de 0. Negativo significa DESC, positivo ASC
     * @param int $usuario_id
     * @return QueryBuilder
     */
    public function listQueryBuilder(array $onlikecriteria = [], int $order = -1, Usuario $usuario = null): QueryBuilder
    {
        $builder = $this->createQueryBuilder('t');
        $builder->innerJoin('t.curso', 'c');

        $i = 0;
        foreach ($onlikecriteria as $field => $value) {
            $builder->setParameter('param' . $i, '%' . $value . '%');
            $builder->orWhere($field . ' LIKE :' . 'param' . $i);
            $i++;
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
            $builder->orderBy($this->orderFields[$orderindex], $orderdirection);
        }

        return $builder;
    }

    public function list($onlikecriteria = [], $order = 1, $usuario = null)
    {
        return $this->listQueryBuilder($onlikecriteria, $order, $usuario)->getQuery()->getResult();
    }

    public function getOrderFields(): array
    {
        return $this->orderFields;
    }

    public function setOrderFields(array $orderFields): void
    {
        $this->orderFields = $orderFields;
    }
}
