<?php

namespace App\Repository;

use App\Entity\GroupBoxItems;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GroupBoxItems|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupBoxItems|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupBoxItems[]    findAll()
 * @method GroupBoxItems[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupBoxItemsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GroupBoxItems::class);
    }

    /**
     * Get box links
     * author: ThanhDT
     * date:   2018-12-24 11:13 PM
     * @param $boxKey
     * @param $limit
     * @return array
     */
    public function getBoxLinks($boxKey, $limit){
        $tags = $this->createQueryBuilder('b')
            ->select('b.boxItemId', 'b.title', 'b.linkUrl')
            ->where('b.key = :key')
            ->setParameter(':key', $boxKey)
            ->orderBy('b.order')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();

        return $tags;
    }
}
