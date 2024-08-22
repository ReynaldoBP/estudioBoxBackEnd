<?php

namespace App\Repository;

use App\Entity\InfoOpcionRespuesta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoOpcionRespuesta>
 *
 * @method InfoOpcionRespuesta|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoOpcionRespuesta|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoOpcionRespuesta[]    findAll()
 * @method InfoOpcionRespuesta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoOpcionRespuestaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoOpcionRespuesta::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InfoOpcionRespuesta $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(InfoOpcionRespuesta $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return InfoOpcionRespuesta[] Returns an array of InfoOpcionRespuesta objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InfoOpcionRespuesta
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
