<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/21/2019
 * Time: 9:30 AM
 */

namespace Mobile\Controller\Post;

use Mobile\Controller\BaseController;
use App\Entity\PostPublishes;
use App\Service\Category;
use App\Service\CryptUtils;
use App\Service\DataExchange;
use App\Service\ElasticSearch;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AmpController extends BaseController
{
    /**
     * @param $cateSlug
     * @param $slug
     * @param $postId
     * @param DataExchange $dataExchange
     * @param Category $category
     * @param ElasticSearch $elasticSearch
     * @param CryptUtils $cryptUtils
     * @return Response|void
     * @throws \Exception
     */
    public function detail(
        $cateSlug,
        $slug,
        $postId,
        DataExchange $dataExchange,
        Category $category,
        Elasticsearch $elasticSearch,
        CryptUtils $cryptUtils
    ) {
        $post = $this->getCachePostInfo($slug, $postId, $dataExchange);
        if (!$post) {
            $categories = $category->getCategoriesKeyId();
            //return $this->createNotFoundWithContent($slug, $elasticSearch, $categories, $dataExchange, $cryptUtils);
            return $this->postNotFoundAction();
        }
        $relatedPosts = !empty($post['relatedPosts']) ? $this->getCacheRelatedPosts($post['relatedPosts'], $dataExchange) : [];
        $relatedArticles = $this->getCacheRelatedArticles($post['cateId'], $dataExchange);
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

        return $this->render('posts/detail-amp.html.twig', [
            'post' => $post,
            'seo' => $post['seo'],
            'title' => $post['title'],
            'relatedPosts' => $relatedPosts,
            'relatedArticles' => $relatedArticles,
            'cateSlug' => $cateSlug, // Cate slug to active on menu
            'postId' => $postId,
            'urlAmp' => $this->getParameter('mobile').$this->generateUrl('post_amp_detail',['cateSlug' => $cateSlug, 'slug' => $slug, 'postId' => $postId])
        ]);
    }

    /**
     * show 404 page
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
    ) {
        $keyword = preg_replace('/[-_]/m', ' ', $slug);
        $keyword = filter_var($keyword, FILTER_SANITIZE_STRING);
        $keyword = (mb_strlen($keyword) > 100) ? substr($keyword, 0, 100) : $keyword;

        $limit = Constants::SEARCH_POST_LIMIT;
        $queryData = '{"multi_match" : {"query" : "'.$keyword.'", "fields": ["title", "sapo"] }}';
        $searchData = $elasticSearch->search(Elasticsearch::INDIA_POSTS_INDEX, $queryData, 0, $limit, $total, $error);

        $posts = $exchangeService->exchangeArraySearchPost(
            $searchData,
            Constants::M_HOME_AVATAR_LIST_SIZE,
            $categories,
            null,
            null
        );

        $loadMoreToken = null;
        if( $posts ) {
            if ($total && $total > $limit) {
                $loadMoreToken = [
                    'keyword' => $keyword,
                    'total' => $total,
                    'page' => 1
                ];
                $loadMoreToken = $cryptUtils->encrypt(json_encode($loadMoreToken));
            }
        }

        $viewParams = [
            'seo' => [
                'title' => 'Search ' . $keyword
            ],
            'keyword' => $keyword,
            'posts' => $posts,
            'loadMoreToken' => $loadMoreToken,
            'totalPosts' => $total,
            'cateSlug' => 'Home'
        ];
        return $this->render('default/search.html.twig', $viewParams);
    }

    /**
     * Get cache detail post
     * @param string $slug
     * @param int $postId
     * @param DataExchange $dataExchange
     * @return array|bool|mixed|string
     * @throws \Exception
     */
    public function getCachePostInfo($slug = "", $postId = 0, DataExchange $dataExchange)
    {
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $key_post_id = $this->formatCacheKey(Constants::CACHE_AMP_DETAIL_POST_SLUG_ID, $slug, $postId);
        if (($postExchange = $cacheService->get($key_post_id)) === false) {
            $em     = $this->getDoctrine();
            $post = $em->getRepository(PostPublishes::class)->getDetailPost($postId);
            $postExchange = $dataExchange->exchangeArticleDetail($post);
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
     */
    public function getCacheRelatedPosts($ids = "", $dataExchange){
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $keyRelatedPosts = $this->formatCacheKey(Constants::CACHE_AMP_DETAIL_RELATED_POSTS, $ids);
        if (($postExchange = $cacheService->get($keyRelatedPosts)) === false) {
            $em     = $this->getDoctrine();
            $post = $em->getRepository(PostPublishes::class)->getRelatedPosts($ids);
            $postExchange = $dataExchange->exchangeRelatedArticles($post, 0);
            $cacheService->set($keyRelatedPosts, $postExchange, $this->getParameter('cache_time')['hour']);
        }

        return $postExchange;
    }

    /**
     * get cache Related Articles
     * @param int $cateId
     * @param $dataExchange
     * @return mixed
     * @throws \Exception
     */
    public function getCacheRelatedArticles($cateId = 0, $dataExchange){
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $keyRelatedArticles = $this->formatCacheKey(Constants::CACHE_AMP_DETAIL_RELATED_ARTICLES, $cateId);
        if (($postExchange = $cacheService->get($keyRelatedArticles)) === false) {
            $em   = $this->getDoctrine();
            $post = $em->getRepository(PostPublishes::class)->getRelatedArticles($cateId);
            $postExchange = $dataExchange->exchangeRelatedArticlesMobile($post, 2);
            $cacheService->set($keyRelatedArticles, $postExchange, $this->getParameter('cache_time')['hour']);
        }

        return $postExchange;
    }
}