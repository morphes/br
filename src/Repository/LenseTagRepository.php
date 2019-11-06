<?php

namespace App\Repository;

use App\Entity\LenseTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LenseTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method LenseTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method LenseTag[]    findAll()
 * @method LenseTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LenseTagRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LenseTag::class);
    }

    // /**
    //  * @return LenseTag[] Returns an array of LenseTag objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LenseTag
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
