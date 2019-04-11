<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/25/2018
 * Time: 3:33 PM
 */

namespace App\Controller\Video;


use App\Controller\BaseController;
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

        // list top 16 videos
        $focusVideosCacheKey = sprintf(Constants::CACHE_VIDEO_FOCUS_LIST);
        $arrIdTopVideo = [];
        if (($focusVideos = $cacheService->get($focusVideosCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $dataFocusVideos = $em->getRepository(Videos::class)->getTopFocusVideos(
                Constants::VIDEO_TOP_PAGE_LIMIT,
                Constants::START_VIDEO_PAGE
            );
            $focusVideos = [
                'first' => false,
                'list' => [],
                'play_list' => []
            ];

            if($dataFocusVideos){
                foreach ($dataFocusVideos as $key => $item) {
                    $arrIdTopVideo[] = $item['videoId'];
                    $focusVideos['play_list'][]=  $dataExchange->exchangePlayVideoList($item);
                    if($key === 0){
                        $focusVideos['first'] = $dataExchange->exchangeDataVideoList(
                            $item,
                            Constants::IMAGE_FIRST_VIDEO_TOP,
                            Constants::TITLE_LENGTH_VIDEO_FIRST
                        );
                    } else {
                        $focusVideos['list'][] = $dataExchange->exchangeDataVideoList(
                            $item,
                            Constants::IMAGE_LIST_VIDEO_TOP,
                            Constants::TITLE_LENGTH_VIDEO_LIST
                        );
                    }
                }
            }
            $cacheService->set($focusVideosCacheKey, $focusVideos, $this->getParameter('cache_time')['hour']);
        }

        // list 12 videos bottom
        $listVideoKeyCache = sprintf(Constants::CACHE_VIDEOS_LIST_BOTTOM);
        if (($listBottomVideos = $cacheService->get($listVideoKeyCache)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceVideos = $em->getRepository(Videos::class)->getListVideos($arrIdTopVideo, Constants::VIDEO_PAGE_LIMIT + 1, Constants::START_VIDEO_PAGE);

            if($sourceVideos) {
                if(count($sourceVideos) > Constants::VIDEO_PAGE_LIMIT){
                    $sourceVideos = array_slice($sourceVideos, 0, Constants::VIDEO_PAGE_LIMIT);
                    $lastInfo = [
                        'arrIdTopVideo' => $arrIdTopVideo,
                        'nextPage' => Constants::START_VIDEO_PAGE + 1
                    ];
                } else {
                    $lastInfo = null;
                }
                $sourceVideos = $dataExchange->exchangeDataVideoArray(
                    $sourceVideos,
                    Constants::POST_AVATAR_LIST_SIZE,
                    null
                );
                $listBottomVideos = [
                    'videoPosts' => $sourceVideos,
                    'loadMoreToken' => $this->encrypt($cryptUtils, $lastInfo)
                ];
            }else {
                $listBottomVideos = [
                    'videoPosts' => [],
                    'loadMoreToken' => null
                ];
            }
            $cacheService->set($listVideoKeyCache, $listBottomVideos, $this->getParameter('cache_time')['hour']);
        }

        $seo = [
            'title' => 'Latest Game Videos, Trailers, Gameplays, Event Converage in Gaming World',
            'description' => 'Watch the latest video game trailers, video reviews, gameplay videos, game demos, event coverage, interviews, and more. Check out the latest video games on PC, Consoles and Mobile.',
            'og_type' => 'object'
        ];
        // render view
        $response = $this->render('video/list_video.html.twig',[
            'firstVideos' => $focusVideos['first'],
            'listTopVideo' => $focusVideos['list'],
            'listBottomVideos' => $listBottomVideos['videoPosts'],
            'loadMoreToken' => $listBottomVideos['loadMoreToken'],
            'cateSlug' => 'video',
            'seo' => $seo
        ]);

        // Set cache page
        $this->addCachePage($request, $response);
        return $response;
    }

    /**
     * @param $videoSlug
     * @param $videoId
     * @param DataExchange $dataExchange
     * @param CryptUtils $cryptUtils
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function videoDetail($videoSlug, $videoId, DataExchange $dataExchange, CryptUtils $cryptUtils, Request $request)
    {
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $arrIdTopVideo = [];
        $videoId = (int) $videoId;

        $detailVideosCacheKey = sprintf(Constants::CACHE_VIDEO_DETAIL_ID, $videoId);
        if (($detailVideos = $cacheService->get($detailVideosCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceVideos = $em->getRepository(Videos::class)->getDetailVideosById($videoId);
            if($sourceVideos){
                $detailVideos = $dataExchange->exchangeDataVideoList(
                    $sourceVideos,
                    Constants::IMAGE_FIRST_VIDEO_TOP,
                    null
                );
            } else {
                $detailVideos = [];
            }
            $cacheService->set($detailVideosCacheKey, $detailVideos, $this->getParameter('cache_time')['hour']);
        }

        // get 15 video for right top
        $topFourVideoKey = sprintf(Constants::CACHE_VIDEO_FOUR_RIGHT, $videoId);
        array_push($arrIdTopVideo, $videoId);
        if (($fourVideos = $cacheService->get($topFourVideoKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceFourVideos = $em->getRepository(Videos::class)->getTopFourVideos($videoId, Constants::FOUR_TOP_RIGHT_VIDEO);
           if($sourceFourVideos){
               $arrIdTopVideo = array_column($sourceFourVideos,'videoId');
               $fourVideos = $dataExchange->exchangeDataVideoArray(
                   $sourceFourVideos,
                   Constants::IMAGE_LIST_VIDEO_TOP,
                   Constants::TITLE_LENGTH_VIDEO_LIST
               );
           } else {
               $fourVideos = [];
           }
            $cacheService->set($topFourVideoKey, $fourVideos, $this->getParameter('cache_time')['hour']);
        }

        // list 12 videos bottom
        $listVideoKeyCache = sprintf(Constants::CACHE_VIDEOS_DETAIL_BOTTOM, $videoId);
        if (($listBottomVideos = $cacheService->get($listVideoKeyCache)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceVideos = $em->getRepository(Videos::class)->getListVideos($arrIdTopVideo, Constants::VIDEO_PAGE_LIMIT + 1, Constants::START_VIDEO_PAGE);
            if($sourceVideos) {
                if(count($sourceVideos) > Constants::VIDEO_PAGE_LIMIT){
                    $sourceVideos = array_slice($sourceVideos, 0, Constants::VIDEO_PAGE_LIMIT);
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
                    Constants::POST_AVATAR_LIST_SIZE,
                    null
                );
                $listBottomVideos = [
                    'videoPosts' => $sourceVideos,
                    'loadMoreToken' => $this->encrypt($cryptUtils, $lastInfo)
                ];
            }else {
                $listBottomVideos = [
                    'videoPosts' => [],
                    'loadMoreToken' => null
                ];
            }
            $cacheService->set($listVideoKeyCache, $listBottomVideos, $this->getParameter('cache_time')['hour']);
        }
        $videoUrl = $this->generateUrl('video_detail', ['videoSlug' => $videoSlug, 'videoId' => $videoId]);
        $seo = $this->buildPagingMeta($videoUrl, $detailVideos['title'], 'Hot video ' . $detailVideos['title']);
        // render view
        $response = $this->render('video/list_video.html.twig',[
            'firstVideos' => $detailVideos,
            'listTopVideo' => $fourVideos,
            'listBottomVideos' => $listBottomVideos['videoPosts'],
            'loadMoreToken' => $listBottomVideos['loadMoreToken'],
            'cateSlug' => 'video',
            'seo' => $seo
        ]);

        // Set cache page
        $this->addCachePage($request, $response);
        return $response;
    }
}