<?php
/**
 * Created by PhpStorm.
 * User: HaiHS
 * Date: 5/22/2018
 * Time: 9:44 AM
 */

namespace App\Controller;

use App\Entity\PostPublishes;
use App\Entity\Tags;
use App\Service\Category;
use App\Service\DataExchange;
use App\Utils\Constants;
use App\Utils\Lib;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RssController extends BaseController
{
    /**
     * Content: RSS get limit = 18 latest news
     * author : ThangPD
     * date : 2019-46-4 15:46 PM
     * @param string $map
     * @param DataExchange $dataExchange
     * @return Response
     * @throws \Exception
     */
    public function rss($map = "", DataExchange $dataExchange)
    {
        //date_default_timezone_set("Asia/Ho_Chi_Minh");
        $service_cache = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $keyRssFeed = Constants::TABLE_RSS_FEED;
        if (($content = $service_cache->get($keyRssFeed)) === false) {
            $data['title'] = $this->getParameter('site_name');
            $data['link'] = $this->getParameter('domain');
            $data['desc'] = $this->getParameter('site_desc');
            $data['image'] = $this->getParameter('site_logo');
            $data['BuildDate'] = date('D, d M Y H:i:s O');

            $em = $this->getDoctrine()->getManager();
            $latestNews = $em->getRepository(PostPublishes::class)->getArticleLatest(Constants::FEED_RSS_LIMIT);
            if ($latestNews) {
                $data['items'] = $dataExchange->ExchangeArticleRss($latestNews);
            }

            $response = $this->render('default/rss.xml.twig', array(
                'data' => $data
            ));
            $content = Lib::gzip($response->getContent());
            $service_cache->set($keyRssFeed, $content, $this->getParameter('cache_time')['hour']);
        }

        $response = new Response($content);
        $response->headers->set('Content-Type', 'xml');
        $response->headers->set('Content-Encoding', 'gzip');

        return $response;
    }

    /**
     * Content: Cate RSS
     * author : ThangPD
     * date : 2019-46-4 15:46 PM
     * @param string $rootSlug
     * @param string $parentSlug
     * @param string $cateSlug
     * @param DataExchange $dataExchange
     * @param Category $cateService
     * @return Response
     * @throws \Exception
     */
    public function cateRss($rootSlug = "", $parentSlug = "", $cateSlug = "", DataExchange $dataExchange, Category $cateService)
    {
        $service_cache = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);

        $key_rss_cate_feed = sprintf(Constants::TABLE_RSS_CATE_FEED, $parentSlug, $cateSlug);
        if (($content = $service_cache->get($key_rss_cate_feed)) === false) {
            if (!empty($parentSlug)) {
                if (!empty($rootSlug)) {
                    $cateUrl = $this->generateUrl('sub_cate_level2', ['rootSlug' => $rootSlug, 'parentSlug' => $parentSlug, 'cateSlug' => $cateSlug]);
                } else {
                    $cateUrl = $this->generateUrl('sub_cate', ['parentSlug' => $parentSlug, 'cateSlug' => $cateSlug]);
                }
            } else {
                $cateUrl = $this->generateUrl('cate', ['cateSlug' => $cateSlug]);
            }

            $cate = $cateService->getCateBySlug($cateSlug);
            if ($cate == null) {
                return $this->forward('App\Controller\DefaultController::_404page');
            }

            if ($cate['parentId'] == 0) {
                $cateIdList = $cateService->getCategoryParentId($cate['cateId']);
            } else {
                $cateIdList = $cate['cateId'];
            }

            $data['title'] = $cate['name'] . ' &#8211; ' . $this->getParameter('site_name');
            $data['link'] = $this->getParameter('domain');
            $data['atomLink'] = $this->getParameter('domain') . $cateUrl;
            $data['desc'] = $this->getParameter('site_desc');
            $data['image'] = $this->getParameter('site_logo');
            $data['BuildDate'] = date('D, d M y H:i:s O');

            $em = $this->getDoctrine()->getManager();
            $articleList = $em->getRepository(PostPublishes::class)->getArticleInCatePaging($cateIdList, 1, Constants::FEED_RSS_LIMIT);
            if ($articleList) {
                $data['items'] = $dataExchange->ExchangeArticleRss($articleList);
            }
            $response = $this->render('default/rss.xml.twig', array(
                'data' => $data
            ));
            $content = Lib::gzip($response->getContent());
            $service_cache->set($key_rss_cate_feed, $content, $this->getParameter('cache_time')['hour']);
        }

        $response = new Response($content);
        $response->headers->set('Content-Type', 'xml');
        $response->headers->set('Content-Encoding', 'gzip');
        return $response;
    }

    /**
     * Content: RSS tags
     * author : ThangPD
     * date : 2019-28-5 9:28 AM
     * @param string $tagSlug
     * @param DataExchange $dataExchange
     * @return Response
     * @throws \Exception
     */
    public function tagsRss($tagSlug = "", DataExchange $dataExchange)
    {
        $service_cache = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $keyRssTagsFeed = sprintf(Constants::TABLE_RSS_TAGS_FEED, $tagSlug);
        if (($content = $service_cache->get($keyRssTagsFeed)) === false) {
            $em = $this->getDoctrine()->getManager();
            //$tagService = $this->get(Tag::TAG_CACHE_NAME);
            $tags = $em->getRepository(Tags::class)->getTagBySlug($tagSlug);
            if ($tags == null) {
                return $this->forward('App\Controller\DefaultController::_404page');
            }

            $data['title'] = $tags['name'] . ' &#8211; ' . $this->getParameter('site_name');
            $data['link'] = $this->getParameter('domain');
            $data['atomLink'] = $this->getParameter('domain') . $this->generateUrl('tag', ['tagSlug' => $tagSlug]);;
            $data['desc'] = $this->getParameter('site_desc');
            $data['image'] = $this->getParameter('site_logo');
            $data['BuildDate'] = date('D, d M y H:i:s O');

            $tagsData = $em->getRepository(PostPublishes::class)->getArticleInTagRss($tags['tagId'], Constants::FEED_RSS_LIMIT);
            if ($tagsData) {
                $data['items'] = $dataExchange->ExchangeArticleRss($tagsData);
            }
            $response = $this->render('default/rss.xml.twig', array(
                'data' => $data
            ));
            $content = Lib::gzip($response->getContent());
            $service_cache->set($keyRssTagsFeed, $content, $this->getParameter('cache_time')['hour']);
        }

        $response = new Response($content);
        $response->headers->set('Content-Type', 'xml');
        $response->headers->set('Content-Encoding', 'gzip');
        return $response;
    }

    /**
     * Feed news detail
     * author : ThangPD
     * date : 2019-28-5 9:28 AM
     * @param $slug
     * @param $postId
     * @param Request $request
     * @param DataExchange $dataExchange
     * @return Response
     * @throws \Exception
     */
    public function detailFeed($slug, $postId, Request $request, DataExchange $dataExchange)
    {
        $serviceCache = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $keyDetail = sprintf(Constants::TABLE_ARTICLE_DETAIL_BY_SLUG, $slug);
        if (($data = $serviceCache->get($keyDetail)) === false) {
            $em = $this->getDoctrine()->getManager();
            $detail = $em->getRepository(PostPublishes::class)->getDetailBySlug($slug);
            if ($detail == null) {
                return $this->forward('App\Controller\DefaultController::_404page');
            }
            if ($detail) {
                $linkDetail = $this->getParameter('domain') . $this->generateUrl('post_detail', ['cateSlug' => $detail['cateSlug'],'slug' => $slug, 'postId' => $postId], true);
                $data['title'] = 'Comments on: ' . $detail['title'];
                $data['link'] = $linkDetail;
                $data['atomLink'] = $linkDetail;
                $data['BuildDate'] = date('D, d M y H:i:s O');
                $data['desc'] = $this->getParameter('site_desc');
                $response = $this->render('default/rss.xml.twig', array(
                    'data' => $data,
                    'domain' => $linkDetail
                ));
                $content = Lib::gzip($response->getContent());
                $serviceCache->set($keyDetail, $data, $this->getParameter('cache_time')['hour']);
            }
        }
        $response = new Response($content);
        $response->headers->set('Content-Type', 'xml');
        $response->headers->set('Content-Encoding', 'gzip');
        return $response;
    }

}