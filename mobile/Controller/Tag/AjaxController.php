<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 9:19 AM
 */

namespace Mobile\Controller\Tag;

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
    /**
     * Get stream posts in tag
     * author: ThanhDT
     * date:   2018-12-26 12:19 PM
     * @param Request $request
     * @param Category $cateService
     * @param DataExchange $exchangeService
     * @param CryptUtils $cryptUtils
     * @return JsonResponse
     * @throws \Exception
     */
    public function getTagStreamPosts(Request $request, Category $cateService, DataExchange $exchangeService, CryptUtils $cryptUtils)
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
        $tagId = $curLastInfo['tagId'];
        $tagStreamCacheKey = $this->formatCacheKey(Constants::CACHE_POST_TAG_TIMESTAMP, $tagId,$curLastInfo['lastPublishedTimestamp'], $curLastInfo['lastPostId']);
        if (($tagPostInfo = $cacheService->get($tagStreamCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourcePosts = $em->getRepository(PostPublishes::class)->getTagPosts($tagId, Constants::PAGE_SIZE + 1, $curLastInfo['lastPublishedTimestamp'], [$curLastInfo['lastPostId']]);
            if ($sourcePosts) {
                $showViewMore = false;
                if (count($sourcePosts) > Constants::PAGE_SIZE) {
                    $lastCateStream = $sourcePosts[Constants::PAGE_SIZE - 1];
                    $sourcePosts = array_slice($sourcePosts, 0, Constants::PAGE_SIZE);
                    $lastInfo = [
                        'tagId' => $tagId,
                        'lastPostId' => $lastCateStream['postId'],
                        'lastPublishedTimestamp' => $lastCateStream['publishedTimestamp'],
                        'nextPage' => $curLastInfo['nextPage'] + 1
                    ];
                    $showViewMore = ($curLastInfo['nextPage']-1) % Constants::PAGE_TO_SHOW_VIEW_MORE == 0;
                    $lastInfo = $this->encrypt($cryptUtils, $lastInfo);
                } else {
                    $lastInfo = null;
                }
                $sourcePosts = $exchangeService->ExchangeArrayArticle($sourcePosts, Constants::MOBILE_POST_AVATAR_LIST_SIZE, null, Constants::CUT_NUMBER_CHARACTERS_DEFAULT, Constants::M_CUT_NUMBER_CHARACTERS_LIST_SUMMARY);
                $tagPostInfo = [
                    'catePosts' => $sourcePosts,
                    'loadMoreToken' => $lastInfo,
                    'showViewMore' => $showViewMore
                ];
            } else {
                $tagPostInfo = [
                    'catePosts' => [],
                    'loadMoreToken' => null,
                    'showViewMore' => false
                ];
            }
            $cacheService->set($tagStreamCacheKey, $tagPostInfo, $this->getParameter('cache_time')['hour']);
        }

        $html = $this->renderView('category/widgets/contents.html.twig', [
            'postList' => $tagPostInfo['catePosts']
        ]);

        return new JsonResponse([
            'success' => 1,
            'html' => $html,
            'loadMoreToken' => $tagPostInfo['loadMoreToken'],
            'showViewMore' => $tagPostInfo['showViewMore']
        ]);
    }
}
