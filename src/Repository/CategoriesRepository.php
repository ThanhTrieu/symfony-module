<?php

namespace App\Repository;

use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Categories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categories[]    findAll()
 * @method Categories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Categories::class);
    }

    // /**
    //  * @return Categories[] Returns an array of Categories objects
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
    public function findOneBySomeField($value): ?Categories
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
    /**
     * Get all Categories
     * author: ThanhDT
     * date:   2018-10-18 11:19 AM
     * @return array
     */
    public function getAllCategories()
    {
        $cates = $this->createQueryBuilder('c')
            ->select(['c.cateId','c.name','c.slug','c.parentId', 'c.seoTitle', 'c.seoDescription'])
            ->getQuery()->getArrayResult();
        
        return $cates;
    }
    
    /**
     * @param $cateId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDetailCates($cateId)
    {
        $data = $this->createQueryBuilder('a')
            ->select('a.cateId, a.name, a.slug, a.parentId')
            ->where('a.cateId = :cateId')
            ->setParameters([':cateId' => $cateId])
            ->getQuery()->getOneOrNullResult();
        return $data;
    }
}
