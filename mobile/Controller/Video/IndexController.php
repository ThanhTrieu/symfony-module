<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/25/2018
 * Time: 3:33 PM
 */

namespace Mobile\Controller\Video;


use Mobile\Controller\BaseController;
use App\Service\DataExchange;
use App\Utils\Constants;
use App\Service\CryptUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Videos;

class IndexController extends BaseController
{
    /**
     * @param DataExchange $dataExchange
     * @param CryptUtils $cryptUtils
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function videoList(DataExchange $dataExchange, CryptUtils $cryptUtils, Request $request)
    {
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        // get top 7 video
        $topOneVideosCacheKey = $this->formatCacheKey(Constants::CACHE_VIDEO_FOCUS_LIST);
        if (($dataTopListVideos = $cacheService->get($topOneVideosCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $dataFocusVideos = $em->getRepository(Videos::class)->getTopFocusVideos(Constants::M_HOME_TOP_VIDEO_LIMIT + 1, Constants::START_VIDEO_PAGE);
            if ($dataFocusVideos) {
                if (count($dataFocusVideos) > Constants::M_HOME_TOP_VIDEO_LIMIT) {
                    $arrIdTopVideo = array_column($dataFocusVideos, 'videoId');
                    $dataFocusVideos = array_slice($dataFocusVideos, 0, Constants::M_HOME_TOP_VIDEO_LIMIT);
                    $lastInfo = [
                        'arrIdTopVideo' => $arrIdTopVideo,
                        'nextPage' => Constants::START_VIDEO_PAGE + 1
                    ];
                } else {
                    $lastInfo = null;
                }

                $focusVideos = [
                    'first' => false,
                    'list' => [],
                ];
                foreach ($dataFocusVideos as $key => $item) {
                    if ($key === 0) {
                        $focusVideos['first'] = $dataExchange->exchangeDataVideoList(
                            $item,
                            Constants::M_FIRST_TOP_VIDEO_SIZE,
                            Constants::M_TITLE_FIRST_VIDEO_LIMIT
                        );
                    } else {
                        $focusVideos['list'][] = $dataExchange->exchangeDataVideoList(
                            $item,
                            Constants::M_LIST_VIDEO_SIZE,
                            Constants::M_TITLE_LIST_VIDEO_LIST
                        );
                    }
                }
                $dataTopListVideos = [
                    'videoPosts' => $focusVideos,
                    'loadMoreToken' => $this->encrypt($cryptUtils, $lastInfo)
                ];
            } else {
                $dataTopListVideos = [
                    'videoPosts' => [
                        'first' => false,
                        'list' => [],
                    ],
                    'loadMoreToken' => null
                ];
            }
            $cacheService->set($topOneVideosCacheKey, $dataTopListVideos, $this->getParameter('cache_time')['hour']);
        }

        $idVideoYoutube = null;
        if ($dataTopListVideos['videoPosts']['first']) {
            $idVideoYoutube = $dataTopListVideos['videoPosts']['first']['source_video'];
            if (strpos($idVideoYoutube, 'watch?v=') !== false) {
                $idVideoYoutube = strstr($idVideoYoutube, 'watch?v=');
                $idVideoYoutube = ltrim($idVideoYoutube, 'watch?v=');
            }
        }
        $videoUrl = $this->generateUrl('video');
        $title = 'Latest Game Videos, Trailers, Gameplays, Event Converage in Gaming World';
        $desc = 'Watch the latest video game trailers, video reviews, gameplay videos, game demos, event coverage, interviews, and more. Check out the latest video games on PC, Consoles and Mob';
        $seo = $this->buildPagingMeta($videoUrl, $title, $desc);

        //render view
        $response = $this->render('video/list-videos.html.twig', [
            'firstVideos' => $dataTopListVideos['videoPosts']['first'],
            'listTopVideo' => $dataTopListVideos['videoPosts']['list'],
            'loadMoreToken' => $dataTopListVideos['loadMoreToken'],
            'cateSlug' => 'video',
            'videoId' => null,
            'idVideoYoutube' => $idVideoYoutube,
            'domain' => $this->getParameter('mobile'),
            'seo' => $seo
        ]);

        // Set cache page
        $this->addCachePage($request, $response);
        return $response;
    }

    /**
     * @param $videoId
     * @param DataExchange $dataExchange
     * @param CryptUtils $cryptUtils
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function detail($videoId, DataExchange $dataExchange, CryptUtils $cryptUtils, Request $request)
    {
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $arrIdTopVideo = [];
        $videoId = (int)$videoId;

        $detailVideosCacheKey = $this->formatCacheKey(Constants::CACHE_VIDEO_DETAIL_ID, $videoId);
        if (($detailVideos = $cacheService->get($detailVideosCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceVideos = $em->getRepository(Videos::class)->getDetailVideosById($videoId);
            if ($sourceVideos) {
                $detailVideos = $dataExchange->exchangeDataVideoList(
                    $sourceVideos,
                    Constants::M_FIRST_TOP_VIDEO_SIZE,
                    Constants::M_TITLE_FIRST_VIDEO_LIMIT
                );
            } else {
                $detailVideos = [];
            }
            $cacheService->set($detailVideosCacheKey, $detailVideos, $this->getParameter('cache_time')['hour']);
        }

        array_push($arrIdTopVideo, $videoId);
        // list 6 videos bottom
        $listVideoKeyCache = $this->formatCacheKey(Constants::CACHE_VIDEOS_DETAIL_BOTTOM, $videoId);
        if (($listBottomVideos = $cacheService->get($listVideoKeyCache)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceVideos = $em->getRepository(Videos::class)->getListVideos($arrIdTopVideo, Constants::M_VIDEO_AJAX_PAGE_LIMIT + 1, Constants::START_VIDEO_PAGE);
            if ($sourceVideos) {
                if (count($sourceVideos) > Constants::M_VIDEO_AJAX_PAGE_LIMIT) {
                    $sourceVideos = array_slice($sourceVideos, 0, Constants::M_VIDEO_AJAX_PAGE_LIMIT);
                    $lastInfo = [
                        'videoId' => $videoId,
                        'arrIdTopVideo' => $arrIdTopVideo,
                        'nextPage' => Constants::START_VIDEO_PAGE + 1
                    ];
                } else {
                    $lastInfo = null;
                }
                $sourceVideos = $dataExchange->exchangeDataVideoArray(
                    $sourceVideos,
                    Constants::M_LIST_VIDEO_SIZE,
                    Constants::M_TITLE_LIST_VIDEO_LIST
                );
                $listBottomVideos = [
                    'videoPosts' => $sourceVideos,
                    'loadMoreToken' => $this->encrypt($cryptUtils, $lastInfo)
                ];
            } else {
                $listBottomVideos = [
                    'videoPosts' => [],
                    'loadMoreToken' => null
                ];
            }
            $cacheService->set($listVideoKeyCache, $listBottomVideos, $this->getParameter('cache_time')['hour']);
        }

        $idVideoYoutube = null;
        if ($detailVideos) {
            $idVideoYoutube = $detailVideos['source_video'];
            if (strpos($idVideoYoutube, 'watch?v=') !== false) {
                $idVideoYoutube = strstr($idVideoYoutube, 'watch?v=');
                $idVideoYoutube = ltrim($idVideoYoutube, 'watch?v=');
            }
        }
        $seo = [
            'title' => $detailVideos['title'],
            'description' => 'Hot video ' . $detailVideos['title'],
            'og_type' => 'object'
        ];

        // render view
        $response = $this->render('video/list-videos.html.twig', [
            'firstVideos' => $detailVideos,
            'listTopVideo' => $listBottomVideos['videoPosts'],
            'loadMoreToken' => $listBottomVideos['loadMoreToken'],
            'cateSlug' => 'video',
            'videoId' => $videoId,
            'idVideoYoutube' => $idVideoYoutube,
            'domain' => $this->getParameter('mobile'),
            'seo' => $seo
        ]);

        // Set cache page
        $this->addCachePage($request, $response);
        return $response;
    }
}