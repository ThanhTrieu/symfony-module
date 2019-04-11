<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/12/2019
 * Time: 10:00 AM
 */

namespace Mobile\Controller;

use App\Entity\PostPublishes;
use App\Service\CryptUtils;
use App\Service\DataExchange;
use App\Utils\CacheProvider;
use App\Utils\Constants;
use App\Utils\Lib;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    /**
     * Get cache provider for all controller
     * author: ThanhDT
     * date:   2018-12-04 09:07 AM
     * @param $serverCache
     * @return \App\Service\Memcache|\App\Service\RedisUtils
     * @throws \Exception
     */
    protected function getCacheProvider($serverCache = Constants::SERVER_CACHE_ARTICLE)
    {
        $cacheService = CacheProvider::createInstance($this->get('request_stack')->getCurrentRequest(), $serverCache, $this->getParameter('cache_config'));
        return $cacheService;
    }

    /**
     * Get Redis cache provider
     * author: ThanhDT
     * date:   2018-12-04 09:07 AM
     * @param $serverCache
     * @return \App\Service\Memcache|\App\Service\RedisUtils
     * @throws \Exception
     */
    protected function getRedisProvider($serverCache = Constants::SERVER_CACHE_ARTICLE)
    {
        $cacheService = CacheProvider::createInstance($this->get('request_stack')->getCurrentRequest(), $serverCache, $this->getParameter('cache_config'), CacheProvider::REDIS);
        return $cacheService;
    }

    /**
     * Add cache page
     * author: ThanhDT
     * date:   2018-12-15 04:44 PM
     * @param $request
     * @param $response
     * @param int $cacheTime
     */
    protected function addCachePage($request, $response, $cacheTime = 0)
    {
        $cacheConfig = $this->getParameter('cache_config');
        if ($cacheConfig['allow_cache_page'] !== 1) {
            return;
        }
        if ($cacheTime == 0) {
            $cacheTime = $this->getParameter('cache_time')['hour'];
        }

        Lib::addCachePage($request, $response, Constants::SERVER_CACHE_FULL_PAGE, $cacheTime);
    }

    /**
     * Build paging meta
     * author: ThanhDT
     * date:   2018-12-19 10:06 AM
     * @param $baseUrl
     * @param $title
     * @param string $desc
     * @param string $rssUrl
     * @param string $ogType
     * @param int $pageIndex
     * @param int $pageCount
     * @param string $suffix
     * @return array
     */
    public function buildPagingMeta($baseUrl, $title, $desc = '', $rssUrl = '', $ogType = 'object', $pageIndex = 1, $pageCount = 0, $suffix = '')
    {
        $seo = [];
        if ($pageIndex == 1) {
            $seo['title'] = $title;
        } else {
            $seo['title'] = sprintf(Constants::TITLE_SEO_PAGING_FORMAT, $title, $pageIndex, $pageCount);
        }
        if (!empty($suffix)) {
            $seo['title'] .= ' - ' . $suffix;
        }
        $seo['description'] = !empty($desc) ? $desc : $this->getParameter('site_desc');
        $seo['og_type'] = $ogType;
        $seo['url'] = $baseUrl;
        $seo['rss'] = $rssUrl;

        return $seo;
    }

    /**
     * Encrypt data
     * author: ThanhDT
     * date:   2018-12-24 11:45 PM
     * @param CryptUtils $cryptUtils
     * @param $arrData : Array data
     * @return string
     */
    protected function encrypt(CryptUtils $cryptUtils, $arrData)
    {
        if ($arrData == null) {
            return null;
        }
        return $cryptUtils->encrypt(json_encode($arrData));
    }

    /**
     * Decrypt data
     * author: ThanhDT
     * date:   2018-12-24 11:45 PM
     * @param CryptUtils $cryptUtils
     * @param $encryptedString
     * @return string
     */
    protected function decrypt(CryptUtils $cryptUtils, $encryptedString)
    {
        return json_decode($cryptUtils->decrypt($encryptedString), true);
    }

    /* Get data common */
    /**
     * Get featured stories
     * author: ThanhDT
     * date:   2018-12-26 11:53 AM
     * @param DataExchange $dataExchangeService
     * @return array|bool|mixed|string
     * @throws \Exception
     */
    public function getFeaturedStories(DataExchange $dataExchangeService)
    {
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $featuredStoriesCacheKey = Constants::CACHE_FEATURED_STORY;
        if (($featuredStories = $cacheService->get($featuredStoriesCacheKey)) === false) {
            $em = $this->getDoctrine()->getManager();
            // data CATEGORY_FEATURED_STORIES
            $dataFeaturedStories = $em->getRepository(PostPublishes::class)->getFeaturedStories(Constants::LIMIT_FEATURED_STORIES);
            if ($dataFeaturedStories) {
                $featuredStories = $dataExchangeService->ExchangeArrayArticle($dataFeaturedStories, Constants::IMAGE_CATEGORY_FEATURED_STORIES_SIZE);
            } else {
                $featuredStories = [];
            }

            $cacheService->set($featuredStoriesCacheKey, $featuredStories, $this->getParameter('cache_time')['hour']);
        }

        return $featuredStories;
    }

    /**
     * Format cache key for mobile
     * @param $format
     * @param null $args
     * @param null $_
     * @return string
     */
    protected function formatCacheKey($format, ...$args)
    {
        if ($args) {
            return Constants::MOBILE . vsprintf($format, $args);
        }

        return Constants::MOBILE . $format;
    }

    /**
     * @param $template
     * @param array $viewParams
     * @return \Symfony\Component\HttpFoundation\Response
     */
    /*protected function customRenderView($template, $viewParams = [])
    {
        $siteName = $this->getParameter('site_name');
        $metaData = [
            'title' => $siteName,
            'siteName' => $siteName,
            'description' => $this->getParameter('site_desc'),
        ];
        if (!empty($viewParams['seo'])) {
            $metaData = array_merge($metaData, $viewParams['seo']);
        }
        if (empty($viewParams['breadcrumbs'])) {
            $viewParams['breadcrumbs'] = [];
        }

        $viewParams['metaData'] = $metaData;
        if (!empty($viewParams['sectionContext'])) {
            $viewParams['sectionContext'] = $viewParams['sectionContext'];
        } else {
            $viewParams['sectionContext'] = 'home';
        }
        return $this->render($template, $viewParams);
    }*/

    /**
     * Returns a json response.
     *
     * @param      array $data The data
     * @param      int $statusCode The status code
     *
     * @return     \Symfony\Component\HttpFoundation\Response
     */
    protected function returnJsonResponse($data = [], $statusCode = 200)
    {
        $response = new Response();
        $response->setContent(json_encode($data));
        $response->setStatusCode($statusCode);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Return xml response
     *
     * @param      string $content The content
     * @param      int $statusCode The status code
     *
     * @return     \Symfony\Component\HttpFoundation\Response
     */
    protected function xmlResponse($content, $statusCode = 200)
    {
        $response = new Response($content);
        $response->headers->set('Content-Type', 'xml');
        $response->headers->set('Content-Encoding', 'gzip');
        $response->setStatusCode($statusCode);
        return $response;
    }
}