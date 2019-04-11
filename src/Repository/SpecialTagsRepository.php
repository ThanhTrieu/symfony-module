<?php

namespace App\Repository;

use App\Entity\SpecialTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SpecialTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecialTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecialTags[]    findAll()
 * @method SpecialTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecialTagsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SpecialTags::class);
    }
    
    // /**
    //  * @return SpecialTags[] Returns an array of SpecialTags objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    
    /*
    public function findOneBySomeField($value): ?SpecialTags
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getTagBySlugIsSpecial($tag_id)
    {
        $tag = $this->createQueryBuilder('c')
            ->select('c.tagId', 'c.summary', 'c.focusPosts', 'c.releaseDate', 'c.iosLink', 'c.androidLink')
            ->where('c.tagId = :tag_id')
            ->setParameters([':tag_id' => $tag_id])
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
        return $tag;
    }
}
