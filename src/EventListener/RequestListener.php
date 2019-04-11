<?php

namespace App\EventListener;

use App\Utils\CacheProvider;
use App\Utils\Constants;
use App\Utils\Lib;
use \Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class RequestListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }
        $request = $event->getRequest();
        $url = $request->getRequestUri();
        if (!Lib::checkValidPageUrl($url)) {
            return;
        }

        $cacheConfig = $this->container->getParameter('cache_config');
        // If don't allow cache page
        if ($cacheConfig['allow_cache_page'] !== 1) {
            return;
        }
        // Get cache page
        $serverCache = CacheProvider::createInstance(
            $request,
            Constants::SERVER_CACHE_FULL_PAGE,
            $cacheConfig,
            Constants::SERVER_CACHE_ARTICLE_TYPE
        );

        $cachekey = $url;
        $htmlContent = $serverCache->getString($cachekey);
        if ($htmlContent) {
            $response = new Response($htmlContent);
            $response->headers->set('Content-Encoding', 'gzip');
            $response->headers->set('X-Cache-Page', '1');
            $event->setResponse($response);
        }
    }
}
