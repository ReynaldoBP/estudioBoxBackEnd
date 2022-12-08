<?php

namespace App\Repository;

use App\Entity\InfoRespuesta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoRespuesta>
 *
 * @method InfoRespuesta|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoRespuesta|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoRespuesta[]    findAll()
 * @method InfoRespuesta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoRespuestaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoRespuesta::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InfoRespuesta $entity, bool $flush = true): void
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
    public function remove(InfoRespuesta $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return InfoRespuesta[] Returns an array of InfoRespuesta objects
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
    public function findOneBySomeField($value): ?InfoRespuesta
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
