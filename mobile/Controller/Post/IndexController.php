<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 9:18 AM
 */

namespace Mobile\Controller\Post;

use App\Service\Images;
use Mobile\Controller\BaseController;
use App\Entity\PostPublishes;
use App\Service\Category;
use App\Service\CryptUtils;
use App\Service\DataExchange;
use App\Service\ElasticSearch;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndexController extends BaseController
{
    /**
     * Post detail action
     * author : ThangPD
     * date : 2019-27-14 10:27 AM
     * @return Response
     */
    public function detail($cateSlug, $slug = "", $postId = 0, DataExchange $dataExchange, Category $category, Elasticsearch $elasticSearch, CryptUtils $cryptUtils)
    {
        $post = $this->getCachePostInfo($slug, $postId, $dataExchange);
        if (!$post || !($cateInfo = $category->getCateById($post['cateId']))) {
            $categories = $category->getCategoriesKeyId();
            return $this->createNotFoundWithContent($slug, $elasticSearch, $categories, $dataExchange, $cryptUtils);
//            return $this->postNotFoundAction();
        }
        // ThanhDT check valid url and redirect 301 if invalid
        if ($cateSlug != $cateInfo['slug'] || $slug != $post['slug']) {
            return $this->redirectToRoute('post_detail', ['cateSlug' => $cateInfo['slug'], 'slug' => $post['slug'], 'postId' => $postId], 301);
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
        return $this->render('posts/detail.html.twig', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'relatedArticles' => $relatedArticles,
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
            Constants::M_HOME_AVATAR_LIST_SIZE,
            $categories,
            null,
            null
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

        $viewParams = [
            'seo' => [
                'title' => 'Search ' . $keyword,
                'og_type' => 'object'
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
     * modifier: ThanhDT - Fix error when used cache
     * modified date:   2019-02-26 01:26 PM
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
        $keyPostId = $this->formatCacheKey(Constants::CACHE_POST_SLUG_ID, $slug, $postId);
        if (($post = $cacheService->get($keyPostId)) === false) {
            $em = $this->getDoctrine();
            $postSource = $em->getRepository(PostPublishes::class)->getDetailPost($postId);
            $post = $dataExchange->exchangeArticleDetail($postSource);
            $cacheService->set($keyPostId, $post, $this->getParameter('cache_time')['hour']);
        }

        return $post;
    }

    /**
     * Get cache related posts
     * modifier: ThanhDT Fix error when used cache
     * modified date:   2019-02-26 01:30 PM
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
        $keyRelatedPosts = $this->formatCacheKey(Constants::CACHE_RELATED_POSTS, $ids);
        if (($relatedPosts = $cacheService->get($keyRelatedPosts)) === false) {
            $em = $this->getDoctrine();
            $postSource = $em->getRepository(PostPublishes::class)->getRelatedPosts($ids);
            $relatedPosts = $dataExchange->exchangeRelatedArticles($postSource, 0);
            $cacheService->set($keyRelatedPosts, $relatedPosts, $this->getParameter('cache_time')['hour']);
        }

        return $relatedPosts;
    }

    /**
     * get cache Related Articles
     * @param int $cateId
     * @param $dataExchange
     * @return mixed
     * @throws \Exception
     * author : ThangPD
     * date : 2018-33-22 14:33 PM
     */
    public function getCacheRelatedArticles($cateId = 0, DataExchange $dataExchange)
    {
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $keyRelatedArticles = $this->formatCacheKey(Constants::CACHE_RELATED_ARTICLES, $cateId);
        if (($relatedPosts = $cacheService->get($keyRelatedArticles)) === false) {
            $em = $this->getDoctrine();
            $postSource = $em->getRepository(PostPublishes::class)->getRelatedArticles($cateId);
            $relatedPosts = $dataExchange->exchangeRelatedArticlesMobile($postSource, 2);
            $cacheService->set($keyRelatedArticles, $relatedPosts, $this->getParameter('cache_time')['hour']);
        }

        return $relatedPosts;
    }

    /**
     * @param int $postId
     * @param DataExchange $dataExchange
     * @param Images $imagesService
     * @return Response
     * @throws \Exception
     */
    public function detailShortAction($postId = 0, DataExchange $dataExchange, Images $imagesService)
    {
        $data = $this->getCachePostInfo('', $postId, $dataExchange);
        if (!$data) {
//            throw $this->createNotFoundException('The news does not exist');
            return $this->forward('Mobile\Controller\DefaultController::_404page');
        }
        $postDetails = [];
        $postDetails['title'] = $data['title'];
        $postDetails['description'] = $data['sapo'];
        $postDetails['url'] = $this->getParameter('mobile') . $data['url'];
        $postDetails['shortUrl'] = $this->getParameter('mobile') . $this->generateUrl('post_detail_short', array('postId' => $postId));
        $postDetails['image'] = $imagesService->getImageSize(isset($data['avatar']) ? $data['avatar'] : '', Constants::SIZE_IMG_LINK_SHARE_POST_DETAIL);
        $response = $this->render('posts/detail-short-news.html.twig', array(
            'post' => $postDetails,
            'postId' => $postId,
            'site_name' => $this->getParameter('site_name'),
            'facebook_app_id' => $this->getParameter('facebook_app_id'),
        ));

        return $response;
    }
}
