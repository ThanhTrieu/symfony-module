<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 12:43 AM
 */

namespace App\Controller;

use App\Entity\GroupBoxItems;
use App\Entity\PostPublishes;
use App\Entity\Tags;
use App\Entity\Videos;
use App\Service\CryptUtils;
use App\Service\DataExchange;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    /**
     * Home page action
     * author: ThanhDT
     * date:   2018-12-04 09:05 AM
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
        $focusCacheKey = Constants::CACHE_HOME_FEATURED_POST;
        if (($featuredPosts = $cacheService->get($focusCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourcePosts = $em->getRepository(PostPublishes::class)
                ->getTopPostsInGroupBox(Constants::GROUP_BOX_HOME_FEATURE, Constants::HOME_TOP_FEATURED_POSTS_LIMIT);
            if ($sourcePosts) {
                $sourcePosts = $exchangeService->exchangeArrayArticle(
                    $sourcePosts,
                    Constants::OTHER_TOP_FEATURED_SIZE,
                    Constants::FIRST_TOP_FEATURED_SIZE
                );
                $topFeaturedPost = $sourcePosts[0];
                $otherFeaturedPosts = array_slice($sourcePosts, 1);
                $featuredPostIds = array_column($sourcePosts, 'postId');

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
        $homeStreamCacheKey = sprintf(Constants::CACHE_HOME_STREAM_POST_PAGE, $curHomeSteamPage);
        if (($homeStreamPost = $cacheService->get($homeStreamCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourcePosts = $em->getRepository(PostPublishes::class)
                ->getFocusPosts(
                    Constants::FOCUS_HOME,
                    Constants::HOME_STREAM_FIRST_PAGE_SIZE + 1,
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
                if (count($sourcePosts) > Constants::HOME_STREAM_FIRST_PAGE_SIZE) {
                    $lastHomeStream = $sourcePosts[Constants::HOME_STREAM_FIRST_PAGE_SIZE - 1];
                    $sourcePosts = array_slice($sourcePosts, 0, Constants::HOME_STREAM_FIRST_PAGE_SIZE);
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
//                $featuredStories = $exchangeService->exchangeArrayArticle($featuredStories, Constants::HOME_FEATURED_STORY_SIZE);
//                $sourcePosts = $exchangeService->exchangeArrayArticle($sourcePosts, Constants::POST_AVATAR_LIST_SIZE);
                $featuredStories = $exchangeService->exchangeArrayArticleStream(
                    $featuredStories,
                    Constants::HOME_FEATURED_STORY_SIZE
                );
                $sourcePosts = $exchangeService->exchangeArrayArticleStream(
                    $sourcePosts,
                    Constants::POST_AVATAR_LIST_SIZE,
                    null,
                    Constants::CUT_NUMBER_CHARACTERS_DEFAULT,
                    Constants::CUT_NUMBER_CHARACTERS_LIST_SUMMARY
                );
                if (count($sourcePosts) > Constants::HOME_STREAM_PAGE_SIZE) {
                    $firstHomeStreamPosts = array_slice($sourcePosts, 0, Constants::HOME_STREAM_PAGE_SIZE);
                    $secondHomeStreamPosts = array_slice($sourcePosts, Constants::HOME_STREAM_PAGE_SIZE);
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

        // Get box links
        $boxLinkCacheKey = Constants::CACHE_HOME_BOX_LINK;
        if (($boxLinks = $cacheService->get($boxLinkCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $boxLinks = $em->getRepository(GroupBoxItems::class)
                ->getBoxLinks(Constants::GROUP_BOX_SEO, Constants::HOME_BOX_LINK_LIMIT);
            $cacheService->set($boxLinkCacheKey, $boxLinks, $this->getParameter('cache_time')['hour']);
        }

        // Get trending tags
        $trendingCacheKey = Constants::CACHE_HOME_TAGS_TRENDING;
        if (($trendingTags = $cacheService->get($trendingCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceTags = $em->getRepository(Tags::class)->getTrendingTags(Constants::HOME_TRENDING_TAGS_LIMIT);
            if ($sourceTags) {
                $trendingTags = $exchangeService->exchangeTags($sourceTags);
            } else {
                $trendingTags = [];
            }
            $cacheService->set($trendingCacheKey, $trendingTags, $this->getParameter('cache_time')['hour']);
        }

        // Get game tags
        $gameCacheKey = Constants::CACHE_HOME_TAGS_GAME;
        if (($gameTags = $cacheService->get($gameCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceTags = $em->getRepository(Tags::class)->getGameTags(Constants::HOME_TRENDING_TAGS_LIMIT);
            if ($sourceTags) {
                $gameTags = $exchangeService->exchangeTags($sourceTags);
            } else {
                $gameTags = [];
            }
            $cacheService->set($gameCacheKey, $gameTags, $this->getParameter('cache_time')['hour']);
        }

        // Get trending stories
        $trendingStoriesCacheKey = Constants::CACHE_HOME_TRENDING_STORY;
        if (($trendingStories = $cacheService->get($trendingStoriesCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceTrendingStories = $em->getRepository(PostPublishes::class)
                ->getFocusPosts(Constants::FOCUS_STATUS_TRENDING_STORY, Constants::HOME_TRENDING_STORY_LIMIT, time());
            if ($sourceTrendingStories) {
                $trendingStories = $exchangeService->exchangeArrayArticle($sourceTrendingStories);
            } else {
                $trendingStories = [];
            }
            $cacheService->set($trendingStoriesCacheKey, $trendingStories, $this->getParameter('cache_time')['hour']);
        }

        // Get review posts
        $reviewCacheKey = Constants::CACHE_HOME_REVIEWS;
        if (($reviewPosts = $cacheService->get($reviewCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceReviewPosts = $em->getRepository(PostPublishes::class)
                ->getTopReviewPosts(Constants::REVIEW_CATE_ID, Constants::HOME_REVIEWS_LIMIT);
            if ($sourceReviewPosts) {
                $reviewPosts = $exchangeService->exchangeReviewPosts(
                    $sourceReviewPosts,
                    Constants::IMAGE_CATEGORY_FEATURED_STORIES_SIZE
                );
            } else {
                $reviewPosts = [];
            }
            $cacheService->set($reviewCacheKey, $reviewPosts, $this->getParameter('cache_time')['hour']);
        }

        // Get top hot videos
        $hotVideoCacheKey = Constants::CACHE_HOME_HOT_VIDEO;
        if (($hotVideos = $cacheService->get($hotVideoCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            $sourceHotVideos = $em->getRepository(Videos::class)->getTopHotVideos(Constants::HOME_HOT_VIDEO_LIMIT);
            if ($sourceHotVideos) {
                $hotVideos = $exchangeService->exchangeVideos($sourceHotVideos);
            } else {
                $hotVideos = [];
            }
            $cacheService->set($hotVideoCacheKey, $hotVideos, $this->getParameter('cache_time')['hour']);
        }

        $homeUrl = $this->generateUrl('index');
        $seo = $this->buildPagingMeta($homeUrl, $this->getParameter('home_title'), $this->getParameter('site_desc'));
        $seo['is_home'] = true;
        /*$seo = array(
            'title' => $this->getParameter('home_title'),
            'description' => $this->getParameter('site_desc'),
            'og_type' => 'object',
            'is_home' => true
            //'rssHome' => $this->generateUrl('rss', ['map' => 'feed'])
        );*/

        $response = $this->render('default/index.html.twig', [
            'featuredPosts' => $featuredPosts,
            'firstHomeStreamPosts' => $homeStreamPost['firstHomeStreamPosts'],
            'secondHomeStreamPosts' => $homeStreamPost['secondHomeStreamPosts'],
            'firstFeaturedStory' => $homeStreamPost['firstFeaturedStory'],
            'secondFeaturedStory' => $homeStreamPost['secondFeaturedStory'],
            'positionInject' => Constants::HOME_FEATURED_POSITION_INJECT,
            'loadMoreToken' => $homeStreamPost['loadMoreToken'],
            'boxLinks' => $boxLinks,
            'trendingTags' => $trendingTags,
            'gameTags' => $gameTags,
            'trendingStories' => $trendingStories,
            'reviewPosts' => $reviewPosts,
            'hotVideos' => $hotVideos,
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
        $response = $this->render('404.html.twig', array('seo' => $seo));
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
