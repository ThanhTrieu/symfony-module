<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 9:19 AM
 */

namespace Mobile\Controller\Category;

use Mobile\Controller\BaseController;
use App\Entity\PostPublishes;
use App\Service\Category;
use App\Service\CryptUtils;
use App\Service\DataExchange;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends BaseController
{
    public function getCateStreamPosts(Request $request, Category $cateService, DataExchange $exchangeService, CryptUtils $cryptUtils)
    {
        $token = $request->query->get('loadMoreToken');
        if (!$token) {
            return new JsonResponse([
                'success' => 0
            ]);
        }
        $curLastInfo = $this->decrypt($cryptUtils, $token);
        if (!$curLastInfo) {
            return new JsonResponse([
                'success' => 0
            ]);
        }
        
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        // Get Home stream
        $cateId = $curLastInfo['cateId'];
        $cateStreamCacheKey = $this->formatCacheKey(Constants::CACHE_CATEGORY_LASTEST_TIMESTAMP, $cateId,$curLastInfo['lastPublishedTimestamp'], $curLastInfo['lastPostId']);
        if (($catePostInfo = $cacheService->get($cateStreamCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $cate = $cateService->getCateById($cateId);
            if (!$cate) {
                return new JsonResponse([
                    'success' => 0
                ]);
            }
            if ($cate['parentId'] == 0) {
                $cateIdList = $cateService->getCategoryParentId($cateId);
            } else {
                $cateIdList = $cateId;
            }
            $sourcePosts = $em->getRepository(PostPublishes::class)->getCatePosts($cateIdList, Constants::MOBILE_PAGE_SIZE + 1, $curLastInfo['lastPublishedTimestamp'], [$curLastInfo['lastPostId']]);
            if ($sourcePosts) {
                $showViewMore = false;
                if (count($sourcePosts) > Constants::MOBILE_PAGE_SIZE) {
                    $lastCateStream = $sourcePosts[Constants::MOBILE_PAGE_SIZE - 1];
                    $sourcePosts = array_slice($sourcePosts, 0, Constants::MOBILE_PAGE_SIZE);
                    $lastInfo = [
                        'cateId' => $cateId,
                        'lastPostId' => $lastCateStream['postId'],
                        'lastPublishedTimestamp' => $lastCateStream['publishedTimestamp'],
                        'nextPage' => $curLastInfo['nextPage'] + 1
                    ];
                    $showViewMore = ($curLastInfo['nextPage'] - 1) % Constants::PAGE_TO_SHOW_VIEW_MORE == 0;
                    $lastInfo = $this->encrypt($cryptUtils, $lastInfo);
                } else {
                    $lastInfo = null;
                }
                $sourcePosts = $exchangeService->ExchangeArrayArticle($sourcePosts, Constants::MOBILE_POST_AVATAR_LIST_SIZE, null, Constants::CUT_NUMBER_CHARACTERS_DEFAULT, Constants::M_CUT_NUMBER_CHARACTERS_LIST_SUMMARY);
                $catePostInfo = [
                    'catePosts' => $sourcePosts,
                    'loadMoreToken' => $lastInfo,
                    'showViewMore' => $showViewMore
                ];
            } else {
                $catePostInfo = [
                    'catePosts' => [],
                    'loadMoreToken' => null,
                    'showViewMore' => false
                ];
            }
            $cacheService->set($cateStreamCacheKey, $catePostInfo, $this->getParameter('cache_time')['hour']);
        }
        
        $html = $this->renderView('category/widgets/contents.html.twig', [
            'postList' => $catePostInfo['catePosts']
        ]);
        
        return new JsonResponse([
            'success' => 1,
            'html' => $html,
            'loadMoreToken' => $catePostInfo['loadMoreToken'],
            'showViewMore' => $catePostInfo['showViewMore']
        ]);
    }
}