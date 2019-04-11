<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hoandoanviet
 * Date: 11/21/15
 * Time: 9:45 AM
 * To change this template use File | Settings | File Templates.
 */

namespace App\Service;

use App\Utils\Lib;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppExtension extends \Twig_Extension
{
    private $container;

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('format_media_url', array($this, 'formatMediaUrlFilter'), array('is_safe' => array('all'))),
        );
    }

    /*
     * ThanhDT add for common image lib
     * */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Format madia url
     * author: ThanhDT
     * date:   2019-03-01 05:05 PM
     * @param $url
     * @return string
     */
    public function formatMediaUrlFilter($url)
    {
        $version = $this->container->getParameter('static_version');
        $staticFolder = $this->container->getParameter('static_folder');
        $url = $staticFolder . $url;
        $token = Lib::generateMediaToken($this->container->getParameter('static_private_key'), $url, $version);

        $timeExpire = $this->container->getParameter('static_expire');
        $fullUrl = sprintf('%s%s?v=%s&token=%s&ts=%s', $this->container->getParameter('static_url'), $url, $version, $token, $timeExpire);
        return $fullUrl;
    }
}
