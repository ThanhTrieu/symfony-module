<?php

namespace App\Repository;

use App\Entity\Categories;
use App\Entity\GroupBoxItems;
use App\Entity\PostDatas;
use App\Entity\PostPublishes;
use App\Entity\PostsCates;
use App\Entity\PostsTags;
use App\Utils\Constants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PostPublishes|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostPublishes|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostPublishes[]    findAll()
 * @method PostPublishes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostPublishesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PostPublishes::class);
    }

    /**
     * Get home featured posts
     * author: ThanhDT
     * date:   2018-12-22 01:23 PM
     * @param $groupBoxKey
     * @param $limit
     * @return array
     */
    public function getTopPostsInGroupBox($groupBoxKey, $limit)
    {
        $posts = $this->createQueryBuilder('p')
            ->select('p.postId, p.title, p.slug, p.sapo, p.cateId, p.publishedDate, p.publishedTimestamp, p.avatar, p.authorName, c.name cateName, c.slug cateSlug')
            ->innerJoin(GroupBoxItems::class, 'gi', 'WITH', 'gi.dataId = p.postId')
            ->innerJoin(Categories::class, 'c', 'WITH', 'c.cateId = p.cateId')
            ->where('gi.key = :key AND p.publishedTimestamp <= :currentDate')
            ->setParameters([':key' => $groupBoxKey, ':currentDate' => time()])
            ->orderBy('gi.order')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $posts;
    }

    /**
     * Get focus posts
     * author: ThanhDT
     * date:   2018-12-24 08:44 AM
     * @param $focusStatus
     * @param $limit
     * @param $lastPublishedStamp
     * @param array $excludePostIds - ExlucdeIds include: LastPostId + Exclude post list
     * @return array
     */
    public function getFocusPosts($focusStatus, $limit, $lastPublishedStamp, $excludePostIds = null)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.postId, p.title, p.slug, p.sapo, p.cateId, p.publishedDate, p.publishedTimestamp, p.avatar, p.postType, c.name cateName, c.slug cateSlug')
            ->innerJoin(Categories::class, 'c', 'WITH', 'c.cateId = p.cateId');
        $parameters = [];
        if ($excludePostIds) {
            $query->where('p.postId NOT IN (:postIds)');//->setParameter(':postIds', $excludePostIds);
            $parameters[':postIds'] = $excludePostIds;
        }
        $query->andWhere('p.publishedTimestamp <= :currentDate AND p.focusStatus = :focusStatus');
        $parameters[':currentDate'] = $lastPublishedStamp;
        $parameters[':focusStatus'] = $focusStatus;
        $posts = $query->setParameters($parameters)
            ->orderBy('p.publishedDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();

        return $posts;
    }

    /**
     * Get top posts in category
     * author: ThanhDT
     * date:   2018-12-25 10:49 AM
     * @param $cateId
     * @param $limit
     * @return array
     */
    public function getTopReviewPosts($cateId, $limit)
    {
        $posts = $this->createQueryBuilder('p')
            ->select('p.postId, p.cateId, p.title, p.slug, p.avatar, p.reviewPoints, p.otherTags')
            ->where('p.cateId = :cateId AND p.publishedTimestamp <= :currentDate')
            ->setParameters([':cateId' => $cateId, ':currentDate' => time()])
            ->orderBy('p.publishedTimestamp', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();

        return $posts;
    }

    /**
     * Get paging posts in category
     * author: ThanhDT
     * date:   2018-12-26 10:10 AM
     * @param $cateIds
     * @param $limit
     * @param $lastPublishedStamp
     * @param null $excludePostIds
     * @return array
     */
    public function getCatePosts($cateIds, $limit, $lastPublishedStamp, $excludePostIds = null)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.postId, p.title, p.slug, p.sapo, p.cateId, p.publishedDate, p.publishedTimestamp, p.avatar, p.postType, c.name cateName, c.slug cateSlug')
            ->innerJoin(PostsCates::class, 'pc', 'WITH', 'pc.postId = p.postId')
            ->innerJoin(Categories::class, 'c', 'WITH', 'c.cateId = p.cateId');
        $parameters = [];
        if ($excludePostIds) {
            $query->where('p.postId NOT IN (:postIds)');//->setParameter(':postIds', $excludePostIds);
            $parameters[':postIds'] = $excludePostIds;
        }
        $query->andWhere('pc.cateId IN (:cateIds) AND p.publishedTimestamp <= :currentDate');
        $parameters[':currentDate'] = $lastPublishedStamp;
        $parameters[':cateIds'] = $cateIds;
        $posts = $query->setParameters($parameters)
            ->orderBy('p.publishedDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();

        return $posts;
    }

    /**
     * get Articles in categories by paging
     * author: AnhPT4
     * date: 2018-30-22 9:30 AM
     * @param $cateId
     * @param $pageIndex
     * @param $pageSize
     * @return array
     */
    public function getArticleInCatePaging($cateId, $pageIndex, $pageSize)
    {
        $startIndex = ($pageIndex - 1) * $pageSize;
        $data = $this->createQueryBuilder('p')
            ->select('p.postId, p.cateId, p.title, p.slug, p.sapo, p.publishedDate, p.publishedTimestamp, p.avatar, p.publishedTimestamp, p.authorName, pd.content')
            ->innerJoin(PostsCates::class, 'c', 'WITH', 'c.postId = p.postId')
            ->leftJoin(PostDatas::class, 'pd', 'WITH', 'pd.postId = p.postId')
            ->where('c.cateId IN (:cateId) AND p.focusStatus = :focusStatus ')
            ->setParameters([':cateId' => $cateId, ':focusStatus' => 0])
            ->orderBy('p.publishedDate', 'DESC')
            ->setFirstResult($startIndex)
            ->setMaxResults($pageSize)
            ->getQuery()->getArrayResult();

        return $data;
    }

    /**
     * get data Post in categories by paging ajax
     * author: AnhPT4
     * date:   2018-10-24 03:25 PM
     * @param $cateId
     * @param $Timestamp
     * @param $pageSize
     * @param int $langId
     * @return array
     */
    /*public function getArticleInCateTimestamp($cateId, $postId, $Timestamp, $pageSize, $langId = 0)
    {
        $data = $this->createQueryBuilder('a')
            ->select('a.postId, a.title, a.slug, a.sapo, a.publishedDate,a.publishedTimestamp,a.avatar,c.name')
            ->innerJoin(PostsCates::class, 'c', 'WITH', 'c.postId = a.postId')
            ->where('a.postId < :postId AND c.cateId IN (:cateId) AND a.publishedTimestamp < :currentDate')
            ->setParameters([':postId' => $postId, ':cateId' => $cateId, ':currentDate' => $Timestamp])
            ->groupBy('a.postId')
            ->orderBy('a.publishedTimestamp', 'DESC')
            ->setMaxResults($pageSize)
            ->getQuery()->getArrayResult();

        return $data;
    }*/

    /**
     * get Post Featured In Category
     * author: AnhPT4
     * date: 2018-6-22 14:6 PM
     * @param $cateId
     * @param int $focusStatus
     * @param int $limit
     * @return array
     */
    public function getPostFeaturedInCategory($cateId, $limit, $focusStatus)
    {
        $data = $this->createQueryBuilder('a')
            ->select('a.postId, a.cateId, a.title, a.slug, a.sapo, a.publishedDate,a.publishedTimestamp,a.avatar')
            ->innerJoin(PostsCates::class, 'c', 'WITH', 'c.postId = a.postId')
            ->where('c.cateId IN (:cateId) AND a.focusStatus IN (:focusStatus)')
            ->setParameters([':cateId' => $cateId, ':focusStatus' => $focusStatus])
            ->orderBy('a.publishedTimestamp', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getArrayResult();

        return $data;
    }

    /**
     * Get featured stories
     * author: ThanhDT
     * date:   2018-12-26 11:43 AM
     * @param $limit
     * @return array
     */
    public function getFeaturedStories($limit)
    {
        $data = $this->createQueryBuilder('a')
            ->select('a.postId, a.cateId, a.title, a.slug, a.publishedDate, a.publishedTimestamp, a.avatar')
            ->where('a.focusStatus IN (:focusStatus)')
            ->setParameters([':focusStatus' => [Constants::FOCUS_STATUS_FEATURED_STORY, Constants::FOCUS_STATUS_TRENDING_STORY]])
            ->orderBy('a.publishedTimestamp', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getArrayResult();

        return $data;
    }

    /**
     * Get posts in tag paging
     * author: ThanhDT
     * date:   2018-12-26 12:15 PM
     * @param $tagId
     * @param $limit
     * @param $lastPublishedStamp
     * @param null $excludePostIds
     * @return array
     */
    public function getTagPosts($tagId, $limit, $lastPublishedStamp, $excludePostIds = null)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.postId, p.title, p.slug, p.sapo, p.cateId, p.publishedDate, p.publishedTimestamp, p.avatar, p.postType, c.name cateName, c.slug cateSlug')
            ->innerJoin(PostsTags::class, 'pt', 'WITH', 'pt.postId = p.postId')
            ->innerJoin(Categories::class, 'c', 'WITH', 'c.cateId = p.cateId');
        $parameters = [];
        if ($excludePostIds) {
            $query->where('p.postId NOT IN (:postIds)');//->setParameter(':postIds', $excludePostIds);
            $parameters[':postIds'] = $excludePostIds;
        }
        $query->andWhere('pt.tagId = :tagId AND p.publishedTimestamp <= :currentDate');
        $parameters[':currentDate'] = $lastPublishedStamp;
        $parameters[':tagId'] = $tagId;
        $posts = $query->setParameters($parameters)
            ->orderBy('p.publishedDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();

        return $posts;
    }

    /**
     * get Articles in Tag by paging
     * author: ThanhDT
     * date:   2018-07-11 08:09 PM
     * @param $tagIds : Array
     * @param $pageIndex
     * @param $pageSize
     * @return array
     */
    /*public function getArticleInTagPaging($tagIds, $pageIndex, $pageSize)
    {
        $startIndex = ($pageIndex - 1) * $pageSize;
        $data = $this->createQueryBuilder('a')
            ->select('a.postId, a.title, a.slug, a.sapo, a.publishedDate,a.publishedTimestamp,a.avatar')
            ->innerJoin(PostsTags::class, 'c', 'WITH', 'c.postId = a.postId')
            ->where('c.tagId IN (:tagIds)')
            ->setParameters([':tagIds' => $tagIds])
            ->setFirstResult($startIndex)
            ->orderBy('a.publishedDate', 'DESC')
            ->setMaxResults($pageSize)
            ->getQuery()->getArrayResult();
        
        return $data;
    }*/
    
    /**
     * get Post Featured In Category
     * author: AnhPT4
     * date: 2018-6-22 14:6 PM
     * @param $cateId
     * @param int $status
     * @param int $limit
     * @return array
     */
    public function getPostFeaturedInTags($tagIds, $status, $limit = Constants::LIMIT_FEATURED_STORIES)
    {
        $data = $this->createQueryBuilder('a')
            ->select('a.postId, a.cateId, a.title, a.slug, a.sapo, a.publishedDate,a.publishedTimestamp,a.avatar')
            ->innerJoin(PostsTags::class, 'c', 'WITH', 'c.postId = a.postId')
            ->where('c.tagId IN (:tagIds) AND a.focusStatus = :focusStatus ')
            ->setParameters([':tagIds' => $tagIds, ':focusStatus' => $status])
            ->orderBy('a.publishedDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getArrayResult();
        
        return $data;
    }
    
    /**
     * get Post Tag Count
     * author: AnhPT4
     * date: 2018-8-25 18:8 PM
     * @param $tagId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPostTagCount($tagId){
        $count = $this->createQueryBuilder('a')
            ->select('count(a.postId)')
            ->innerJoin(PostsTags::class, 'c', 'WITH', 'c.postId = a.postId')
            ->where('c.tagId IN (:tagId) AND a.publishedDate <= :currentDate')
            ->setParameters([':tagId' => $tagId, ':currentDate' => new \DateTime()])
            ->getQuery()->getSingleScalarResult();
    
        return $count;
    }
    
    /**
     * Gets the oldest post date.
     *
     * @return     \DateTime  The oldest post date.
     */
    public function getOldestPostDate()
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $selectFields = [
            'p.publishedDate'
        ];
        $oldest = $queryBuilder
            ->select($selectFields)
            ->orderBy('p.publishedDate', 'ASC')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleResult();
        
        return $oldest['publishedDate'];
    }
    
    /**
     * Gets the posts between dates.
     *
     * @param      \DateTime  $start  The start
     * @param      \DateTime  $end    The end
     *
     * @return     array
     */
    public function getPostsBetweenDates($start, $end)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $selectFields = [
            'p.postId',
            'p.cateId',
            'p.avatar',
            'p.slug',
            'p.sapo',
            'p.title',
            'c.slug as slugCate',
            'p.publishedDate'
        ];
        $condition = $queryBuilder
            ->select($selectFields)
            ->innerJoin(Categories::class, 'c', 'WITH', 'c.cateId = p.cateId')
            ->orderBy('p.publishedDate', 'DESC')
            ->where('p.publishedDate >= :startDate')
            ->setParameter('startDate', $start)
            ->andWhere('p.publishedDate <= :endDate')
            ->setParameter('endDate', $end);
        
        $postsQuery = $condition->getQuery();
        $posts = $postsQuery->getArrayResult();
        
        return $posts;
    }

    /**
     * @param $start
     * @param $end
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countPostsBetweenDates($start, $end)
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.postId)')
            ->where('p.publishedDate >= :startDate')
            ->setParameter('startDate', $start)
            ->andWhere('p.publishedDate <= :endDate')
            ->setParameter('endDate', $end)
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * Get latest article paging
     * author: ThanhDT
     * date:   2018-07-11 07:37 PM
     * @param int $pageSize
     * @param int $offset
     * @return array
     * @throws \Exception
     */
    public function getArticleLatest($pageSize = 9, $offset = 0)
    {
        $data = $this->createQueryBuilder('p')
            ->select('p.postId, p.cateId, p.title, p.slug, p.sapo, p.publishedDate, p.avatar, p.publishedTimestamp, p.authorName, pd.content')
            ->leftJoin(PostDatas::class, 'pd', 'WITH', 'pd.postId = p.postId')
            ->where('p.publishedDate <= :currentDate')
            ->setParameters([':currentDate' => new \DateTime()])
            ->orderBy('p.publishedDate', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($pageSize)
            ->getQuery()->getArrayResult();

        return $data;
    }

    /**
     * Get news for RssFeed
     * @param $tagIds
     * @param $limit
     * @return array
     */
    public function getArticleInTagRss($tagIds, $limit)
    {
        $data = $this->createQueryBuilder('p')
            ->select('p.postId, p.cateId, p.authorId, p.title, p.slug, p.avatar, p.sapo, pd.content, pd.cates, pd.tags, p.publishedDate, p.publishedTimestamp, p.seoTitle, p.seoMetadesc, p.authorName')
            ->innerJoin(PostDatas::class, 'pd', 'WITH', 'p.postId = pd.postId')
            ->innerJoin(PostsTags::class, 'c', 'WITH', 'c.postId = p.postId')
            ->where('p.publishedDate <= :currentDate AND c.tagId IN (:tagIds)')
            ->setParameters([':tagIds' => $tagIds, ':currentDate' => new \DateTime()])
            ->orderBy('p.publishedDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getArrayResult();

        return $data;
    }

    /**
     * get Articles in categories by paging
     * author: ThanhDT
     * date:   2018-07-11 08:09 PM
     * @param $authorId
     * @param $pageIndex
     * @param $pageSize
     * @return array
     */
    public function getArticleByAuthorPaging($authorId, $pageIndex, $pageSize)
    {
        $startIndex = ($pageIndex - 1) * $pageSize;
        $data = $this->createQueryBuilder('p')
            ->select('p.postId, p.cateId, p.authorId, p.title, p.slug, p.avatar, p.sapo, pd.content, p.publishedDate, p.publishedTimestamp, p.authorName')
            ->innerJoin(PostsTags::class, 'c', 'WITH', 'c.postId = p.postId')
            ->where('p.authorId = :authorId AND p.publishedDate <= :currentDate')
            ->setParameters([':authorId' => $authorId, ':currentDate' => new \DateTime()])
            ->setFirstResult($startIndex)
            ->orderBy('p.publishedDate', 'DESC')
            ->setMaxResults($pageSize)
            ->getQuery()->getArrayResult();

        return $data;
    }

    /**
     * Get Article by slug
     * author: ThanhDT
     * date:   2018-07-12 11:10 AM
     * @param $slug
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDetailBySlug($slug)
    {
        $data = $this->createQueryBuilder('a')
            ->select('a.postId, a.cateId, a.authorId, a.title, a.slug, c.slug as cateSlug ,a.avatar, a.sapo, pd.content, pd.cates, pd.tags, a.publishedDate, a.reviewId, a.seoTitle, a.seoMetadesc')
            ->innerJoin(PostDatas::class, 'pd', 'WITH', 'a.postId = pd.postId')
            ->leftJoin(Categories::class,'c', 'WITH', 'a.cateId = c.cateId')
            ->where('a.slug = :slug')
            ->setParameters([':slug' => $slug])
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        return $data;
    }

    /************************** Start get Detail data ***************************/
    /**
     * get detail post
     * @param int $postId
     * @param string $slug
     * @return array
     * author : ThangPD
     * date : 2018-25-21 14:25 PM
     */
    public function getDetailPost($postId = 0)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.postId, p.title, p.slug, p.avatar, p.sapo, p.cateId, p.authorId, p.authorName, p.publishedDate, pd.content, pd.contentMobile, pd.contentAmp, pd.cates, pd.tags, pd.relatedPosts, pd.sourceTags, c.name cateName, c.slug cateSlug')
            ->leftJoin(PostDatas::class, 'pd', 'WITH', 'p.postId = pd.postId')
            ->leftJoin(Categories::class, 'c', 'WITH', 'p.cateId = c.cateId')
            ->where('p.postId = '. $postId);
        $result = $query->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * get related post by id
     * @param string $ids
     * @return array
     * author : ThangPD
     * date : 2018-20-22 10:20 AM
     */
    public function getRelatedPosts($ids = "") {
        $query = $this->createQueryBuilder('p')
            ->select('p.postId, p.cateId, p.title, p.slug, p.avatar, p.publishedDate, p.sapo, c.name cateName, c.slug cateSlug')
            ->leftJoin(Categories::class, 'c', 'WITH', 'p.cateId = c.cateId')
            ->where('p.postId IN ('. $ids .')')
            ->setMaxResults(Constants::LIMIT_RELATED)
            ->orderBy('p.publishedDate', 'DESC');
        $result = $query->getQuery()->getArrayResult();

        return $result;
    }

    /**
     * get featured stories by cate id
     * @param int $cateId
     * @return array
     * author : ThangPD
     * date : 2018-21-22 14:21 PM
     */
    /*public function getFeaturedStories($cateId = 0) {
        $query = $this->createQueryBuilder('p')
            ->select('p.postId, p.title, p.slug, p.avatar, p.publishedDate')
            ->where('p.cateId = '. $cateId )
            ->andWhere('p.status = '. Constants::POST_PUBLISHED)
            ->andWhere('p.focusStatus IN ('. Constants::FOCUS_STATUS_FEATURED_STORY .','. Constants::FOCUS_STATUS_TRENDING_STORY .')')
            ->setMaxResults(Constants::LIMIT_FEATURED_STORIES)
            ->orderBy('p.publishedDate', 'DESC');
        $result = $query->getQuery()->getArrayResult();

        return $result;
    }*/

    /**
     * get related articles by cate id
     * @param int $cateId
     * @return array
     * author : ThangPD
     * date : 2018-22-22 14:22 PM
     */
    public function getRelatedArticles($cateId = 0) {
        $query = $this->createQueryBuilder('p')
            ->select('p.postId, p.cateId, p.title, p.slug, p.avatar, p.publishedDate, p.sapo, c.name cateName, c.slug cateSlug')
            ->leftJoin(Categories::class, 'c', 'WITH', 'p.cateId = c.cateId')
            ->where('p.cateId = '. $cateId )
            ->setMaxResults(Constants::LIMIT_RELATED)
            ->orderBy('p.publishedDate', 'DESC');
        $result = $query->getQuery()->getArrayResult();

        return $result;
    }
    /************************** End get Detail data ***************************/
}