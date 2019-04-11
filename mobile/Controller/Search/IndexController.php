<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 12:43 AM
 */

namespace Mobile\Controller\Search;

use Mobile\Controller\BaseController;
use App\Service\DataExchange;
use App\Service\Category;
use App\Service\CryptUtils;
use App\Service\ElasticSearch;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends BaseController
{
    const LIMIT_KEYWORDS = 100;

    /**
     * Search action
     * @param      Request $request The request
     * @param      ElasticSearch $elasticSearch The elastic search
     * @param      DataExchange $exchangeService The exchange service
     * @param      Category $category The category
     * @param      CryptUtils $cryptUtils The crypt utilities
     * @return     \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function searchAction(
        Request $request,
        ElasticSearch $elasticSearch,
        DataExchange $exchangeService,
        Category $category,
        CryptUtils $cryptUtils
    ) {
        $keyword = $request->get('q');
        $keyword = filter_var($keyword, FILTER_SANITIZE_STRING);
        $keyword = (mb_strlen($keyword) > self::LIMIT_KEYWORDS) ? substr($keyword, 0, self::LIMIT_KEYWORDS) : $keyword;
        $total = 0;
        $error = null;
        $limit = Constants::SEARCH_POST_LIMIT;
        $queryData = '{"multi_match" : {"query" : "'.$keyword.'", "fields": ["title", "sapo"] }}';
        $searchData = $elasticSearch->search(ElasticSearch::INDIA_POSTS_INDEX, $queryData, 0, $limit, $total, $error);

        $categories = $category->getCategoriesKeyId();
        $posts = $exchangeService->exchangeArraySearchPost(
            $searchData,
            Constants::M_HOME_AVATAR_LIST_SIZE,
            $categories,
            null,
            Constants::CUT_NUMBER_CHARACTERS_FEATURED_TOP_TITLE
        );

        $loadMoreToken = ($posts) ? $this->getLoadMoreParams($cryptUtils, $limit, $keyword, $total) : null;

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
     * Gets the load more parameters.
     *
     * @param      CryptUtils  $cryptUtils  The crypt utilities
     * @param      int         $limit       The limit
     * @param      string      $keyword     The keyword
     * @param      int         $total       The total
     *
     * @return     array|bool  The load more parameters.
     */
    protected function getLoadMoreParams(CryptUtils $cryptUtils, $limit, $keyword, $total)
    {
        $token = null;
        if ($total && $total > $limit) {
            $loadMoreToken = [
                'keyword' => $keyword,
                'total' => $total,
                'page' => 1
            ];
            $token = $cryptUtils->encrypt(json_encode($loadMoreToken));
        }
        return $token;
    }
}
