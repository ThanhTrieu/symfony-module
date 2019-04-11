<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/24/2018
 * Time: 2:20 PM
 */

namespace App\Controller;


use App\Entity\PostPublishes;
use App\Service\CryptUtils;
use App\Service\DataExchange;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends BaseController
{
    /**
     * Get home stream posts
     * @param Request $request
     * @param DataExchange $exchangeService
     * @param CryptUtils $cryptUtils
     * @return JsonResponse
     * @throws \Exception
     */
    public function getHomeStreamPosts(Request $request, DataExchange $exchangeService, CryptUtils $cryptUtils)
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
        $homeStreamCacheKey = sprintf(Constants::CACHE_HOME_STREAM_POST_PAGE, $curLastInfo['nextPage']);
        if (($homeStreamPost = $cacheService->get($homeStreamCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourcePosts = $em->getRepository(PostPublishes::class)->getFocusPosts(Constants::FOCUS_HOME, Constants::HOME_STREAM_PAGE_SIZE + 1, $curLastInfo['lastPublishedTimestamp'], [$curLastInfo['lastPostId']]);
            if ($sourcePosts) {
                $showViewMore = false;
                $featuredStories = $em->getRepository(PostPublishes::class)->getFocusPosts(Constants::FOCUS_FEATURE_STORY, Constants::HOME_FEATURED_STORY_PAGE_SIZE + 1, $curLastInfo['lastFeaturedPublishedTimestamp'], [$curLastInfo['lastFeaturedPostId']]);
                if (count($sourcePosts) > Constants::HOME_STREAM_PAGE_SIZE) {
                    $lastHomeStream = $sourcePosts[Constants::HOME_STREAM_PAGE_SIZE - 1];
                    $sourcePosts = array_slice($sourcePosts, 0, Constants::HOME_STREAM_PAGE_SIZE);
                    $lastInfo = [
                        'lastPostId' => $lastHomeStream['postId'],
                        'lastPublishedTimestamp' => $lastHomeStream['publishedTimestamp'],
                        'nextPage' => $curLastInfo['nextPage'] + 1
                    ];
                    $showViewMore = ($curLastInfo['nextPage']-1) % Constants::PAGE_TO_SHOW_VIEW_MORE == 0;
                    if (count($featuredStories) > Constants::HOME_FEATURED_STORY_PAGE_SIZE) {
                        $featuredStories = array_slice($featuredStories, 0, Constants::HOME_STREAM_PAGE_SIZE);
                        $lastFeaturedStory = $featuredStories[Constants::HOME_FEATURED_STORY_PAGE_SIZE - 1];
                        $lastInfo['lastFeaturedPostId'] = $lastFeaturedStory['postId'];
                        $lastInfo['lastFeaturedPublishedTimestamp'] = $lastFeaturedStory['publishedTimestamp'];
                    } else {
                        $lastInfo['lastFeaturedPublishedTimestamp'] = $lastInfo['lastFeaturedPostId'] = 0;
                    }
                    $lastInfo = $this->encrypt($cryptUtils, $lastInfo);
                } else {
                    $lastInfo = null;
                }
                $homeStreamPosts = $exchangeService->exchangeArrayArticle($sourcePosts, Constants::POST_AVATAR_LIST_SIZE,null,Constants::CUT_NUMBER_CHARACTERS_DEFAULT,Constants::CUT_NUMBER_CHARACTERS_LIST_SUMMARY);
                if ($featuredStories) {
                    $featuredStories = $exchangeService->exchangeArrayArticle($featuredStories, Constants::HOME_FEATURED_STORY_SIZE);
                    $featuredStory = $featuredStories[0];
                } else {
                    $featuredStory = null;
                }

                $homeStreamPost = [
                    'homeStreamPosts' => $homeStreamPosts,
                    'featuredStory' => $featuredStory,
                    'loadMoreToken' => $lastInfo,
                    'showViewMore' => $showViewMore
                ];
            } else {
                $homeStreamPost = [
                    'homeStreamPosts' => [],
                    'featuredStory' => null,
                    'loadMoreToken' => null,
                    'showViewMore' => false
                ];
            }
            $cacheService->set($homeStreamCacheKey, $homeStreamPost, $this->getParameter('cache_time')['hour']);
        }

        $html = $this->renderView('default/widgets/home-stream-posts.html.twig', [
            'homeStreamPosts' => $homeStreamPost['homeStreamPosts'],
            'featuredStory' => $homeStreamPost['featuredStory'],
            'boxSeoPosts' => null,
            'positionInject' => Constants::HOME_FEATURED_POSITION_INJECT
        ]);

        return new JsonResponse([
            'success' => 1,
            'html' => $html,
            'loadMoreToken' => $homeStreamPost['loadMoreToken'],
            'showViewMore' => $homeStreamPost['showViewMore']
        ]);
    }
}