<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 9:19 AM
 */

namespace Mobile\Controller\Search;

use Mobile\Controller\BaseController;
use App\Service\DataExchange;
use App\Service\ElasticSearch;
use App\Service\Category;
use App\Service\CryptUtils;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends BaseController
{
    /**
     * Gets the tag posts action.
     *
     * @param      Request        $request          The request
     * @param      ElasticSearch  $elasticSearch    The elastic search
     * @param      DataExchange   $exchangeService  The exchange service
     * @param      Category       $category         The category
     * @param      CryptUtils     $cryptUtils       The crypt utilities
     *
     * @return     \Symfony\Component\HttpFoundation\Response
     */
    public function getSearchPostAction(
        Request $request,
        ElasticSearch $elasticSearch,
        DataExchange $exchangeService,
        Category $category,
        CryptUtils $cryptUtils
    ) {
        $token = $request->get('loadMoreToken', '');
        $tokenInfo = $cryptUtils->decrypt($token);
        if (!$tokenInfo) {
            return $this->returnJsonResponse([
                'success' => 0,
                'html' => '',
                'loadMoreToken' => null,
                'tokenInfo' => null,
            ]);
        }
        $params = json_decode($tokenInfo, true);
        if (!$params || empty($params['keyword']) || empty($params['total']) || empty($params['page'])) {
            return $this->returnJsonResponse([
                'success' => 0,
                'html' => '',
                'loadMoreToken' => null,
                'tokenInfo' => null,
            ]);
        }

        $limit = Constants::SEARCH_POST_LIMIT;
        $start = $params['page'] * $limit;
        $queryData = '{"multi_match" : {"query" : "'.$params['keyword'].'", "fields": ["title", "sapo"] }}';
        $searchData = $elasticSearch->search(
            ElasticSearch::INDIA_POSTS_INDEX,
            $queryData,
            $start,
            $limit,
            $total,
            $error
        );
        $categories = $category->getCategoriesKeyId();
        $posts = $exchangeService->exchangeArraySearchPost(
            $searchData,
            Constants::POST_AVATAR_LIST_SIZE,
            $categories,
            null,
            Constants::CUT_NUMBER_CHARACTERS_FEATURED_TOP_TITLE
        );

        if($posts) {
            $loadMoreToken = $this->getLoadMoreToken($cryptUtils, $posts, $start, $params['page'], $params['keyword'], $total);
            $showViewMore = ($params['page']) % Constants::PAGE_TO_SHOW_VIEW_MORE == 0;
        } else {
            $loadMoreToken = null;
            $showViewMore = false;
        }

        $html = $this->renderView('default/search_ajax_posts.html.twig', [
            'posts' => $posts
        ]);
        return $this->returnJsonResponse([
            'success' => 1,
            'html' => $html,
            'loadMoreToken' => $loadMoreToken,
            'tokenInfo' => $tokenInfo,
            'showViewMore' => $showViewMore
        ]);
    }

    /**
     * Gets the load more token.
     *
     * @param      CryptUtils  $cryptUtils  The crypt utilities
     * @param      array       $posts       The posts
     * @param      int         $start       The start
     * @param      int         $page        The page
     * @param      string      $keyword     The keyword
     * @param      int         $total       The total
     *
     * @return     array|bool  The load more token.
     */
    protected function getLoadMoreToken(CryptUtils $cryptUtils, $posts, $start, $page, $keyword, $total)
    {
        $token = false;
        if ($posts && $total && (count($posts) + $start) < $total) {
            $page++;
            $loadMoreToken = [
                'keyword' => $keyword,
                'total' => $total,
                'page' => $page
            ];
            $token = $cryptUtils->encrypt(json_encode($loadMoreToken));
        }
        return $token;
    }
}
