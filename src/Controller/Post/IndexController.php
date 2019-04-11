<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 9:18 AM
 */

namespace App\Controller\Post;

use App\Controller\BaseController;
use App\Entity\PostPublishes;
use App\Service\Category;
use App\Service\CryptUtils;
use App\Service\DataExchange;
use App\Service\ElasticSearch;
use App\Service\Images;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndexController extends BaseController
{
    /**
     * Post detail action
     * ThanhDT Test routing
     * author: ThanhDT
     * date:   2018-12-04 09:34 AM
     * @return Response
     */
    public function detail($cateSlug, $slug = "", $postId = 0, DataExchange $dataExchange, Category $category, Elasticsearch $elasticSearch, CryptUtils $cryptUtils)
    {
        $post = $this->getCachePostInfo($slug, $postId, $dataExchange);
        if (!$post || !($cateInfo = $category->getCateById($post['cateId']))) {
            $categories = $category->getCategoriesKeyId();
            return $this->createNotFoundWithContent($slug, $elasticSearch, $categories, $dataExchange, $cryptUtils);
        }
        // ThanhDT check valid url and redirect 301 if invalid
        if ($cateSlug != $cateInfo['slug'] || $slug != $post['slug']) {
            return $this->redirectToRoute('post_detail', ['cateSlug' => $cateInfo['slug'], 'slug' => $post['slug'], 'postId' => $postId], 301);
        }

        $relatedPosts = !empty($post['relatedPosts']) ? $this->getCacheRelatedPosts($post['relatedPosts'], $dataExchange) : [];
        $relatedArticles = $this->getCacheRelatedArticles($post['cateId'], $dataExchange); //!empty($post['cateId']) ? $this->getCacheRelatedArticles($post['cateId'], $dataExchange) : [];
        $featuredStories = $this->getFeaturedStories($dataExchange); //!empty($post['cateId']) ? $this->getCacheFeaturedStories($post['cateId'], $dataExchange) : [];

        $cateInfo = $category->getCateById($post['cateId']);
        if ($cateInfo) {
            $cateSlug = $cateInfo['slug'];
            if ($cateInfo['parentId'] != 0) {
                $parentCate = $category->getCateById($cateInfo['parentId']);
                if ($parentCate) {
                    $cateSlug = $parentCate['slug'];
                }
            }
        } else {
            $cateSlug = '';
        }
        
        return $this->render('posts/detail.html.twig', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'relatedArticles' => $relatedArticles,
            'featuredStories' => $featuredStories,
            'cateSlug' => $cateSlug, // Cate slug to active on menu
            'postId' => $postId,
            'seo' => $post['seo']
        ]);
    }

    /**
     * @param string $slug
     * @param int $postId
     * author : ThangPD
     * date : 2018-34-21 15:34 PM
     */
    public function postNotFoundAction()
    {
        throw new NotFoundHttpException("Page not found");
    }

    public function createNotFoundWithContent(
        string $slug,
        Elasticsearch $elasticSearch,
        $categories,
        DataExchange $exchangeService,
        CryptUtils $cryptUtils
    )
    {
        $keyword = preg_replace('/[-_]/m', ' ', $slug);
        $keyword = filter_var($keyword, FILTER_SANITIZE_STRING);
        $keyword = (mb_strlen($keyword) > 100) ? substr($keyword, 0, 100) : $keyword;

        $limit = Constants::SEARCH_POST_LIMIT;
        $queryData = '{"multi_match" : {"query" : "' . $keyword . '", "fields": ["title", "sapo"] }}';
        $searchData = $elasticSearch->search(Elasticsearch::INDIA_POSTS_INDEX, $queryData, 0, $limit, $total, $error);

        $posts = $exchangeService->exchangeArraySearchPost(
            $searchData,
            Constants::POST_AVATAR_LIST_SIZE,
            $categories,
            Constants::POST_LIST_TITLE_LENGTH,
            Constants::POST_LIST_SAPO_LENGTH
        );

        $loadMoreToken = null;
        if ($posts) {
            if ($total && $total > $limit) {
                $loadMoreToken = [
                    'keyword' => $keyword,
                    'total' => $total,
                    'page' => 1
                ];
                $loadMoreToken = $cryptUtils->encrypt(json_encode($loadMoreToken));
            }
        }
        $featuredStories = $this->getFeaturedStories($exchangeService);

        $viewParams = [
            'seo' => [
                //'site_name' => $this->getParameter('site_name'),
                'title' => 'You are looking for "' . $keyword . '"',
                'og_type' => 'object'
            ],
            'keyword' => $keyword,
            'posts' => $posts,
            'loadMoreToken' => $loadMoreToken,
            'totalPosts' => $total,
            'featuredStories' => $featuredStories,
        ];
        return $this->render('default/search.html.twig', $viewParams);
    }

    /**
     * Get cache detail post
     * @param string $slug
     * @param int $postId
     * @param DataExchange $dataExchange
     * @return array|bool|mixed|string
     * @throws \Exception author : ThangPD
     * date : 2018-32-21 15:32 PM
     */
    public function getCachePostInfo($slug = "", $postId = 0, DataExchange $dataExchange)
    {
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $key_post_id = sprintf(Constants::CACHE_POST_SLUG_ID, $slug, $postId);
        if (($postExchange = $cacheService->get($key_post_id)) === false) {
            $em = $this->getDoctrine();
            $post = $em->getRepository(PostPublishes::class)->getDetailPost($postId);
            $postExchange = $dataExchange->exchangeArticleDetail($post);
            $postExchange['seo']['rss'] = $this->generateUrl('rss_detail_news', ['cateSlug' => $postExchange['cateSlug'],'slug' => $slug, 'postId' => $postId]);
            $cacheService->set($key_post_id, $postExchange, $this->getParameter('cache_time')['hour']);
        }

        return $postExchange;
    }

    /**
     * Get cache related posts
     * @param string $ids
     * @param $dataExchange
     * @return mixed
     * @throws \Exception
     * author : ThangPD
     * date : 2018-39-22 10:39 AM
     */
    public function getCacheRelatedPosts($ids = "", DataExchange $dataExchange)
    {
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $keyRelatedPosts = sprintf(Constants::CACHE_RELATED_POSTS, $ids);
        if (($postExchange = $cacheService->get($keyRelatedPosts)) === false) {
            $em = $this->getDoctrine();
            $post = $em->getRepository(PostPublishes::class)->getRelatedPosts($ids);
            $postExchange = $dataExchange->exchangeRelatedArticles($post, 0);
            $cacheService->set($keyRelatedPosts, $postExchange, $this->getParameter('cache_time')['hour']);
        }

        return $postExchange;
    }

    /**
     * Get cache featured stories
     * @param int $cateId
     * @param $dataExchange
     * @return mixed
     * @throws \Exception
     * author : ThangPD
     * date : 2018-42-22 11:42 AM
     */
    /*public function getCacheFeaturedStories($cateId = 0, $dataExchange){
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $keyFeaturedStories = sprintf(Constants::CACHE_FEATURED_STORIES, $cateId);
        if (($post = $cacheService->get($keyFeaturedStories)) === false) {
            $em     = $this->getDoctrine();
            $post = $em->getRepository(PostPublishes::class)->getFeaturedStories($cateId);
            $postExchange = $dataExchange->exchangeRelatedArticles($post, 1);
            $cacheService->set($keyFeaturedStories, $postExchange, $this->getParameter('cache_time')['hour']);
        }

        return $postExchange;
    }*/

    /**
     * get cache Related Articles
     * @param int $cateId
     * @param $dataExchange
     * @return mixed
     * @throws \Exception
     * author : ThangPD
     * date : 2018-33-22 14:33 PM
     */
    public function getCacheRelatedArticles($cateId = 0, $dataExchange)
    {
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $keyRelatedArticles = sprintf(Constants::CACHE_RELATED_ARTICLES, $cateId);
        if (($postExchange = $cacheService->get($keyRelatedArticles)) === false) {
            $em = $this->getDoctrine();
            $post = $em->getRepository(PostPublishes::class)->getRelatedArticles($cateId);
            $postExchange = $dataExchange->exchangeRelatedArticles($post, 2);
            $cacheService->set($keyRelatedArticles, $postExchange, $this->getParameter('cache_time')['hour']);
        }

        return $postExchange;
    }
    
    /**
     * @author: HaiHS
     * @param $id
     * @param Request $request
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function detailShortAction($postId = 0,DataExchange $dataExchange, Images $imagesService)
    {
        $data = $this->getCachePostInfo('',$postId,$dataExchange);
        if (!$data) {
//            throw $this->createNotFoundException('The news does not exist');
            return $this->forward('App\Controller\DefaultController::_404page');
        }
        $postDetails = [];
        $postDetails['title'] = $data['title'];
        $postDetails['description'] = $data['sapo'];
        $postDetails['url'] = $this->getParameter('domain'). $data['url'];
        $postDetails['shortUrl'] = $this->getParameter('domain'). $this->generateUrl('post_detail_short', array('postId' => $postId));
        $postDetails['image'] = $data['seo']['image'];
        $response = $this->render('posts/detail-short-news.html.twig', array(
            'post' => $postDetails,
            'postId' => $postId,
            'site_name' => $this->getParameter('site_name'),
            'facebook_app_id' => $this->getParameter('facebook_app_id'),
        ));
        
        return $response;
    }
}
