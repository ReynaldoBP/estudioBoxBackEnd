<?php

namespace App\Repository;

use App\Entity\AdmiTipoOpcionRespuesta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdmiTipoOpcionRespuesta>
 *
 * @method AdmiTipoOpcionRespuesta|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdmiTipoOpcionRespuesta|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdmiTipoOpcionRespuesta[]    findAll()
 * @method AdmiTipoOpcionRespuesta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdmiTipoOpcionRespuestaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdmiTipoOpcionRespuesta::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(AdmiTipoOpcionRespuesta $entity, bool $flush = true): void
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
    public function remove(AdmiTipoOpcionRespuesta $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return AdmiTipoOpcionRespuesta[] Returns an array of AdmiTipoOpcionRespuesta objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdmiTipoOpcionRespuesta
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
