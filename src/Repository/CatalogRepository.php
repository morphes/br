<?php

namespace App\Repository;

use App\Entity\Catalog;
use App\Entity\Product;
use App\Entity\ProductTagItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Catalog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Catalog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Catalog[]    findAll()
 * @method Catalog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Catalog::class);
    }

    public function findByTagId($tagId)
    {
        $catalog = $this->createQueryBuilder('c')
            ->innerJoin('c.productTagItems', 'pti')
            ->where('pti = :tag_id')
            ->setParameter('tag_id', $tagId)
            ->setMaxResults(10)
            ->getQuery()
            ->getOneOrNullResult();
        return $catalog;
    }

    public function getAllByParentTag($tagId)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.productTagItems', 'pti')
            ->where('pti.entity_id = :parent_id')
            ->setParameter('parent_id', $tagId)
            ->getQuery()
            ->getResult();
    }

    public function flushByProduct($product)
    {
        $query = 'DELETE FROM product_catalog WHERE product_id = :product_id';
        $statement = $this->_em->getConnection()->prepare($query);
        $statement->bindValue('product_id', $product->getId());
        $statement->execute();
    }

    // /**
    //  * @return Catalog[] Returns an array of Catalog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Catalog
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
