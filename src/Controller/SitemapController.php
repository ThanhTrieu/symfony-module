<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 12:43 AM
 */

namespace App\Controller;

use App\Entity\PostPublishes;
use App\Entity\Tags;
use App\Service\Category;
use App\Service\Sitemap;
use App\Utils\Constants;
use App\Utils\Lib;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;

class SitemapController extends BaseController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction(Sitemap $sitemap)
    {
        $key = Constants::CACHE_SITEMAP_GLOBAL;
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $sitemapContent = $cacheService->get($key);
        if ($sitemapContent === false) {
            $em = $this->getDoctrine()->getManager();
            //$startDate = $em->getRepository(PostPublishes::class)->getOldestPostDate();
            $startDate = Constants::MIN_DATE_SITE_MAP_V2;
            $startDate = new \DateTime('@'.strtotime($startDate));
            $sitemapContent = $sitemap->buildIndex($startDate);
            $sitemapContent = Lib::gzip($sitemapContent);
            $cacheService->set($key, $sitemapContent, $this->getParameter('cache_time')['hour']);
        }

        return $this->xmlResponse($sitemapContent);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function tagAction($page, Sitemap $sitemap, Category $category)
    {
        $key = sprintf(Constants::CACHE_SITEMAP_TAGS, $page);
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $sitemapContent = $cacheService->get($key);
        if ($sitemapContent === false) {
            $em = $this->getDoctrine()->getManager();
            $tags = $em->getRepository(Tags::class)->getTags($page, Constants::SITEMAP_TAG_PERPAGE);
            $sitemapContent = $sitemap->buildTagsSitemap($tags);
            $sitemapContent = Lib::gzip($sitemapContent);
            $cacheService->set($key, $sitemapContent, $this->getParameter('cache_time')['hour']);
        }

        return $this->xmlResponse($sitemapContent);
    }

    public function tagIndexAction(Sitemap $sitemap, Category $category)
    {
        $key = Constants::CACHE_SITEMAP_TAG_INDEX;
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $sitemapContent = $cacheService->get($key);
        if ($sitemapContent === false) {
            $em = $this->getDoctrine()->getManager();
            $totalTag = $em->getRepository(Tags::class)->getTotalTags();
            $sitemapContent = $sitemap->buildTagsIndex($totalTag);
            $sitemapContent = Lib::gzip($sitemapContent);
            $cacheService->set($key, $sitemapContent, $this->getParameter('cache_time')['hour']);
        }

        return $this->xmlResponse($sitemapContent);
    }

    public function categoryAction(Sitemap $sitemap, Category $category)
    {
        $key = Constants::CACHE_SITEMAP_CATEGORIES;
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $sitemapContent = $cacheService->get($key);
        if ($sitemapContent === false) {
            $categories = $category->getCategoriesKeyId();
            $sitemapContent = $sitemap->buildCateogriesIndex($categories);
            $sitemapContent = Lib::gzip($sitemapContent);
            $cacheService->set($key, $sitemapContent, $this->getParameter('cache_time')['hour']);
        }

        return $this->xmlResponse($sitemapContent);
    }

    /**
     * @param string $dateString
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function articleAction($dateString, Sitemap $sitemap)
    {
        $key = sprintf(Constants::CACHE_SITEMAP_ARTICLES, $dateString);
        $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
        $sitemapContent = $cacheService->get($key);
        if ($sitemapContent === false) {
            $y = substr($dateString, 0, 4);
            $m = substr($dateString, 4, 2);
            //$d = substr($dateString, 6, 2);

            //$startDate = new \DateTime("$y-$m-$d");
            $startDate = new \DateTime("$y-$m");
            $startDate->modify('first day of this month');
            $startDate->setTime(0, 0, 0);
            $endDate = clone $startDate;
            //$endDate = $endDate->modify('+1 day');
            $endDate->modify('last day of this month');
            $endDate->setTime(23, 59, 59);
            $em = $this->getDoctrine()->getManager();
            $posts = $em->getRepository(PostPublishes::class)
                        ->getPostsBetweenDates(
                            $startDate->format('Y-m-d H:i:s'),
                            $endDate->format('Y-m-d H:i:s')
                        );
            $sitemapContent = $sitemap->buildArticlesIndex($posts);
            $sitemapContent = Lib::gzip($sitemapContent);

            $today = new \DateTime('now');
            $today->modify('first day of this month');
            $today->setTime(0, 0, 0);
            $cacheService->set(
                $key,
                $sitemapContent,
                ($startDate->format('m') == date('m')
                    ? 300
                    : $this->getParameter('cache_time')['hour'])
            );
        }

        return $this->xmlResponse($sitemapContent);
    }
}
