<?php

namespace App\Repository;

use App\Entity\GroupBoxItems;
use App\Entity\Tags;
use App\Utils\Constants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Tags|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tags|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tags[]    findAll()
 * @method Tags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tags::class);
    }
    
    /**
     * Get top trending tags
     * author: ThanhDT
     * date:   2018-12-24 09:53 PM
     * @param $limit
     * @return array
     */
    public function getTrendingTags($limit)
    {
        $tags = $this->createQueryBuilder('t')
            ->select('t.tagId, t.name, t.slug')
            ->innerJoin(GroupBoxItems::class, 'g', 'WITH', 't.tagId=g.dataId')
            ->where('g.key = :key')
            ->setParameter(':key', Constants::GROUP_BOX_TOP_TAGS)
            ->orderBy('g.order')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
        
        return $tags;
    }
    
    /**
     * Get top game tags
     * author: ThanhDT
     * date:   2018-12-24 09:55 PM
     * @param $limit
     * @return array
     */
    public function getGameTags($limit)
    {
        $tags = $this->createQueryBuilder('t')
            ->select('t.tagId, t.name, t.slug, t.avatar, t.otherTags, t.postCount, t.points')
            ->innerJoin(GroupBoxItems::class, 'g', 'WITH', 't.tagId=g.dataId')
            ->where('g.key = :key')
            ->setParameter(':key', Constants::GROUP_BOX_HOT_TOPICS)
            ->orderBy('g.order')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
        
        return $tags;
    }
    
    /**
     * Get tag by slug
     * author: ThanhDT
     * date:   2018-07-12 01:32 PM
     * @param $slug
     * @return null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTagBySlug($slug)
    {
        $tag = $this->createQueryBuilder('c')
            ->select('c.tagId', 'c.name', 'c.slug', 'c.postCount','c.points', 'c.description', 'c.isSpecial', 'c.otherTags','c.avatar')
            ->where('c.slug = :slug')
            ->setParameters([':slug' => $slug])
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
        
        return $tag;
    }
    
    /**
     * get Tags sitemap
     * author: AnhPT4
     * date: 2019-56-3 17:56 PM
     * @param $page
     * @param $perPage
     * @return array
     */
    public function getTags($page, $perPage)
    {
        $startIndex = ($page - 1) * $perPage;
        $queryBuilder = $this->createQueryBuilder('t');
        $selectFields = [
            't.tagId',
            't.name',
            't.avatar',
            't.slug',
        ];
        $topTags = $queryBuilder
            ->select($selectFields)
            ->orderBy('t.tagId', 'ASC')
            ->setFirstResult($startIndex)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getArrayResult();
        
        return $topTags;
    }
    
    /**
     * get Total Tags sitemap
     * author: AnhPT4
     * date: 2019-58-3 17:58 PM
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalTags()
    {
        $count = $this->createQueryBuilder('t')
            ->select('COUNT(t.tagId)')
            ->getQuery()->getSingleScalarResult();
        
        return $count;
    }
}
