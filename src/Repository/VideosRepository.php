<?php

namespace App\Repository;

use App\Entity\Videos;
use App\Utils\Constants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Videos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Videos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Videos[]    findAll()
 * @method Videos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Videos::class);
    }

    /**
     * Get top hot videos
     * author: ThanhDT
     * date:   2018-12-25 03:21 PM
     * @param $limit
     * @return mixed
     */
    public function getTopHotVideos($limit)
    {
        return $this->createQueryBuilder('v')
            ->select('v.videoId, v.title, v.slug, v.avatar')
            ->where('v.focus = :focus AND v.status = :status')
            ->setParameters([':focus' => Constants::VIDEO_HOME_FOCUS, ':status' => Constants::VIDEO_PUBLISHED_STATUS])
            ->orderBy('v.publishedDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $limit
     * @return mixed
     */
    public function getTopFocusVideos($limit, $startIndex = null)
    {
        $queryBuilder = $this->createQueryBuilder('v');
        $condition = $queryBuilder
            ->select('v.videoId, v.title, v.slug, v.avatar, v.url as sourceVideo, v.createdDate, v.publishedDate')
            ->where('v.status = :status')
            ->setParameters([':status' => Constants::VIDEO_PUBLISHED_STATUS])
            ->orderBy('v.publishedDate', 'DESC');
        if($startIndex){
            $condition->setFirstResult($startIndex);
            $condition->setMaxResults($limit);
        } else {
            $condition->setMaxResults($limit);
        }

        $videos = $condition
            ->getQuery()
            ->getArrayResult();

        return $videos;
    }

    /**
     * @param $ignoreIds
     * @param $limit
     * @param $startIndex
     * @return array|mixed
     */
    public function getListVideos($ignoreIds, $limit, $startIndex)
    {
        $queryBuilder = $this->createQueryBuilder('v');
        $condition = $queryBuilder
            ->select('v.videoId, v.title, v.slug, v.avatar, v.url as sourceVideo, v.createdDate, v.publishedDate')
            ->where('v.status = :status')
            ->setParameters([
                ':status' =>  Constants::VIDEO_PUBLISHED_STATUS
            ])
            ->orderBy('v.publishedDate', 'DESC')
            ->setFirstResult($startIndex)
            ->setMaxResults($limit);
        if ($ignoreIds) {
            $condition->andWhere(
                $condition->expr()->notIn('v.videoId', $ignoreIds)
            );
        }

        $videos = $condition
            ->getQuery()
            ->getArrayResult();

        return $videos;
    }

    /**
     * @param $videoId
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDetailVideosById($videoId)
    {
        return $this->createQueryBuilder('v')
            ->select('v.videoId, v.title, v.slug, v.avatar, v.url as sourceVideo, v.createdDate, v.publishedDate')
            ->where('v.status = :status AND v.videoId = :videoId')
            ->setParameters([':status' => Constants::VIDEO_PUBLISHED_STATUS, ':videoId' => $videoId])
            ->orderBy('v.publishedDate', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $videoId
     * @param $limit
     * @return mixed
     */
    public function getTopFourVideos($videoId, $limit)
    {
        return $this->createQueryBuilder('v')
            ->select('v.videoId, v.title, v.slug, v.avatar, v.url as sourceVideo, v.createdDate, v.publishedDate')
            ->where('v.status = :status AND v.videoId <> :videoId')
            ->setParameters([':status' => Constants::VIDEO_PUBLISHED_STATUS, ':videoId' => $videoId])
            ->orderBy('v.publishedDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
