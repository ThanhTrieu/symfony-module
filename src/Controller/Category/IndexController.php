<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 9:18 AM
 */

namespace App\Controller\Category;

use App\Controller\BaseController;
use App\Entity\PostPublishes;
use App\Service\Category;
use App\Service\CryptUtils;
use App\Service\DataExchange;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends BaseController
{
    /**
     * list data category
     * author: AnhPT4
     * date: 2018-53-21 17:53 PM
     * @param DataExchange $dataExchangeService
     * @param Category $cateService
     * @param Request $request
     * @param CryptUtils $cryptUtils
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(DataExchange $dataExchangeService, Category $cateService, Request $request, CryptUtils $cryptUtils)
    {
        // Process category info
        $rootSlug = $request->get('rootSlug');
        $parentSlug = strtolower($request->get('parentSlug'));
        $cateSlug = strtolower($request->get('cateSlug'));
        $cate = $cateService->getCateBySlug($cateSlug);

        if ($cate == null) {
            return $this->forward('App\Controller\DefaultController::_404page');
        }

        // Cate parent slug is invalid
        if ($cate['parentId'] != 0) {
            $parentCate = $cateService->getCateById($cate['parentId']);
            if ($parentCate != null && $parentCate['slug'] != $parentSlug) {
                return $this->redirectToRoute('sub_cate', ['parentSlug' => $parentCate['slug'], 'cateSlug' => $cateSlug]);
                /*if ($currentPage == 1) {
                    return $this->redirectToRoute('sub_cate', ['parentSlug' => $parentCate['slug'], 'cateSlug' => $cateSlug]);
                } else {
                    return $this->redirectToRoute('sub_cate_paging', ['parentSlug' => $parentCate['slug'], 'cateSlug' => $cateSlug, 'currentPage' => $currentPage]);
                }*/
            }
        }

        $cateId = $cate['cateId'];
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        // Get focus posts in category
        $focusPostsCacheKey = sprintf(Constants::CACHE_FOCUS_CATE, $cateId);
        if (($focusPosts = $cacheService->get($focusPostsCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            // data CATEGORY_FEATURED_STORIES
            $dataFocusPosts = $em->getRepository(PostPublishes::class)->getPostFeaturedInCategory($cateId, Constants::LIMIT_FEATURED, [Constants::FOCUS_HOME, Constants::FOCUS_CATEGORY]);
            if ($dataFocusPosts) {
                $focusPosts = $dataExchangeService->ExchangeArrayArticle($dataFocusPosts, Constants::POST_AVATAR_LIST_SIZE, Constants::FIRST_TOP_FEATURED_SIZE, Constants::CUT_NUMBER_CHARACTERS_FEATURED_TOP_TITLE, Constants::CUT_NUMBER_CHARACTERS_FEATURED_TOP_SUMMARY);
            } else {
                $focusPosts = [];
            }

            $cacheService->set($focusPostsCacheKey, $focusPosts, $this->getParameter('cache_time')['hour']);
        }
        // check the same article
        $ArrayPostIds = [];
        if (!empty($focusPosts)) {
            foreach ($focusPosts as $key) {
                $ArrayPostIds[] = $key['id'];
            }
        }

        $catePostsKeyCache = sprintf(Constants::CACHE_CATEGORY_LASTEST_PAGE, $cateId);
        $catePostInfo = $cacheService->get($catePostsKeyCache);
        if ($catePostInfo === false) {
            $em = $this->getDoctrine()->getManager();
            // data list
            if ($cate['parentId'] == 0) {
                $cateIdList = $cateService->getCategoryParentId($cate['cateId']);
            } else {
                $cateIdList = $cateId;
            }
            $sourcePosts = $em->getRepository(PostPublishes::class)->getCatePosts($cateIdList, Constants::PAGE_SIZE + 1, time(), $ArrayPostIds);
            if ($sourcePosts) {
                if (count($sourcePosts) > Constants::PAGE_SIZE) {
                    $lastHomeStream = $sourcePosts[Constants::PAGE_SIZE - 1];
                    $sourcePosts = array_slice($sourcePosts, 0, Constants::PAGE_SIZE);
                    $lastInfo = [
                        'cateId' => $cateId,
                        'lastPostId' => $lastHomeStream['postId'],
                        'lastPublishedTimestamp' => $lastHomeStream['publishedTimestamp'],
                        'nextPage' => Constants::START_PAGE + 1
                    ];
                } else {
                    $lastInfo = null;
                }
                $sourcePosts = $dataExchangeService->ExchangeArrayArticle($sourcePosts, Constants::POST_AVATAR_LIST_SIZE, null, Constants::CUT_NUMBER_CHARACTERS_DEFAULT, Constants::CUT_NUMBER_CHARACTERS_LIST_SUMMARY);
                $catePostInfo = [
                    'catePosts' => $sourcePosts,
                    'loadMoreToken' => $this->encrypt($cryptUtils, $lastInfo)
                ];
            } else {
                $catePostInfo = [
                    'catePosts' => [],
                    'loadMoreToken' => null
                ];
            }
            $cacheService->set($catePostsKeyCache, $catePostInfo, $this->getParameter('cache_time')['hour']);
        }

        // Get featured stories
        $featuredStories = $this->getFeaturedStories($dataExchangeService);
//        $featuredStoriesCacheKey = Constants::CACHE_FEATURED_STORY;
//        if (($featuredStories = $cacheService->get($featuredStoriesCacheKey)) === false) {
//            $em = $this->getDoctrine()->getManager();
//            // data CATEGORY_FEATURED_STORIES
//            $dataFeaturedStories =  $em->getRepository(PostPublishes::class)->getPostFeaturedInCategory($cateId,Constants::STATUS_FEATURED_STORIES,Constants::LIMIT_FEATURED_STORIES);
//            if ($dataFeaturedStories) {
//                $featuredStories = $dataExchangeService->ExchangeArrayArticle($dataFeaturedStories,Constants::IMAGE_CATEGORY_FEATURED_STORIES_SIZE);
//            } else {
//                $featuredStories = [];
//            }
//
//            $cacheService->set($featuredStoriesCacheKey, $featuredStories, $this->getParameter('cache_time')['hour']);
//        }

        // Build Seo
        if (!empty($parentSlug)) {
            if (!empty($rootSlug)) {
                $cateUrl = $this->generateUrl('sub_cate_level2', ['rootSlug' => $rootSlug, 'parentSlug' => $parentSlug, 'cateSlug' => $cateSlug]);
            } else {
                $cateUrl = $this->generateUrl('sub_cate', ['parentSlug' => $parentSlug, 'cateSlug' => $cateSlug]);
            }
        } else {
            $cateUrl = $this->generateUrl('cate', ['cateSlug' => $cateSlug]);
        }
        $cateSeoTitle = empty($cate['seoTitle']) ? $cate['name'] : $cate['seoTitle'];
        $cateSeoDesc = $cate['seoDescription'];
        $rssUrl = $cateUrl . '/feed';
        $seo = $this->buildPagingMeta($cateUrl, $cateSeoTitle, $cateSeoDesc, $rssUrl);
//        $seo = array_merge($seo, array(
//            'og_type' => 'object',
//            'is_home' => false,
//            'description' => !empty( $cate['seoDescription'] ) ? $cate['seoDescription'] : $this->getParameter('site_desc')
//        ));
        // build view
        $response = $this->render('category/index.html.twig', array(
            //'currentPage' => $currentPage + 1,
            'catePosts' => $catePostInfo['catePosts'],
            'listFeatured' => $focusPosts,
            'featuredStories' => $featuredStories,
            'cate_name' => $cate['name'],
            'cate_id' => $cate['cateId'],
            //'url_ajax' => $this->generateUrl('ajax_cate'),
            'loadMoreToken' => $catePostInfo['loadMoreToken'],
            'parentSlug' => $cateSlug,
            'seo' => $seo,
            'cateSlug' => empty($parentSlug) ? $cateSlug : $parentSlug // Cate slug to active on menu
        ));
        // ThanhDT: Set cache page
        $this->addCachePage($request, $response);

        return $response;
    }
}