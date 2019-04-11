<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/25/2019
 * Time: 2:07 PM
 */

namespace Mobile\Controller;


class SitemapController extends BaseController
{
    public function index()
    {
        $seo = array(
            'title' => 'Page not found',
            'og_type' => 'object'
        );
        $response = $this->render('sitemap/index.html.twig', array(
            'seo' => $seo,
            'cateSlug' => 'home'
        ));
        return $response;
    }
}