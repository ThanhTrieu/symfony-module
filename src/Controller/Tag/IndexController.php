<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 9:18 AM
 */

namespace App\Controller\Tag;

use App\Controller\BaseController;
use App\Entity\PostPublishes;
use App\Entity\SpecialTags;
use App\Entity\Tags;
use App\Service\CryptUtils;
use App\Service\DataExchange;
use App\Utils\Constants;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Count;

class IndexController extends BaseController
{
    /**
     * Get data all Post in by tag
     * author: AnhPT4
     * date: 2018-16-24 15:16 PM
     * @param $tagSlug
     * @param int $currentPage
     * @param DataExchange $dataExchangeService
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index($tagSlug, DataExchange $dataExchangeService, Request $request, CryptUtils $cryptUtils)
    {
        // Process tag info
        $tag = $this->findByTagSlug($tagSlug);
        if ($tag == null) {
//            throw $this->createNotFoundException('The Tag does not exist');
            return $this->forward('App\Controller\DefaultController::_404page');
        }
        $tagId = $tag['tagId'];

        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        // Get post in tag
        $tagPostsKeyCache = sprintf(Constants::CACHE_POST_TAG, $tagId);
        $tagPostInfo = $cacheService->get($tagPostsKeyCache);
        if ($tagPostInfo === false) {
            $em = $this->getDoctrine()->getManager();
            /*// data CATEGORY_FEATURED_STORIES
            $dataFeaturedStories =  $em->getRepository(PostPublishes::class)->getPostFeaturedInTags($tagId,Constants::STATUS_FEATURED_STORIES,Constants::LIMIT_FEATURED_STORIES);
            if ($dataFeaturedStories) {
                $article_exchange['listFeaturedStories'] = $dataExchangeService->ExchangeArrayArticle($dataFeaturedStories,Constants::IMAGE_CATEGORY_FEATURED_STORIES_SIZE);
            } else {
                $article_exchange['listFeaturedStories'] = [];
            }*/

            // Get posts in tag
            $sourcePosts = $em->getRepository(PostPublishes::class)->getTagPosts($tagId, Constants::PAGE_SIZE + 1, time());
            if ($sourcePosts) {
                if (count($sourcePosts) > Constants::PAGE_SIZE) {
                    $lastHomeStream = $sourcePosts[Constants::PAGE_SIZE - 1];
                    $sourcePosts = array_slice($sourcePosts, 0, Constants::PAGE_SIZE);
                    $lastInfo = [
                        'tagId' => $tagId,
                        'lastPostId' => $lastHomeStream['postId'],
                        'lastPublishedTimestamp' => $lastHomeStream['publishedTimestamp'],
                        'nextPage' => Constants::START_PAGE + 1
                    ];
                } else {
                    $lastInfo = null;
                }
                $sourcePosts = $dataExchangeService->ExchangeArrayArticle($sourcePosts, Constants::POST_AVATAR_LIST_SIZE, null, Constants::CUT_NUMBER_CHARACTERS_DEFAULT, Constants::CUT_NUMBER_CHARACTERS_LIST_SUMMARY);
                $tagPostInfo = [
                    'tagPosts' => $sourcePosts,
                    'loadMoreToken' => $this->encrypt($cryptUtils, $lastInfo)
                ];
            } else {
                $tagPostInfo = [
                    'tagPosts' => [],
                    'loadMoreToken' => null
                ];
            }
            $cacheService->set($tagPostsKeyCache, $tagPostInfo, $this->getParameter('cache_time')['hour']);
        }

        // Get featured stories
        $featuredStories = $this->getFeaturedStories($dataExchangeService);

        // Build Seo
        $tagUrl = $this->generateUrl('tag', ['tagSlug' => $tagSlug]);
        $tagRssUrl = $this->generateUrl('rss_tag', ['tagSlug' => $tagSlug]);
        $seo = $this->buildPagingMeta($tagUrl, $tag['name'], $tag['description'], $tagRssUrl);

        if ($tag['isSpecial']) {
            $data = $dataExchangeService->exchangeTagsIsSpecial($tag);
            $data = empty($data) ? null : $data;
            $response = $this->render('tag/special.html.twig', array(
                'tagPosts' => $tagPostInfo['tagPosts'],
                'listFeatured' => null,
                'featuredStories' => $featuredStories,
                'tag_name' => $tag['name'],
                'tag_id' => $tag['tagId'],
                'data' => $data,
                'loadMoreToken' => $tagPostInfo['loadMoreToken'],
                'seo' => $seo
            ));
        } else {
            $response = $this->render('tag/index.html.twig', array(
                'tagPosts' => $tagPostInfo['tagPosts'],
                'listFeatured' => null,
                'featuredStories' => $featuredStories,
                'tag_name' => $tag['name'],
                'total_count' => $tag['postCount'],
                'cate_id' => $tag['tagId'],
                'loadMoreToken' => $tagPostInfo['loadMoreToken'],
                'seo' => $seo
            ));
        }

        return $response;
    }

    /**
     * find By Tag Slug
     * author: AnhPT4
     * date:   2018-10-25 10:02 AM
     * @param $slug
     * @return null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function findByTagSlug($slug)
    {
        $key_tag_all = sprintf(Constants::TABLE_TAG_BY_SLUG, $slug);
        $service_cache = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $tag = $service_cache->get($key_tag_all);
        if ($tag === false) {
            $tag = $this->getDoctrine()->getManager()->getRepository(Tags::class)->getTagBySlug($slug);
            if ($tag['isSpecial']) {
                $tagIsSpecial = $this->getDoctrine()->getManager()->getRepository(SpecialTags::class)->getTagBySlugIsSpecial($tag['tagId']);
                if (!empty($tagIsSpecial))
                    $tag = array_merge($tag, $tagIsSpecial);
            }
            $service_cache->set($key_tag_all, $tag, $this->getParameter('cache_time')['hour']);
        }

        return $tag;
    }
}
