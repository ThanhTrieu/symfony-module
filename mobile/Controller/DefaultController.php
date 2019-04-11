<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 12:43 AM
 */

namespace Mobile\Controller;

use App\Entity\PostPublishes;
use App\Entity\Tags;
use App\Service\CryptUtils;
use App\Service\DataExchange;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    /**
     * @param Request $request
     * @param DataExchange $exchangeService
     * @param CryptUtils $cryptUtils
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(Request $request, DataExchange $exchangeService, CryptUtils $cryptUtils)
    {
        // Get Home feature posts
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $focusCacheKey = $this->formatCacheKey(Constants::CACHE_HOME_FEATURED_POST);
        if (($featuredPosts = $cacheService->get($focusCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourcePosts = $em->getRepository(PostPublishes::class)
                ->getTopPostsInGroupBox(
                    Constants::GROUP_BOX_HOME_FEATURE,
                    Constants::M_HOME_TOP_FEATURED_POSTS_LIMIT
                );
            if ($sourcePosts) {
                $sourcePosts = $exchangeService->exchangeArrayArticleStream(
                    $sourcePosts,
                    Constants::M_OTHER_TOP_FEATURED_SIZE,
                    Constants::M_FIRST_TOP_FEATURED_SIZE,
                    Constants::M_CUT_NUMBER_CHARACTERS_LIST_SUMMARY,
                    Constants::M_CUT_NUMBER_CHARACTERS_FEATURED_TOP_SUMMARY
                );
                
                $topFeaturedPost = $sourcePosts[0];
                $otherFeaturedPosts = array_slice($sourcePosts, 1);
                $featuredPostIds = array_column($sourcePosts, 'id');
                
                $featuredPosts = [
                    'topFeaturedPost' => $topFeaturedPost,
                    'otherFeaturedPosts' => $otherFeaturedPosts,
                    'featuredPostIds' => $featuredPostIds
                ];
            } else {
                $featuredPosts = [];
                $featuredPostIds = [];
            }
            $cacheService->set($focusCacheKey, $featuredPosts, $this->getParameter('cache_time')['hour']);
        } else {
            $featuredPostIds = $featuredPosts['featuredPostIds'];
        }
        
        // Get Home stream
        $curHomeSteamPage = Constants::START_PAGE;
        $homeStreamCacheKey = $this->formatCacheKey(Constants::CACHE_HOME_STREAM_POST_PAGE, $curHomeSteamPage);
        if (($homeStreamPost = $cacheService->get($homeStreamCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourcePosts = $em->getRepository(PostPublishes::class)
                ->getFocusPosts(
                    Constants::FOCUS_HOME,
                    Constants::M_HOME_STREAM_FIRST_PAGE_SIZE + 1,
                    time(),
                    $featuredPostIds
                );
            if ($sourcePosts) {
                $featuredStories = $em->getRepository(PostPublishes::class)
                    ->getFocusPosts(
                        Constants::FOCUS_FEATURE_STORY,
                        Constants::HOME_FEATURED_STORY_FIRST_PAGE_SIZE + 1,
                        time(),
                        $featuredPostIds
                    );
                if (count($sourcePosts) > Constants::M_HOME_STREAM_FIRST_PAGE_SIZE) {
                    $lastHomeStream = $sourcePosts[Constants::M_HOME_STREAM_FIRST_PAGE_SIZE - 1];
                    $sourcePosts = array_slice($sourcePosts, 0, Constants::M_HOME_STREAM_FIRST_PAGE_SIZE);
                    $lastInfo = [
                        'lastPostId' => $lastHomeStream['postId'],
                        'lastPublishedTimestamp' => $lastHomeStream['publishedTimestamp'],
                        'nextPage' => $curHomeSteamPage + 1
                    ];
                    if (count($featuredStories) > Constants::HOME_FEATURED_STORY_FIRST_PAGE_SIZE) {
                        $featuredStories = array_slice(
                            $featuredStories,
                            0,
                            Constants::HOME_FEATURED_STORY_FIRST_PAGE_SIZE
                        );
                        $lastFeaturedStory = $featuredStories[Constants::HOME_FEATURED_STORY_FIRST_PAGE_SIZE - 1];
                        $lastInfo['lastFeaturedPostId'] = $lastFeaturedStory['postId'];
                        $lastInfo['lastFeaturedPublishedTimestamp'] = $lastFeaturedStory['publishedTimestamp'];
                    } else {
                        $lastInfo['lastFeaturedPublishedTimestamp'] = $lastInfo['lastFeaturedPostId'] = 0;
                    }
                } else {
                    $lastInfo = null;
                }
                
                $featuredStories = $exchangeService->exchangeArrayArticleStream(
                    $featuredStories,
                    Constants::M_HOME_FEATURED_STORY_SIZE,
                    Constants::CUT_NUMBER_CHARACTERS_DEFAULT,
                    Constants::M_CUT_NUMBER_CHARACTERS_LIST_SUMMARY
                );
                $sourcePosts = $exchangeService->exchangeArrayArticleStream(
                    $sourcePosts,
                    Constants::M_HOME_AVATAR_LIST_SIZE,
                    null,
                    Constants::CUT_NUMBER_CHARACTERS_DEFAULT,
                    Constants::M_CUT_NUMBER_CHARACTERS_FEATURED_TOP_TITLE_V2
                );
                
                if (count($sourcePosts) > Constants::M_HOME_STREAM_PAGE_SIZE) {
                    $firstHomeStreamPosts = array_slice($sourcePosts, 0, Constants::M_HOME_STREAM_PAGE_SIZE);
                    $secondHomeStreamPosts = array_slice($sourcePosts, Constants::M_HOME_STREAM_PAGE_SIZE);
                } else {
                    $firstHomeStreamPosts = $sourcePosts;
                    $secondHomeStreamPosts = [];
                }
                if (count($featuredStories) > Constants::HOME_FEATURED_STORY_PAGE_SIZE) {
                    $firstFeaturedStory = $featuredStories[0];
                    $secondFeaturedStory = $featuredStories[1];
                } else {
                    $firstFeaturedStory = count($featuredStories) ? $featuredStories[0] : null;
                    $secondFeaturedStory = null;
                }
                
                $homeStreamPost = [
                    'firstHomeStreamPosts' => $firstHomeStreamPosts,
                    'secondHomeStreamPosts' => $secondHomeStreamPosts,
                    'firstFeaturedStory' => $firstFeaturedStory,
                    'secondFeaturedStory' => $secondFeaturedStory,
                    'loadMoreToken' => $this->encrypt($cryptUtils, $lastInfo)
                ];
            } else {
                $homeStreamPost = [
                    'firstHomeStreamPosts' => [],
                    'secondHomeStreamPosts' => [],
                    'firstFeaturedStory' => null,
                    'secondFeaturedStory' => null,
                    'loadMoreToken' => null
                ];
            }
            $cacheService->set($homeStreamCacheKey, $homeStreamPost, $this->getParameter('cache_time')['hour']);
        }
        // Get Top game hot
        $gameCacheKey = $this->formatCacheKey(Constants::CACHE_HOME_TAGS_GAME);
        if (($gameTags = $cacheService->get($gameCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceTags = $em->getRepository(Tags::class)->getGameTags(Constants::HOME_TRENDING_TAGS_LIMIT);
            if ($sourceTags) {
                $gameTags = $exchangeService->exchangeTags(
                    $sourceTags,
                    Constants::M_SIZE_IMG_HOME_TRENDING_TAGS
                );
            } else {
                $gameTags = [];
            }
            $cacheService->set($gameCacheKey, $gameTags, $this->getParameter('cache_time')['hour']);
        }

        
        // Get trending stories
        $trendingStoriesCacheKey = $this->formatCacheKey(Constants::CACHE_HOME_TRENDING_STORY);
        if (($trendingStories = $cacheService->get($trendingStoriesCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceTrendingStories = $em->getRepository(PostPublishes::class)
                ->getFocusPosts(
                    Constants::FOCUS_STATUS_TRENDING_STORY,
                    Constants::M_HOME_FEATURED_STORY_PAGE_SIZE,
                    time()
                );
            if ($sourceTrendingStories) {
                $trendingStories = $exchangeService->exchangeArrayArticle(
                    $sourceTrendingStories,
                    Constants::M_HOME_FEATURED_STORY_SIZE,
                    Constants::CUT_NUMBER_CHARACTERS_DEFAULT,
                    Constants::M_CUT_NUMBER_CHARACTERS_LIST_SUMMARY
                );
            } else {
                $trendingStories = [];
            }
            $cacheService->set($trendingStoriesCacheKey, $trendingStories, $this->getParameter('cache_time')['hour']);
        }
        $fiveFisrtHomeStreamPostOne = array_slice(
            $homeStreamPost['firstHomeStreamPosts'],
            0,
            Constants::HOME_FEATURED_POSITION_INJECT
        );
        $fiveFisrtHomeStreamPostTwo = array_slice(
            $homeStreamPost['firstHomeStreamPosts'],
            Constants::HOME_FEATURED_POSITION_INJECT
        );

        $fiveSecondHomeStreamPostOne = array_slice(
            $homeStreamPost['secondHomeStreamPosts'],
            0,
            Constants::HOME_FEATURED_POSITION_INJECT
        );
        $fiveSecondHomeStreamPostTwo = array_slice(
            $homeStreamPost['secondHomeStreamPosts'],
            Constants::HOME_FEATURED_POSITION_INJECT
        );

        $homeUrl = $this->generateUrl('index');
        $seo = $this->buildPagingMeta($homeUrl, $this->getParameter('home_title'), $this->getParameter('site_desc'));
        $seo['is_home'] = true;

        $response = $this->render('default/index.html.twig', [
            'featuredPosts' => $featuredPosts,
            'firstHomeStreamPosts' => [
                'first' => $fiveFisrtHomeStreamPostOne,
                'second' => $fiveFisrtHomeStreamPostTwo
            ],
            'secondHomeStreamPosts' => [
                'first' => $fiveSecondHomeStreamPostOne,
                'second' => $fiveSecondHomeStreamPostTwo
            ],
            'firstFeaturedStory' => $homeStreamPost['firstFeaturedStory'],
            'secondFeaturedStory' => $homeStreamPost['secondFeaturedStory'],
            'positionInject' => Constants::HOME_FEATURED_POSITION_INJECT,
            'loadMoreToken' => $homeStreamPost['loadMoreToken'],
            'gameTags' => $gameTags,
            'trendingStories' => $trendingStories,
            'cateSlug' => 'home',
            'seo' => $seo
        ]);

        $this->addCachePage($request, $response);

        return $response;
    }
    
    /**
     * author: AnhPT4
     * date: 2018-23-21 18:23 PM
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function _404page()
    {
        $page404Url = $this->generateUrl('page404');
        $seo = $this->buildPagingMeta($page404Url, 'Page not found');
        $response = $this->render('page-404.html.twig', array('seo' => $seo));
        $response->setStatusCode(404);
        return $response;
    }

    public function termCondition()
    {
        $pageTermUrl = $this->generateUrl('term_condition');
        $seo = $this->buildPagingMeta($pageTermUrl, 'Term Condition');
        $response = $this->render('default/term-condition.html.twig', array(
            'seo' => $seo,
            'cateSlug' => 'home'
        ));
        return $response;
    }
}
