<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 5/17/2018
 * Time: 1:35 PM
 */

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Lib
{

    public static function gzip($content)
    {
        return gzencode($content, 5);
    }

    public static function gunzip($contentGzip)
    {
        return gzdecode($contentGzip, 5);
    }

    /**
     * Cache encode object
     * @param $value object
     * @return string
     */
    public static function cacheEncode($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Cache decode string
     * @param $value : string
     * @return mixed
     */
    public static function cacheDecode($value)
    {
        return json_decode($value, true);
    }

    /**
     * Check valid page url of music-news
     * author: ThanhDT
     * date:   2017-11-28 01:26 PM
     * @param $url
     * @return bool
     */
    public static function checkValidPageUrl($url)
    {
        if ($url == "/") {
            return true;
        }
        if (strpos($url, '/ajax/') !== false) {
            return false;
        }
        if (strpos($url, '.gif') !== false) {
            return false;
        }
        /*if (strpos($url, '.html') !== false || strpos($url, '.xml') !== false) {
            return true;
        }*/
        return true;
    }

    /**
     * Format datetime to string
     * @param $datetime
     * @return mixed
     */
    public static function formatDate($datetime)
    {
        $strTime = $datetime->format('M d, Y, h:i A') . ' IST';
        return $strTime;
    }

    /**
     * Format datetime to string in meta article:publish
     * author: ThanhDT
     * date:   2018-05-22 09:46 AM
     * @param $datetime
     * @return mixed
     */
    public static function fomartMetaDate($datetime)
    {
        $strTime = $datetime->format('c');
        return $strTime;
    }

    /**
     * Format Date in Rss feed to GMT
     * @param \DateTime $datetime
     * @return string
     */
    public static function fomartRssDate(\DateTime $datetime)
    {
        $strTime = $datetime->setTimezone(new \DateTimeZone('GMT'))->format('D, d M Y H:i:s O');
        return $strTime;
    }

    const UNICODE_ENCODING = 'utf-8';

    /**
     * Substring by word in utf-8 mode
     * author: ThanhDT
     * date:   2017-03-29 9:15 AM
     * @param $str
     * @param $limit
     * @param string $end
     * @return string
     */
    public static function subString($str, $limit, $end = '...')
    {
        $str_new = trim(strip_tags($str));
        // process special content
        $str_new = mb_substr($str_new, 0, $limit + 100, self::UNICODE_ENCODING);
        $str_new = self::removeSpecialContent($str_new);

        if (mb_strlen($str_new, self::UNICODE_ENCODING) > $limit) {
            $str_new = mb_substr($str_new, 0, $limit, self::UNICODE_ENCODING);
            if ($str[$limit] == ' ') {
                return $str_new . $end;
            }
            $pos = mb_strrpos($str_new, ' ', 0, self::UNICODE_ENCODING);
            if ($pos > 0) {
                $str_new = mb_substr($str_new, 0, $pos, self::UNICODE_ENCODING);
            }

            return preg_replace('/\\s{2,}/', ' ', $str_new . $end);
        }

        return preg_replace('/\\s{2,}/', ' ', $str_new);
    }

    /**
     * Remove WP special tag in content
     * @param $str
     * @return mixed
     */
    public static function removeSpecialContent($str)
    {
        $str = preg_replace('/\\[caption[^\\]+](.*)[\\/caption]/i', '', $str);
        return $str;
    }

    public static function buildPagingMeta($title, $baseUrl, $pageIndex, $pageCount, $suffix = '')
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
        $pageUrl = $baseUrl[strlen($baseUrl) - 1] == '/' ? $baseUrl . 'page/' : $baseUrl . '/page/';
        if ($pageIndex != 1) {
            if ($pageIndex != 2) {
                $seo['prev_url'] = $pageUrl . ($pageIndex - 1);
            } else {
                $seo['prev_url'] = $baseUrl;
            }
            $seo['url'] = $pageUrl . $pageIndex;
        } else {
            $seo['url'] = $baseUrl;
        }
        if ($pageCount != 1) {
            if ($pageIndex != $pageCount) {
                $seo['next_url'] = $pageUrl . ($pageIndex + 1);
            }
        }

        return $seo;
    }

    public static function sanitizeOutput($buffer)
    {
        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );
        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );
        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

    /*
     * Store html content of page to cache
     * Author: ThanhDT
     * Date: 2016-01-12 11:32 AM
     * */
    public static function addCachePage(Request $request, Response $response, $serverCache, $cacheTime)
    {
        $key = $request->getRequestUri();
        $gzipContent = Lib::gzip(Lib::sanitizeOutput($response->getContent()));
        //$gzipContent = $response->getContent();
        //$redis = $container->get(Constants::SERVER_CACHE_FULL_PAGE);
        //$redis->setString($key, $gzipContent, $cacheTime);
        $serverCache = CacheProvider::createInstance($request, $serverCache, Constants::SERVER_CACHE_FULL_PAGE, Constants::SERVER_CACHE_ARTICLE_TYPE);
        $serverCache->setString($key, $gzipContent, $cacheTime);
    }

    /**
     * Format point
     * author: ThanhDT
     * date:   2018-12-25 09:25 PM
     * @param $reviewPoints
     * @return string
     */
    public static function formatPoint($points)
    {
        return number_format($points / 10, 1);
    }

    /**
     * generate canonical url
     * @param  string $baseUrl
     * @param  string $url
     * @return string
     */
    public static function canonicalUrl($baseUrl, $url)
    {
        if (preg_match("/^(?:http(s)?:\/\/)/mi", $url)) {
            return $url;
        }
        $url = ltrim($url, '/');
        return rtrim($baseUrl, '/') . '/' . $url;
    }

    const unicode_encoding = 'utf-8';

    /**
     * Substring by word in utf-8 mode
     * author: ThanhDT
     * date:   2017-03-29 9:15 AM
     * @param $str
     * @param $limit
     * @param string $end
     * @return string
     */
    public static function subWords($str, $limit, $end = '..')
    {
        $str_new = trim($str);
        if (mb_strlen($str_new, self::unicode_encoding) > $limit) {
            $str_new = mb_substr($str_new, 0, $limit, self::unicode_encoding);
            if ($str[$limit] == ' ') return $str_new . $end;
            $pos = mb_strrpos($str_new, ' ', 0, self::unicode_encoding);
            if ($pos > 0) {
                $str_new = mb_substr($str_new, 0, $pos, self::unicode_encoding);
            }

            return $str_new . $end;
        }

        return $str_new;
    }

    /**
     * Generate media token
     * author: ThanhDT
     * date:   2019-03-01 04:41 PM
     * @param $mediaPrivateKey
     * @param $url
     * @param $version
     * @return mixed|string
     */
    public static function generateMediaToken($mediaPrivateKey, $url, $version)
    {
        $token = base64_encode(md5($mediaPrivateKey . $url . $version, true));
        $token = str_replace('+', '-', $token);
        $token = str_replace('=', '', $token);
        $token = str_replace('/', '_', $token);

        return $token;
    }
}
