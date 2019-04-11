<?php

namespace App\Service;

use App\Utils\Lib;
use App\Utils\Constants;
use Symfony\Component\Routing\Router;
use App\Entity\PostPublishes;
use Doctrine\Common\Persistence\ManagerRegistry;

class Sitemap
{
    /**
     * @var Router
     */
    private $router;
    /**
     * @var Images
     */
    private $imageService;
    private $baseUrl;
    private $siteName;
    private $em;

    /**
     * Sitemap constructor.
     *
     * @param      Router  $router
     * @param      string  $baseUrl       The base url
     * @param      string  $siteName      The site name
     * @param      Images  $imageService
     * @param      ManagerRegistry $doctrine
     */
    public function __construct(Router $router, $baseUrl, $siteName, Images $imageService, ManagerRegistry $doctrine)
    {
        $this->router = $router;
        $this->imageService = $imageService;
        $this->baseUrl = $baseUrl;
        $this->siteName = $siteName;
        $this->em = $doctrine->getManager();
    }

    public function buildIndex($startDate)
    {
        $output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $begin = $startDate;
        $begin->modify('first day of this month');
        $begin->setTime(0, 0, 0);

        $end = new \DateTime('now', $startDate->getTimeZone());

        $interval = new \DateInterval('P1M');
        $daterange = new \DatePeriod($begin, $interval, $end);
        $output .= "\t"
            . '<sitemap>'
                . '<loc>' . htmlspecialchars(Lib::canonicalUrl($this->baseUrl, 'sitemaps/category.xml')) . '</loc>'
                . '<lastmod>' . Lib::fomartMetaDate($end) . '</lastmod>'
            . '</sitemap>' . "\n";

        $output .= "\t"
            . '<sitemap>'
                . '<loc>' . htmlspecialchars(Lib::canonicalUrl($this->baseUrl, 'sitemaps/tag.xml')) . '</loc>'
                . '<lastmod>' . Lib::fomartMetaDate($end) . '</lastmod>'
            . '</sitemap>' . "\n";

        foreach ($daterange as $date) {
            $output .= $this->buildMonthSitemap($date);
        }

        $output .= '</sitemapindex>';

        return $output;
    }

    public function buildMonthSitemap($startDate)
    {
        $startDate->modify('first day of this month');
        $startDate->setTime(0, 0, 0);
        $currentMonth = date('m');
        if ($startDate->format("m") == $currentMonth) {
            $endDate = new \DateTime('now');
        } else {
            $endDate = clone $startDate;
            $endDate->modify('last day of this month');
            $endDate->setTime(23, 59, 59);
        }

        $postCount = $this->em->getRepository(PostPublishes::class)->countPostsBetweenDates($startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s'));
        if (!$postCount) {
            return '';
        }
        return "\t"
            . '<sitemap>'
            . '<loc>' . htmlspecialchars(Lib::canonicalUrl($this->baseUrl, 'sitemaps/article-'. $startDate->format("Ym")) .'.xml') . '</loc>'
            . '<lastmod>' . Lib::fomartMetaDate($endDate) . '</lastmod>'
            . '</sitemap>' . "\n";
    }

    public function buildCateogriesIndex($categories)
    {
        $output = '<?xml version="1.0" encoding="UTF-8"?>'
        . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
        . ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $date = new \DateTime('now');

        foreach ($categories as $category) {
            $output .= "\t"
            . '<url>'
                . '<loc>' . htmlspecialchars(Lib::canonicalUrl($this->baseUrl, $this->router->generate('cate', ['cateSlug' => $category['slug']]))) . '</loc>'
                . '<lastmod>' . Lib::fomartMetaDate($date) . '</lastmod>'
                . '<changefreq>hourly</changefreq>'
                . '<priority>1</priority>'
            . '</url>' . "\n";
        }

        $output .= '</urlset>';

        return $output;
    }

    public function buildTagsIndex($totalTag)
    {
        $output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $page = 1;
        $date = new \DateTime('now');
        do {
            $totalTag -= Constants::SITEMAP_TAG_PERPAGE;
            $output .= "\t"
            . '<sitemap>'
                . '<loc>' . htmlspecialchars(Lib::canonicalUrl($this->baseUrl, 'sitemaps/tag-'.$page.'.xml')) . '</loc>'
                . '<lastmod>' . Lib::fomartMetaDate($date) . '</lastmod>'
            . '</sitemap>' . "\n";

            $page++;
        } while ($totalTag > 0);
        $output .= '</sitemapindex>';

        return $output;
    }

    public function buildTagsSitemap($tags)
    {
        $output = '<?xml version="1.0" encoding="UTF-8"?>'
        . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
        . ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $date = new \DateTime('now');

        foreach ($tags as $tag) {
            $output .= "\t"
            . '<url>'
                . '<loc>' . htmlspecialchars(Lib::canonicalUrl($this->baseUrl, $this->router->generate('tag', ['tagSlug' => $tag['slug']]))) . '</loc>'
                . '<lastmod>' . Lib::fomartMetaDate($date) . '</lastmod>'
                . '<changefreq>hourly</changefreq>'
                . '<priority>1</priority>'
            . '</url>' . "\n";
        }

        $output .= '</urlset>';

        return $output;
    }

    public function buildArticlesIndex($posts)
    {
        $output = '<?xml version="1.0" encoding="utf-8"?>' . "\n"
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
        . ' xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"'
        . ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        $date = new \DateTime('now');
        foreach ($posts as $post) {
            $output .= "\t"
                . '<url>'
                    . '<loc>' . htmlspecialchars(Lib::canonicalUrl($this->baseUrl, $this->router->generate('post_detail', ['cateSlug'=>$post['slugCate'],'slug' => $post['slug'], 'postId' => $post['postId']]))) . '</loc>'
                    . '<image:image>'
                        . '<image:loc>'. $this->imageService->getImageSize($post['avatar'], Constants::POST_AVATAR_LIST_SIZE) .'</image:loc>'
                        . '<image:caption>'. htmlspecialchars($post['title']) .'</image:caption>'
                    . '</image:image>'
                    . '<lastmod>' . Lib::fomartMetaDate($date) . '</lastmod>'
                    . '<changefreq>daily</changefreq>'
                    . '<priority>0.4</priority>'
                    . '<news:news>'
                        . '<news:publication>'
                            . '<news:name>'. $this->siteName .'</news:name>'
                            . '<news:language>en</news:language>'
                        . '</news:publication>'
                        . '<news:genres/>'
                        . '<news:publication_date>' . Lib::fomartMetaDate($post['publishedDate']) . '</news:publication_date>'
                        . '<news:title>'. htmlspecialchars($post['title']) .'</news:title>'
                    . '</news:news>'

                . '</url>' . "\n";
        }

        $output .= '</urlset>';

        return $output;
    }
}