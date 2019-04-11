<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/11/2019
 * Time: 2:31 PM
 */

namespace Mobile\Controller\Video;

use Mobile\Controller\BaseController;
use App\Service\DataExchange;
use App\Utils\Constants;
use App\Service\CryptUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Videos;

class AjaxController extends BaseController
{
    /**
     * @param Request $request
     * @param CryptUtils $cryptUtils
     * @param DataExchange $dataExchange
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request, CryptUtils $cryptUtils, DataExchange $dataExchange)
    {
        if($request->isXmlHttpRequest()) {
            $token = $request->get('loadMoreToken');
            $tokenInfo = $cryptUtils->decrypt($token);
            if (!$tokenInfo) {
                return $this->returnJsonResponse([
                    'success' => 0,
                    'html' => '',
                    'loadMoreToken' => null,
                    'tokenInfo' => null
                ]);
            }

            $params = json_decode($tokenInfo, true);
            if (!$params || empty($params['nextPage']) || empty($params['arrIdTopVideo'])) {
                return $this->returnJsonResponse([
                    'success' => 0,
                    'html' => '',
                    'loadMoreToken' => null,
                    'tokenInfo' => null,
                ]);
            }

            $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
            $videoId = (isset($params['videoId']) && !empty($params['videoId'])) ? $params['videoId'] : null;
            $arrIdTopVideo = $params['arrIdTopVideo'];
            $nextPage = $params['nextPage'];
            $startPage = $nextPage * Constants::M_VIDEO_PAGE_LIMIT;


            $listVideoKeyCache = ($videoId) ? $this->formatCacheKey(Constants::CACHE_AJAX_VIDEOS_DETAIL_BOTTOM, $videoId, $nextPage) : $this->formatCacheKey(Constants::CACHE_AJAX_VIDEOS_LIST_BOTTOM, $nextPage);

            if (($listBottomVideos = $cacheService->get($listVideoKeyCache)) === false) {
                $em = $this->getDoctrine()->getManager();
                $sourceVideos = $em->getRepository(Videos::class)->getListVideos($arrIdTopVideo, Constants::M_VIDEO_AJAX_PAGE_LIMIT + 1, $startPage);
                if($sourceVideos) {
                    if(count($sourceVideos) > Constants::M_VIDEO_AJAX_PAGE_LIMIT){
                        $sourceVideos = array_slice($sourceVideos, 0, Constants::M_VIDEO_AJAX_PAGE_LIMIT);
                        $lastInfo = [
                            'arrIdTopVideo' => $arrIdTopVideo,
                            'nextPage' => $nextPage + 1
                        ];
                    } else {
                        $lastInfo = null;
                    }
                    $sourceVideos = $dataExchange->exchangeDataVideoArray(
                        $sourceVideos,
                        Constants::M_LIST_VIDEO_SIZE,
                        Constants::M_TITLE_LIST_VIDEO_LIST
                    );
                    $showViewMore = $nextPage % Constants::PAGE_TO_SHOW_VIEW_MORE == 0 ? true : false;
                    $listBottomVideos = [
                        'videoPosts' => $sourceVideos,
                        'loadMoreToken' => $this->encrypt($cryptUtils, $lastInfo),
                        'showViewMore' => $showViewMore
                    ];
                }else {
                    $listBottomVideos = [
                        'videoPosts' => [],
                        'loadMoreToken' => null,
                        'showViewMore' => false
                    ];
                }
                $cacheService->set($listVideoKeyCache, $listBottomVideos, $this->getParameter('cache_time')['hour']);
            }

            $html = $this->renderView('video/ajax_videos.html.twig', [
                'listBottomVideos' => $listBottomVideos['videoPosts']
            ]);
            return $this->returnJsonResponse([
                'success' => 1,
                'html' => $html,
                'loadMoreToken' => $listBottomVideos['loadMoreToken'],
                'tokenInfo' => $tokenInfo,
                'showViewMore' => $listBottomVideos['showViewMore'],
                'count' => count($listBottomVideos['videoPosts'])
            ]);
        }
    }
}